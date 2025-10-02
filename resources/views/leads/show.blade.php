@extends('layouts.app')

@section('title', 'Lead Details')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ $lead->title }}</h4>
                        <div>
                            <a href="{{ route('leads.edit', $lead) }}" class="btn btn-outline-warning me-2">
                                <i class="bi bi-pencil"></i> Edit Lead
                            </a>
                            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Leads
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Lead Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Title:</strong></td>
                                    <td>{{ $lead->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Source:</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucfirst(str_replace('_', ' ', $lead->source)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'new' => 'primary',
                                                'contacted' => 'info',
                                                'qualified' => 'warning',
                                                'proposal' => 'secondary',
                                                'won' => 'success',
                                                'lost' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$lead->status] ?? 'secondary' }}">
                                            {{ ucfirst($lead->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Assigned To:</strong></td>
                                    <td>{{ $lead->assignedTo->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>
                                        <a href="{{ route('customers.show', $lead->customer) }}" class="text-decoration-none">
                                            {{ $lead->customer->name }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>
                                        <a href="mailto:{{ $lead->customer->email }}">{{ $lead->customer->email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>
                                        <a href="tel:{{ $lead->customer->phone }}">{{ $lead->customer->phone }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Company:</strong></td>
                                    <td>{{ $lead->customer->company }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Created:</small><br>
                            <span>{{ $lead->created_at->format('M d, Y \a\t g:i A') }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Last Updated:</small><br>
                            <span>{{ $lead->updated_at->format('M d, Y \a\t g:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tasks.create', ['lead_id' => $lead->id]) }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-lg"></i> Add Task
                        </a>
                        <a href="{{ route('customers.show', $lead->customer) }}" class="btn btn-outline-info">
                            <i class="bi bi-person"></i> View Customer
                        </a>
                        <a href="mailto:{{ $lead->customer->email }}" class="btn btn-outline-success">
                            <i class="bi bi-envelope"></i> Send Email
                        </a>
                        <a href="tel:{{ $lead->customer->phone }}" class="btn btn-outline-warning">
                            <i class="bi bi-telephone"></i> Call Customer
                        </a>
                    </div>
                </div>
            </div>

            <!-- Related Tasks -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Related Tasks ({{ $lead->tasks->count() }})</h6>
                        <a href="{{ route('tasks.create', ['lead_id' => $lead->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus-lg"></i> Add
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($lead->tasks->count() > 0)
                        @foreach($lead->tasks->take(5) as $task)
                            <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                            {{ $task->title }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        Assigned to: {{ $task->assignedTo->name }}<br>
                                        Due: {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                    </small>
                                </div>
                                <div>
                                    @php
                                        $taskStatusColors = [
                                            'pending' => 'warning',
                                            'in_progress' => 'info',
                                            'completed' => 'success'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $taskStatusColors[$task->status] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($lead->tasks->count() > 5)
                            <div class="text-center">
                                <a href="{{ route('tasks.index', ['lead_id' => $lead->id]) }}" class="btn btn-sm btn-outline-secondary">
                                    View All Tasks ({{ $lead->tasks->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-clipboard-x display-6 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">No tasks yet</p>
                            <a href="{{ route('tasks.create', ['lead_id' => $lead->id]) }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="bi bi-plus-lg"></i> Add First Task
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection