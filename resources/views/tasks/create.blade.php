@extends('layouts.app')

@section('title', 'Create New Task')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Create New Task</h4>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Tasks
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Enter task description...">{{ old('description') }}</textarea>
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
                                            {{ old('lead_id', $selectedLead?->id) == $lead->id ? 'selected' : '' }}>
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
                                            {{ old('assigned_to', auth()->id()) == $user->id ? 'selected' : '' }}>
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
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>
                                    In Progress
                                </option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
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
                                   id="due_date" name="due_date" value="{{ old('due_date') }}" 
                                   min="{{ date('Y-m-d') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Leave empty if no specific due date is required.</div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Create Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Actions Card -->
            @if($selectedLead)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Lead Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Lead:</strong> {{ $selectedLead->title }}<br>
                                <strong>Customer:</strong> {{ $selectedLead->customer->name }}<br>
                                <strong>Status:</strong> 
                                <span class="badge bg-{{ $selectedLead->status === 'new' ? 'primary' : ($selectedLead->status === 'qualified' ? 'success' : ($selectedLead->status === 'contacted' ? 'info' : 'secondary')) }}">
                                    {{ ucfirst($selectedLead->status) }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <strong>Source:</strong> {{ ucfirst($selectedLead->source) }}<br>
                                <strong>Assigned To:</strong> {{ $selectedLead->assignedTo->name }}<br>
                                <strong>Created:</strong> {{ $selectedLead->created_at->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('leads.show', $selectedLead) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View Lead Details
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-select current user as assigned to if no selection made
    const assignedToSelect = document.getElementById('assigned_to');
    if (!assignedToSelect.value) {
        assignedToSelect.value = '{{ auth()->id() }}';
    }
    
    // Set minimum date to today
    const dueDateInput = document.getElementById('due_date');
    const today = new Date().toISOString().split('T')[0];
    dueDateInput.setAttribute('min', today);
});
</script>
@endsection