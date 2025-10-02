@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Welcome back, {{ auth()->user()->name }}!</h1>
                    <p class="text-muted mb-0">Here's what's happening with your CRM today.</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        @if(auth()->user()->role === 'admin')
            <!-- Admin Statistics -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Customers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_customers'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-people-fill fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Leads
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_leads'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-graph-up-arrow fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Tasks
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_tasks'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-list-task fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Overdue Tasks
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['overdue_tasks'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-exclamation-triangle-fill fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif(auth()->user()->role === 'manager')
            <!-- Manager Statistics -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Customers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_customers'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-people-fill fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Leads
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_leads'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-graph-up-arrow fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    My Tasks
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_tasks'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-person-check-fill fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    My Overdue Tasks
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_overdue_tasks'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-exclamation-triangle-fill fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Staff Statistics -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    My Leads
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_leads'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-graph-up-arrow fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    My Tasks
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_tasks'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-list-task fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Completed Tasks
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_completed_tasks'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-check-circle-fill fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Overdue Tasks
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_overdue_tasks'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-exclamation-triangle-fill fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Charts and Recent Activities Row -->
    <div class="row mb-4">
        <!-- Charts Column -->
        <div class="col-xl-8 col-lg-7">
            <!-- Monthly Trends Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Trends</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyTrendsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Distribution Charts -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Leads by Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="leadsStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Tasks by Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="tasksStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Column -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        @foreach($recentActivities as $activity)
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    @if($activity['type'] === 'customer')
                                        <div class="icon-circle bg-primary">
                                            <i class="bi bi-person-plus text-white"></i>
                                        </div>
                                    @elseif($activity['type'] === 'lead')
                                        <div class="icon-circle bg-success">
                                            <i class="bi bi-graph-up text-white"></i>
                                        </div>
                                    @else
                                        <div class="icon-circle bg-info">
                                            <i class="bi bi-list-task text-white"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-gray-500">{{ $activity['created_at']->diffForHumans() }}</div>
                                    <a href="{{ $activity['url'] }}" class="text-decoration-none">
                                        {{ $activity['description'] }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">No recent activities</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Tasks and Recent Leads Row -->
    <div class="row">
        <!-- Upcoming Tasks -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Tasks</h6>
                    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($upcomingTasks->count() > 0)
                        @foreach($upcomingTasks as $task)
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                            {{ $task->title }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-1">{{ $task->lead->title }} - {{ $task->lead->customer->name }}</p>
                                    <small class="text-muted">Assigned to: {{ $task->assignedTo->name }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $task->status === 'pending' ? 'warning' : 'info' }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                    <div class="small text-muted mt-1">
                                        {{ $task->due_date->format('M d') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="bi bi-calendar-check display-4"></i>
                            <p class="mt-2">No upcoming tasks</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Leads -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Leads</h6>
                    <a href="{{ route('leads.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentLeads->count() > 0)
                        @foreach($recentLeads as $lead)
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('leads.show', $lead) }}" class="text-decoration-none">
                                            {{ $lead->title }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-1">{{ $lead->customer->name }}</p>
                                    <small class="text-muted">Assigned to: {{ $lead->assignedTo->name }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $lead->status === 'new' ? 'primary' : ($lead->status === 'qualified' ? 'success' : ($lead->status === 'contacted' ? 'info' : 'secondary')) }}">
                                        {{ ucfirst($lead->status) }}
                                    </span>
                                    <div class="small text-muted mt-1">
                                        {{ $lead->created_at->format('M d') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="bi bi-graph-up display-4"></i>
                            <p class="mt-2">No recent leads</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.text-gray-500 {
    color: #858796 !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart data from PHP
    const chartData = @json($chartData);
    
    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: chartData.monthly_data.months,
            datasets: [{
                label: 'Leads',
                data: chartData.monthly_data.leads,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3
            }, {
                label: 'Tasks',
                data: chartData.monthly_data.tasks,
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Leads Status Chart
    const leadsCtx = document.getElementById('leadsStatusChart').getContext('2d');
    new Chart(leadsCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(chartData.leads_by_status).map(status => status.charAt(0).toUpperCase() + status.slice(1)),
            datasets: [{
                data: Object.values(chartData.leads_by_status),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Tasks Status Chart
    const tasksCtx = document.getElementById('tasksStatusChart').getContext('2d');
    new Chart(tasksCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(chartData.tasks_by_status).map(status => status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
            datasets: [{
                data: Object.values(chartData.tasks_by_status),
                backgroundColor: ['#f6c23e', '#36b9cc', '#1cc88a']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
});
</script>
@endsection