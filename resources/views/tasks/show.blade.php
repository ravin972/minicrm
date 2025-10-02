@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <!-- Task Details Card -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ $task->title }}</h4>
                        <div>
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manager' || $task->assigned_to === auth()->id())
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-warning btn-sm me-2">
                                    <i class="bi bi-pencil"></i> Edit Task
                                </a>
                            @endif
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Back to Tasks
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Task Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
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
                                </tr>
                                <tr>
                                    <td><strong>Assigned To:</strong></td>
                                    <td>{{ $task->assignedTo->name }} ({{ ucfirst($task->assignedTo->role) }})</td>
                                </tr>
                                <tr>
                                    <td><strong>Due Date:</strong></td>
                                    <td>
                                        @if($task->due_date)
                                            @php
                                                $isOverdue = $task->due_date->isPast() && $task->status !== 'completed';
                                                $isDueSoon = $task->due_date->isToday() || ($task->due_date->isTomorrow() && $task->status !== 'completed');
                                            @endphp
                                            <span class="@if($isOverdue) text-danger @elseif($isDueSoon) text-warning @endif">
                                                {{ $task->due_date->format('F d, Y') }}
                                                @if($isOverdue)
                                                    <i class="bi bi-exclamation-triangle-fill"></i> Overdue
                                                @elseif($isDueSoon)
                                                    <i class="bi bi-clock-fill"></i> Due Soon
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">No due date set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $task->created_at->format('F d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $task->updated_at->format('F d, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Related Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Lead:</strong></td>
                                    <td>
                                        <a href="{{ route('leads.show', $task->lead) }}" class="text-decoration-none">
                                            {{ $task->lead->title }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Customer:</strong></td>
                                    <td>
                                        <a href="{{ route('customers.show', $task->lead->customer) }}" class="text-decoration-none">
                                            {{ $task->lead->customer->name }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Customer Email:</strong></td>
                                    <td>
                                        <a href="mailto:{{ $task->lead->customer->email }}" class="text-decoration-none">
                                            {{ $task->lead->customer->email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Customer Phone:</strong></td>
                                    <td>
                                        @if($task->lead->customer->phone)
                                            <a href="tel:{{ $task->lead->customer->phone }}" class="text-decoration-none">
                                                {{ $task->lead->customer->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Lead Status:</strong></td>
                                    <td>
                                        @php
                                            $leadStatusColors = [
                                                'new' => 'primary',
                                                'contacted' => 'info',
                                                'qualified' => 'success',
                                                'lost' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $leadStatusColors[$task->lead->status] ?? 'secondary' }}">
                                            {{ ucfirst($task->lead->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($task->description)
                        <div class="mb-4">
                            <h6 class="text-muted">Description</h6>
                            <div class="border rounded p-3 bg-light">
                                {!! nl2br(e($task->description)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Actions Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($task->status !== 'completed' && (auth()->user()->role === 'admin' || auth()->user()->role === 'manager' || $task->assigned_to === auth()->id()))
                            <form action="{{ route('tasks.update', $task) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="title" value="{{ $task->title }}">
                                <input type="hidden" name="description" value="{{ $task->description }}">
                                <input type="hidden" name="lead_id" value="{{ $task->lead_id }}">
                                <input type="hidden" name="assigned_to" value="{{ $task->assigned_to }}">
                                <input type="hidden" name="due_date" value="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}">
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-success w-100" 
                                        onclick="return confirm('Mark this task as completed?')">
                                    <i class="bi bi-check-circle"></i> Mark as Completed
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('leads.show', $task->lead) }}" class="btn btn-outline-primary">
                            <i class="bi bi-eye"></i> View Related Lead
                        </a>

                        <a href="{{ route('customers.show', $task->lead->customer) }}" class="btn btn-outline-info">
                            <i class="bi bi-person"></i> View Customer Profile
                        </a>

                        <a href="mailto:{{ $task->lead->customer->email }}" class="btn btn-outline-secondary">
                            <i class="bi bi-envelope"></i> Send Email
                        </a>

                        @if($task->lead->customer->phone)
                            <a href="tel:{{ $task->lead->customer->phone }}" class="btn btn-outline-secondary">
                                <i class="bi bi-telephone"></i> Call Customer
                            </a>
                        @endif

                        <a href="{{ route('tasks.create', ['lead_id' => $task->lead_id]) }}" class="btn btn-outline-success">
                            <i class="bi bi-plus-lg"></i> Add Related Task
                        </a>
                    </div>
                </div>
            </div>

            <!-- Task Progress Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Task Progress</h5>
                </div>
                <div class="card-body">
                    @php
                        $progress = 0;
                        if ($task->status === 'pending') $progress = 25;
                        elseif ($task->status === 'in_progress') $progress = 60;
                        elseif ($task->status === 'completed') $progress = 100;
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Progress</span>
                            <span class="text-muted">{{ $progress }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-{{ $progress === 100 ? 'success' : ($progress > 50 ? 'info' : 'warning') }}" 
                                 role="progressbar" style="width: {{ $progress }}%" 
                                 aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="badge bg-{{ $task->status === 'pending' ? 'warning' : 'light text-dark' }}">
                                Pending
                            </span>
                            <span class="badge bg-{{ $task->status === 'in_progress' ? 'info' : 'light text-dark' }}">
                                In Progress
                            </span>
                            <span class="badge bg-{{ $task->status === 'completed' ? 'success' : 'light text-dark' }}">
                                Completed
                            </span>
                        </div>
                    </div>

                    @if($task->due_date)
                        <hr>
                        <div class="text-center">
                            @php
                                $daysUntilDue = now()->diffInDays($task->due_date, false);
                                $isOverdue = $task->due_date->isPast() && $task->status !== 'completed';
                            @endphp
                            
                            @if($isOverdue)
                                <span class="text-danger">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    Overdue by {{ abs($daysUntilDue) }} day{{ abs($daysUntilDue) !== 1 ? 's' : '' }}
                                </span>
                            @elseif($daysUntilDue === 0)
                                <span class="text-warning">
                                    <i class="bi bi-clock-fill"></i>
                                    Due today
                                </span>
                            @elseif($daysUntilDue === 1)
                                <span class="text-warning">
                                    <i class="bi bi-clock"></i>
                                    Due tomorrow
                                </span>
                            @else
                                <span class="text-muted">
                                    <i class="bi bi-calendar"></i>
                                    {{ $daysUntilDue }} day{{ $daysUntilDue !== 1 ? 's' : '' }} remaining
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Tasks Card -->
            @if($relatedTasks->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Other Tasks for This Lead</h5>
                    </div>
                    <div class="card-body">
                        @foreach($relatedTasks as $relatedTask)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <a href="{{ route('tasks.show', $relatedTask) }}" class="text-decoration-none">
                                        {{ Str::limit($relatedTask->title, 30) }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $relatedTask->assignedTo->name }}</small>
                                </div>
                                <span class="badge bg-{{ $relatedTask->status === 'pending' ? 'warning' : ($relatedTask->status === 'in_progress' ? 'info' : 'success') }}">
                                    {{ ucfirst(str_replace('_', ' ', $relatedTask->status)) }}
                                </span>
                            </div>
                        @endforeach
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('tasks.index', ['lead_id' => $task->lead_id]) }}" class="btn btn-sm btn-outline-primary">
                                View All Tasks for This Lead
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection