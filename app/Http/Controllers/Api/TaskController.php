<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of tasks.
     */
    public function index(Request $request)
    {
        $query = Task::with(['lead.customer', 'assignedTo']);

        // Role-based filtering
        if (auth()->user()->role === 'staff') {
            $query->where('assigned_to', auth()->id());
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('lead', function ($leadQuery) use ($search) {
                      $leadQuery->where('title', 'like', "%{$search}%")
                               ->orWhereHas('customer', function ($customerQuery) use ($search) {
                                   $customerQuery->where('name', 'like', "%{$search}%");
                               });
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by assigned user
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by lead
        if ($request->has('lead_id') && $request->lead_id) {
            $query->where('lead_id', $request->lead_id);
        }

        // Filter by due date
        if ($request->has('due_date') && $request->due_date) {
            $query->whereDate('due_date', $request->due_date);
        }

        // Filter overdue tasks
        if ($request->has('overdue') && $request->overdue) {
            $query->where('status', '!=', 'completed')
                  ->where('due_date', '<', now());
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tasks = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'Tasks retrieved successfully'
        ]);
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());
        $task->load(['lead.customer', 'assignedTo']);

        return response()->json([
            'success' => true,
            'data' => $task,
            'message' => 'Task created successfully'
        ], 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $task->load(['lead.customer', 'assignedTo']);

        // Get related tasks for the same lead
        $relatedTasks = Task::with(['assignedTo'])
            ->where('lead_id', $task->lead_id)
            ->where('id', '!=', $task->id)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'task' => $task,
                'related_tasks' => $relatedTasks
            ],
            'message' => 'Task retrieved successfully'
        ]);
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        // Authorization check
        if (auth()->user()->role === 'staff' && $task->assigned_to !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this task'
            ], 403);
        }

        $task->update($request->validated());
        $task->load(['lead.customer', 'assignedTo']);

        return response()->json([
            'success' => true,
            'data' => $task,
            'message' => 'Task updated successfully'
        ]);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        // Authorization check
        if (auth()->user()->role === 'staff' && $task->assigned_to !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this task'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Export tasks to CSV.
     */
    public function export(Request $request)
    {
        $query = Task::with(['lead.customer', 'assignedTo']);

        // Role-based filtering
        if (auth()->user()->role === 'staff') {
            $query->where('assigned_to', auth()->id());
        }

        // Apply same filters as index method
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('lead', function ($leadQuery) use ($search) {
                      $leadQuery->where('title', 'like', "%{$search}%")
                               ->orWhereHas('customer', function ($customerQuery) use ($search) {
                                   $customerQuery->where('name', 'like', "%{$search}%");
                               });
                  });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->has('lead_id') && $request->lead_id) {
            $query->where('lead_id', $request->lead_id);
        }

        // Filter by date range if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        $filename = 'tasks_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tasks) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Title',
                'Description',
                'Status',
                'Lead',
                'Customer',
                'Assigned To',
                'Due Date',
                'Created At',
                'Updated At'
            ]);

            // Add data rows
            foreach ($tasks as $task) {
                fputcsv($file, [
                    $task->id,
                    $task->title,
                    $task->description,
                    ucfirst($task->status),
                    $task->lead ? $task->lead->title : 'N/A',
                    $task->lead && $task->lead->customer ? $task->lead->customer->name : 'N/A',
                    $task->assignedTo ? $task->assignedTo->name : 'N/A',
                    $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A',
                    $task->created_at->format('Y-m-d H:i:s'),
                    $task->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->json([
            'success' => true,
            'message' => 'Task export initiated',
            'download_url' => response()->stream($callback, 200, $headers)
        ]);
    }

    /**
     * Get task statistics.
     */
    public function statistics(Request $request)
    {
        $query = Task::query();
        
        // Role-based filtering for statistics
        if (auth()->user()->role === 'staff') {
            $query->where('assigned_to', auth()->id());
        }

        $stats = [
            'total_tasks' => $query->count(),
            'pending_tasks' => $query->where('status', 'pending')->count(),
            'in_progress_tasks' => $query->where('status', 'in_progress')->count(),
            'completed_tasks' => $query->where('status', 'completed')->count(),
            'overdue_tasks' => $query->where('status', '!=', 'completed')
                ->where('due_date', '<', now())
                ->count(),
            'tasks_this_month' => $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'completed_this_month' => $query->where('status', 'completed')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count(),
            'tasks_by_status' => $query->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Task statistics retrieved successfully'
        ]);
    }

    /**
     * Get tasks by lead.
     */
    public function byLead(Lead $lead)
    {
        $tasks = $lead->tasks()->with(['assignedTo'])->get();

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'Lead tasks retrieved successfully'
        ]);
    }

    /**
     * Get tasks assigned to authenticated user.
     */
    public function myTasks(Request $request)
    {
        $query = Task::with(['lead.customer'])
            ->where('assigned_to', auth()->id());

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by due date
        if ($request->has('due_date') && $request->due_date) {
            $query->whereDate('due_date', $request->due_date);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'due_date');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tasks = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'My tasks retrieved successfully'
        ]);
    }

    /**
     * Update task status.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        // Authorization check
        if (auth()->user()->role === 'staff' && $task->assigned_to !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this task'
            ], 403);
        }

        $task->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'data' => $task,
            'message' => 'Task status updated successfully'
        ]);
    }

    /**
     * Get upcoming tasks.
     */
    public function upcoming(Request $request)
    {
        $query = Task::with(['lead.customer', 'assignedTo'])
            ->where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->orderBy('due_date');

        // Role-based filtering
        if (auth()->user()->role === 'staff') {
            $query->where('assigned_to', auth()->id());
        }

        $limit = $request->get('limit', 10);
        $tasks = $query->take($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'Upcoming tasks retrieved successfully'
        ]);
    }

    /**
     * Get overdue tasks.
     */
    public function overdue(Request $request)
    {
        $query = Task::with(['lead.customer', 'assignedTo'])
            ->where('status', '!=', 'completed')
            ->where('due_date', '<', now())
            ->orderBy('due_date');

        // Role-based filtering
        if (auth()->user()->role === 'staff') {
            $query->where('assigned_to', auth()->id());
        }

        $limit = $request->get('limit', 10);
        $tasks = $query->take($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'Overdue tasks retrieved successfully'
        ]);
    }
}