@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Edit Task</h4>
                        <div>
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-info btn-sm me-2">
                                <i class="bi bi-eye"></i> View Task
                            </a>
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Back to Tasks
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $task->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Enter task description...">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lead -->
                        <div class="mb-3">
                            <label for="lead_id" class="form-label">Lead <span class="text-danger">*</span></label>
                            <select class="form-select @error('lead_id') is-invalid @enderror" 
                                    id="lead_id" name="lead_id" required>
                                <option value="">Select a lead</option>
                                @foreach($leads as $lead)
                                    <option value="{{ $lead->id }}" 
                                            {{ old('lead_id', $task->lead_id) == $lead->id ? 'selected' : '' }}>
                                        {{ $lead->title }} ({{ $lead->customer->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('lead_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Assigned To -->
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">Assigned To <span class="text-danger">*</span></label>
                            <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                    id="assigned_to" name="assigned_to" required>
                                <option value="">Select a user</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ ucfirst($user->role) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>
                                    In Progress
                                </option>
                                <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Due Date -->
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" 
                                   value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Leave empty if no specific due date is required.</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Update Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Task Information Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Current Task Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Current Status:</strong> 
                            <span class="badge bg-{{ $task->status === 'pending' ? 'warning' : ($task->status === 'in_progress' ? 'info' : 'success') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span><br>
                            <strong>Lead:</strong> {{ $task->lead->title }}<br>
                            <strong>Customer:</strong> {{ $task->lead->customer->name }}<br>
                            <strong>Currently Assigned To:</strong> {{ $task->assignedTo->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Created:</strong> {{ $task->created_at->format('M d, Y g:i A') }}<br>
                            <strong>Last Updated:</strong> {{ $task->updated_at->format('M d, Y g:i A') }}<br>
                            @if($task->due_date)
                                <strong>Current Due Date:</strong> 
                                @php
                                    $isOverdue = $task->due_date->isPast() && $task->status !== 'completed';
                                    $isDueSoon = $task->due_date->isToday() || ($task->due_date->isTomorrow() && $task->status !== 'completed');
                                @endphp
                                <span class="@if($isOverdue) text-danger @elseif($isDueSoon) text-warning @endif">
                                    {{ $task->due_date->format('M d, Y') }}
                                    @if($isOverdue)
                                        <i class="bi bi-exclamation-triangle-fill"></i> Overdue
                                    @elseif($isDueSoon)
                                        <i class="bi bi-clock-fill"></i> Due Soon
                                    @endif
                                </span>
                            @else
                                <strong>Due Date:</strong> <span class="text-muted">Not set</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($task->description)
                        <div class="mt-3">
                            <strong>Current Description:</strong>
                            <div class="border rounded p-2 mt-1 bg-light">
                                {{ $task->description }}
                            </div>
                        </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('leads.show', $task->lead) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View Related Lead
                        </a>
                        <a href="{{ route('customers.show', $task->lead->customer) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-person"></i> View Customer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status change to show completion confirmation
    const statusSelect = document.getElementById('status');
    const originalStatus = '{{ $task->status }}';
    
    statusSelect.addEventListener('change', function() {
        if (this.value === 'completed' && originalStatus !== 'completed') {
            if (!confirm('Are you sure you want to mark this task as completed?')) {
                this.value = originalStatus;
            }
        }
    });
});
</script>
@endsection