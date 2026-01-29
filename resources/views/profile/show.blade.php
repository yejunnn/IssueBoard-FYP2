@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x text-white"></i>
                        </div>
                        <h5 class="mt-3 mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-0">{{ $user->department->name }}</p>
                        <p class="text-muted mb-2"><small>{{ $user->email }}</small></p>
                        <span class="badge bg-success">Department User</span>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-primary mb-0">{{ $ticketStats['total'] }}</h4>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-warning mb-0">{{ $ticketStats['in_progress'] }}</h4>
                                <small class="text-muted">In Progress</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success mb-0">{{ $ticketStats['completed'] }}</h4>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Update Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Note:</strong> You can only update your display name. Email address is managed by the school and cannot be changed.
                    </div>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Display Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Email cannot be changed as it's used for school purposes.
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <input type="text" class="form-control" value="{{ $user->department->name }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Account Type</label>
                                <input type="text" class="form-control" value="Department User" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Member Since</label>
                                <input type="text" class="form-control" value="{{ $user->created_at->format('M d, Y') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Updated</label>
                                <input type="text" class="form-control" value="{{ $user->updated_at->format('M d, Y H:i') }}" readonly>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Password must be at least 8 characters long.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-1"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Ticket Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-ticket-alt fa-2x text-primary mb-2"></i>
                                <h3 class="text-primary">{{ $ticketStats['total'] }}</h3>
                                <p class="mb-0">Total Tickets Assigned</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h3 class="text-warning">{{ $ticketStats['in_progress'] }}</h3>
                                <p class="mb-0">In Progress</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h3 class="text-success">{{ $ticketStats['completed'] }}</h3>
                                <p class="mb-0">Completed</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-percentage fa-2x text-info mb-2"></i>
                                <h3 class="text-info">{{ $ticketStats['total'] > 0 ? round(($ticketStats['completed'] / $ticketStats['total']) * 100, 1) : 0 }}%</h3>
                                <p class="mb-0">Completion Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 