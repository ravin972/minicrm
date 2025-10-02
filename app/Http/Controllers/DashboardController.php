<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        
        // Get statistics based on user role
        $stats = $this->getStatistics($user);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($user);
        
        // Get chart data
        $chartData = $this->getChartData($user);
        
        // Get upcoming tasks
        $upcomingTasks = $this->getUpcomingTasks($user);
        
        // Get recent leads
        $recentLeads = $this->getRecentLeads($user);

        return view('dashboard.index', compact(
            'stats', 
            'recentActivities', 
            'chartData', 
            'upcomingTasks', 
            'recentLeads'
        ));
    }

    private function getStatistics($user)
    {
        $stats = [];

        if ($user->role === 'admin') {
            // Admin sees all statistics
            $stats = [
                'total_customers' => Customer::count(),
                'total_leads' => Lead::count(),
                'total_tasks' => Task::count(),
                'total_users' => User::count(),
                'new_leads_this_month' => Lead::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'completed_tasks_this_month' => Task::where('status', 'completed')
                    ->whereMonth('updated_at', now()->month)
                    ->whereYear('updated_at', now()->year)
                    ->count(),
                'pending_tasks' => Task::where('status', 'pending')->count(),
                'overdue_tasks' => Task::where('status', '!=', 'completed')
                    ->where('due_date', '<', now())
                    ->count(),
            ];
        } elseif ($user->role === 'manager') {
            // Manager sees team statistics
            $stats = [
                'total_customers' => Customer::count(),
                'total_leads' => Lead::count(),
                'total_tasks' => Task::count(),
                'my_tasks' => Task::where('assigned_to', $user->id)->count(),
                'new_leads_this_month' => Lead::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'completed_tasks_this_month' => Task::where('status', 'completed')
                    ->whereMonth('updated_at', now()->month)
                    ->whereYear('updated_at', now()->year)
                    ->count(),
                'my_pending_tasks' => Task::where('assigned_to', $user->id)
                    ->where('status', 'pending')
                    ->count(),
                'my_overdue_tasks' => Task::where('assigned_to', $user->id)
                    ->where('status', '!=', 'completed')
                    ->where('due_date', '<', now())
                    ->count(),
            ];
        } else {
            // Staff sees only their own statistics
            $stats = [
                'my_leads' => Lead::where('assigned_to', $user->id)->count(),
                'my_tasks' => Task::where('assigned_to', $user->id)->count(),
                'my_completed_tasks' => Task::where('assigned_to', $user->id)
                    ->where('status', 'completed')
                    ->count(),
                'my_pending_tasks' => Task::where('assigned_to', $user->id)
                    ->where('status', 'pending')
                    ->count(),
                'my_in_progress_tasks' => Task::where('assigned_to', $user->id)
                    ->where('status', 'in_progress')
                    ->count(),
                'my_overdue_tasks' => Task::where('assigned_to', $user->id)
                    ->where('status', '!=', 'completed')
                    ->where('due_date', '<', now())
                    ->count(),
                'my_leads_this_month' => Lead::where('assigned_to', $user->id)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'my_tasks_completed_this_month' => Task::where('assigned_to', $user->id)
                    ->where('status', 'completed')
                    ->whereMonth('updated_at', now()->month)
                    ->whereYear('updated_at', now()->year)
                    ->count(),
            ];
        }

        return $stats;
    }

    private function getRecentActivities($user)
    {
        $activities = collect();

        if ($user->role === 'admin' || $user->role === 'manager') {
            // Recent customers
            $recentCustomers = Customer::latest()
                ->take(5)
                ->get()
                ->map(function ($customer) {
                    return [
                        'type' => 'customer',
                        'action' => 'created',
                        'description' => "New customer: {$customer->name}",
                        'created_at' => $customer->created_at,
                        'url' => route('customers.show', $customer),
                    ];
                });

            // Recent leads
            $recentLeads = Lead::with(['customer', 'assignedTo'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($lead) {
                    return [
                        'type' => 'lead',
                        'action' => 'created',
                        'description' => "New lead: {$lead->title} for {$lead->customer->name}",
                        'created_at' => $lead->created_at,
                        'url' => route('leads.show', $lead),
                    ];
                });

            // Recent tasks
            $recentTasks = Task::with(['lead.customer', 'assignedTo'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($task) {
                    return [
                        'type' => 'task',
                        'action' => 'created',
                        'description' => "New task: {$task->title} assigned to {$task->assignedTo->name}",
                        'created_at' => $task->created_at,
                        'url' => route('tasks.show', $task),
                    ];
                });

            $activities = $activities->merge($recentCustomers)
                ->merge($recentLeads)
                ->merge($recentTasks);
        } else {
            // Staff sees only their activities
            $myLeads = Lead::with(['customer'])
                ->where('assigned_to', $user->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($lead) {
                    return [
                        'type' => 'lead',
                        'action' => 'assigned',
                        'description' => "Assigned lead: {$lead->title} for {$lead->customer->name}",
                        'created_at' => $lead->created_at,
                        'url' => route('leads.show', $lead),
                    ];
                });

            $myTasks = Task::with(['lead.customer'])
                ->where('assigned_to', $user->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($task) {
                    return [
                        'type' => 'task',
                        'action' => 'assigned',
                        'description' => "Assigned task: {$task->title}",
                        'created_at' => $task->created_at,
                        'url' => route('tasks.show', $task),
                    ];
                });

            $activities = $activities->merge($myLeads)->merge($myTasks);
        }

        return $activities->sortByDesc('created_at')->take(10)->values();
    }

    private function getChartData($user)
    {
        $chartData = [];

        // Leads by status
        if ($user->role === 'admin' || $user->role === 'manager') {
            $leadsQuery = Lead::query();
        } else {
            $leadsQuery = Lead::where('assigned_to', $user->id);
        }

        $chartData['leads_by_status'] = $leadsQuery
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Tasks by status
        if ($user->role === 'admin' || $user->role === 'manager') {
            $tasksQuery = Task::query();
        } else {
            $tasksQuery = Task::where('assigned_to', $user->id);
        }

        $chartData['tasks_by_status'] = $tasksQuery
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Monthly data for the last 6 months
        $months = [];
        $leadsData = [];
        $tasksData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            if ($user->role === 'admin' || $user->role === 'manager') {
                $leadsCount = Lead::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();
                $tasksCount = Task::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();
            } else {
                $leadsCount = Lead::where('assigned_to', $user->id)
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();
                $tasksCount = Task::where('assigned_to', $user->id)
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();
            }

            $leadsData[] = $leadsCount;
            $tasksData[] = $tasksCount;
        }

        $chartData['monthly_data'] = [
            'months' => $months,
            'leads' => $leadsData,
            'tasks' => $tasksData,
        ];

        return $chartData;
    }

    private function getUpcomingTasks($user)
    {
        $query = Task::with(['lead.customer', 'assignedTo'])
            ->where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->orderBy('due_date');

        if ($user->role === 'staff') {
            $query->where('assigned_to', $user->id);
        }

        return $query->take(5)->get();
    }

    private function getRecentLeads($user)
    {
        $query = Lead::with(['customer', 'assignedTo'])->latest();

        if ($user->role === 'staff') {
            $query->where('assigned_to', $user->id);
        }

        return $query->take(5)->get();
    }

    public function api()
    {
        $user = auth()->user();
        
        return response()->json([
            'statistics' => $this->getStatistics($user),
            'recent_activities' => $this->getRecentActivities($user),
            'chart_data' => $this->getChartData($user),
            'upcoming_tasks' => $this->getUpcomingTasks($user),
            'recent_leads' => $this->getRecentLeads($user),
        ]);
    }
}