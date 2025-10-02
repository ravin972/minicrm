@extends('layouts.app')

@section('title', 'Tasks Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Tasks Management</h1>
                <div>
                    <a href="{{ route('tasks.export', request()->query()) }}" class="btn btn-outline-success me-2">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add New Task
                    </a>
                </div>
            </div>

            <!-- Search and Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('tasks.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search tasks, leads...">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="assigned_to" class="form-label">Assigned To</label>
                            <select class="form-select" id="assigned_to" name="assigned_to">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="lead_id" class="form-label">Lead</label>
                            <select class="form-select" id="lead_id" name="lead_id">
                                <option value="">All Leads</option>
                                @foreach($leads as $lead)
                                    <option value="{{ $lead->id }}" {{ request('lead_id') == $lead->id ? 'selected' : '' }}>
                                        {{ $lead->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tasks Table -->
            <div class="card">
                <div class="card-body">
                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <a href="{{ route('tasks.index', array_merge(request()->query(), ['sort_by' => 'title', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                               class="text-decoration-none text-dark">
                                                Title
                                                @if(request('sort_by') == 'title')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Lead</th>
                                        <th>Customer</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        <th>
                                            <a href="{{ route('tasks.index', array_merge(request()->query(), ['sort_by' => 'due_date', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                               class="text-decoration-none text-dark">
                                                Due Date
                                                @if(request('sort_by') == 'due_date')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ route('tasks.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                               class="text-decoration-none text-dark">
                                                Created
                                                @if(request('sort_by') == 'created_at' || !request('sort_by'))
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td>
                                                <strong>{{ $task->title }}</strong>
                                                @if($task->description)
                                                    <br><small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('leads.show', $task->lead) }}" class="text-decoration-none">
                                                    {{ $task->lead->title }}
                                                </a>
                                            </td>
                                            <td>{{ $task->lead->customer->name }}</td>
                                            <td>{{ $task->assignedTo->name }}</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'in_progress' => 'info',
                                                        'completed' => 'success'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                    @php
                                                        $isOverdue = $task->due_date->isPast() && $task->status !== 'completed';
                                                        $isDueSoon = $task->due_date->isToday() || ($task->due_date->isTomorrow() && $task->status !== 'completed');
                                                    @endphp
                                                    <span class="@if($isOverdue) text-danger @elseif($isDueSoon) text-warning @endif">
                                                        {{ $task->due_date->format('M d, Y') }}
                                                        @if($isOverdue)
                                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                                        @elseif($isDueSoon)
                                                            <i class="bi bi-clock-fill"></i>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-muted">No due date</span>
                                                @endif
                                            </td>
                                            <td>{{ $task->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manager' || $task->assigned_to === auth()->id())
                                                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                    onclick="return confirm('Are you sure you want to delete this task?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <p class="text-muted mb-0">
                                    Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }} results
                                </p>
                            </div>
                            <div>
                                {{ $tasks->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-x display-1 text-muted"></i>
                            <h4 class="mt-3">No tasks found</h4>
                            <p class="text-muted">Get started by creating your first task.</p>
                            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Add New Task
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection