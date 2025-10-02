@extends('layouts.app')

@section('title', 'Leads Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Leads Management</h1>
                <div>
                    <a href="{{ route('leads.export', request()->query()) }}" class="btn btn-outline-success me-2">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                    <a href="{{ route('leads.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add New Lead
                    </a>
                </div>
            </div>

            <!-- Search and Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('leads.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search leads or customers...">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                                <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                                <option value="proposal" {{ request('status') == 'proposal' ? 'selected' : '' }}>Proposal</option>
                                <option value="won" {{ request('status') == 'won' ? 'selected' : '' }}>Won</option>
                                <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="source" class="form-label">Source</label>
                            <select class="form-select" id="source" name="source">
                                <option value="">All Sources</option>
                                <option value="website" {{ request('source') == 'website' ? 'selected' : '' }}>Website</option>
                                <option value="referral" {{ request('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="social_media" {{ request('source') == 'social_media' ? 'selected' : '' }}>Social Media</option>
                                <option value="email" {{ request('source') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="phone" {{ request('source') == 'phone' ? 'selected' : '' }}>Phone</option>
                                <option value="other" {{ request('source') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Leads Table -->
            <div class="card">
                <div class="card-body">
                    @if($leads->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <a href="{{ route('leads.index', array_merge(request()->query(), ['sort_by' => 'title', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                               class="text-decoration-none text-dark">
                                                Title
                                                @if(request('sort_by') == 'title')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Source</th>
                                        <th>Customer</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        <th>
                                            <a href="{{ route('leads.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
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
                                    @foreach($leads as $lead)
                                        <tr>
                                            <td>
                                                <strong>{{ $lead->title }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ ucfirst(str_replace('_', ' ', $lead->source)) }}
                                                </span>
                                            </td>
                                            <td>{{ $lead->customer->name }}</td>
                                            <td>{{ $lead->assignedTo->name }}</td>
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
                                            <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this lead?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
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
                                    Showing {{ $leads->firstItem() }} to {{ $leads->lastItem() }} of {{ $leads->total() }} results
                                </p>
                            </div>
                            <div>
                                {{ $leads->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="mt-3">No leads found</h4>
                            <p class="text-muted">Get started by creating your first lead.</p>
                            <a href="{{ route('leads.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Add New Lead
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection