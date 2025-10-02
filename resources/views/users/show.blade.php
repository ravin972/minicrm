@extends('layouts.app')

@section('title', 'User Details - Mini CRM')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-person"></i> User Details</h1>
        <div>
            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit User
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- User Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Name</label>
                                <div class="fw-bold">{{ $user->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Email</label>
                                <div class="fw-bold">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Role</label>
                                <div>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'manager' ? 'warning' : 'info') }} fs-6">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Member Since</label>
                                <div class="fw-bold">{{ $user->created_at->format('F d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-graph-up"></i> Activity Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-primary">{{ $user->assignedLeads->count() }}</h3>
                                <p class="text-muted mb-0">Assigned Leads</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-end">
                                <h3 class="text-success">{{ $user->assignedTasks->count() }}</h3>
                                <p class="text-muted mb-0">Assigned Tasks</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-info">{{ $user->assignedTasks->where('status', 'completed')->count() }}</h3>
                            <p class="text-muted mb-0">Completed Tasks</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit User
                        </a>
                        <a href="{{ route('leads.index', ['assigned_to' => $user->id]) }}" class="btn btn-outline-info">
                            <i class="bi bi-person-check"></i> View Assigned Leads
                        </a>
                        <a href="{{ route('tasks.index', ['assigned_to' => $user->id]) }}" class="btn btn-outline-success">
                            <i class="bi bi-list-task"></i> View Assigned Tasks
                        </a>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" 
                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    <i class="bi bi-trash"></i> Delete User
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Recent Activity</h5>
                </div>
                <div class="card-body">
                    @if($user->assignedTasks->take(5)->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($user->assignedTasks->sortByDesc('updated_at')->take(5) as $task)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $task->title }}</h6>
                                            <p class="mb-1 text-muted small">{{ Str::limit($task->description, 50) }}</p>
                                            <small class="text-muted">{{ $task->updated_at->diffForHumans() }}</small>
                                        </div>
                                        <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-clock-history display-6 text-muted"></i>
                            <p class="text-muted mt-2">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection