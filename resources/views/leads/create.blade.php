@extends('layouts.app')

@section('title', 'Create New Lead')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Create New Lead</h4>
                        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Leads
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('leads.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">Lead Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" 
                                       placeholder="Enter lead title" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="source" class="form-label">Lead Source <span class="text-danger">*</span></label>
                                <select class="form-select @error('source') is-invalid @enderror" 
                                        id="source" name="source" required>
                                    <option value="">Select source</option>
                                    <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Website</option>
                                    <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                                    <option value="social_media" {{ old('source') == 'social_media' ? 'selected' : '' }}>Social Media</option>
                                    <option value="email" {{ old('source') == 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="phone" {{ old('source') == 'phone' ? 'selected' : '' }}>Phone</option>
                                    <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="">Select status</option>
                                    <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="contacted" {{ old('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="qualified" {{ old('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                                    <option value="proposal" {{ old('status') == 'proposal' ? 'selected' : '' }}>Proposal</option>
                                    <option value="won" {{ old('status') == 'won' ? 'selected' : '' }}>Won</option>
                                    <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" 
                                        id="customer_id" name="customer_id" required>
                                    <option value="">Select customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->company }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Don't see the customer? <a href="{{ route('customers.create') }}" target="_blank">Create a new customer</a>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assigned_to" class="form-label">Assign To <span class="text-danger">*</span></label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                        id="assigned_to" name="assigned_to" required>
                                    <option value="">Select user</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ ucfirst($user->role) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('leads.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Create Lead
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection