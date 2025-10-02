<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::with(['lead.customer', 'assignedTo']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('lead', function ($leadQuery) use ($search) {
                      $leadQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by lead
        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->lead_id);
        }

        // Filter by due date
        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }
        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tasks = $query->paginate(10)->appends($request->query());

        // Get filter options
        $leads = Lead::orderBy('title')->get();
        $users = User::orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'leads', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $leads = Lead::with('customer')->orderBy('title')->get();
        $users = User::orderBy('name')->get();
        
        // Pre-select lead if provided
        $selectedLead = $request->lead_id ? Lead::find($request->lead_id) : null;
        
        return view('tasks.create', compact('leads', 'users', 'selectedLead'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        Task::create($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
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
        
        return view('tasks.show', compact('task', 'relatedTasks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $leads = Lead::with('customer')->orderBy('title')->get();
        $users = User::orderBy('name')->get();
        
        return view('tasks.edit', compact('task', 'leads', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        // Check if user can update this task (only assigned user or admin/manager)
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->role !== 'manager' && $task->assigned_to !== $user->id) {
            abort(403, 'You can only update tasks assigned to you.');
        }

        $task->update($request->validated());

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Check if user can delete this task (only admin/manager or assigned user)
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->role !== 'manager' && $task->assigned_to !== $user->id) {
            abort(403, 'You can only delete tasks assigned to you.');
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Export tasks to CSV
     */
    public function export(Request $request)
    {
        $query = Task::with(['lead.customer', 'assignedTo']);

        // Apply same filters as index method
        if (auth()->user()->role === 'staff') {
            $query->where('assigned_to', auth()->id());
        }

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

        return response()->stream($callback, 200, $headers);
    }
}
