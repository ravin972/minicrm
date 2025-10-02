@extends('layouts.app')

@section('title', 'Customer Details - Mini CRM')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-person"></i> {{ $customer->name }}</h1>
        <div>
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil"></i> Edit Customer
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Customers
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Customer Information -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Name</label>
                        <p class="fw-bold">{{ $customer->name }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <p><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></p>
                    </div>
                    
                    @if($customer->phone)
                    <div class="mb-3">
                        <label class="form-label text-muted">Phone</label>
                        <p><a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a></p>
                    </div>
                    @endif
                    
                    @if($customer->company)
                    <div class="mb-3">
                        <label class="form-label text-muted">Company</label>
                        <p>{{ $customer->company }}</p>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <p>
                            <span class="badge bg-{{ $customer->status == 'active' ? 'success' : ($customer->status == 'inactive' ? 'danger' : 'warning') }} fs-6">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Created</label>
                        <p>{{ $customer->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label text-muted">Last Updated</label>
                        <p>{{ $customer->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leads and Tasks -->
        <div class="col-md-8">
            <!-- Leads Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-graph-up-arrow"></i> Leads ({{ $customer->leads->count() }})</h5>
                    <a href="{{ route('leads.create', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Add Lead
                    </a>
                </div>
                <div class="card-body">
                    @if($customer->leads->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Source</th>
                                    <th>Assigned To</th>
                                    <th>Status</th>
                                    <th>Tasks</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->leads as $lead)
                                <tr>
                                    <td><strong>{{ $lead->title }}</strong></td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($lead->source) }}</span>
                                    </td>
                                    <td>{{ $lead->assignedUser->name ?? 'Unassigned' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $lead->status == 'won' ? 'success' : ($lead->status == 'lost' ? 'danger' : 'primary') }}">
                                            {{ ucfirst($lead->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $lead->tasks->count() }}</span>
                                    </td>
                                    <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('leads.edit', $lead) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-graph-up-arrow text-muted" style="font-size: 3rem;"></i>
                        <h6 class="text-muted mt-2">No leads yet</h6>
                        <p class="text-muted">Create the first lead for this customer.</p>
                        <a href="{{ route('leads.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus"></i> Add Lead
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Tasks Section -->
            @if($customer->leads->flatMap->tasks->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-check-square"></i> Recent Tasks</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Lead</th>
                                    <th>Assigned To</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->leads->flatMap->tasks->sortByDesc('created_at')->take(5) as $task)
                                <tr>
                                    <td>{{ Str::limit($task->description, 50) }}</td>
                                    <td>{{ $task->lead->title }}</td>
                                    <td>{{ $task->assignedUser->name ?? 'Unassigned' }}</td>
                                    <td>
                                        @if($task->due_date)
                                            <span class="text-{{ $task->due_date->isPast() ? 'danger' : 'muted' }}">
                                                {{ $task->due_date->format('M d, Y') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection