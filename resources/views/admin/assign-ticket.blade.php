@extends('layouts.app')

@section('title', 'Assign Ticket')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Assign Ticket</h4>
                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Tickets
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Ticket Details</h5>
                            <p><strong>Title:</strong> {{ $ticket->name }}</p>
                            <p><strong>Department:</strong> 
                                <span class="badge bg-primary">{{ $ticket->department->name }}</span>
                            </p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $ticket->status === 'completed' ? 'success' : ($ticket->status === 'in_progress' ? 'warning' : ($ticket->status === 'open' ? 'info' : 'danger')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </p>
                            <p><strong>Location:</strong> {{ $ticket->location }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Current Assignment</h5>
                            @if($ticket->accepted_by)
                                <div class="alert alert-info">
                                    <p><strong>Currently Assigned to:</strong> {{ $ticket->acceptedBy->name }}</p>
                                    <p><strong>Department:</strong> {{ $ticket->acceptedBy->department->name }}</p>
                                    <p><strong>Assigned on:</strong> {{ $ticket->updated_at->format('M d, Y H:i') }}</p>
                                    <p class="mb-0"><small class="text-muted">Select a new user below to reassign this ticket.</small></p>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <p class="mb-0"><strong>Not assigned to any user yet.</strong></p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="alert alert-light border">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>📊 Available Users:</strong> {{ $departmentUsers->count() }} total department users
                            </div>
                            <div class="col-md-6">
                                <strong>🏢 Departments:</strong> {{ $departmentUsers->groupBy('department_id')->count() }} departments
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.tickets.assign.store', $ticket) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="assigned_user_id" class="form-label">
                                <i class="fas fa-users me-1"></i>Assign to Department User
                            </label>
                            <small class="form-text text-muted mb-2 d-block">
                                Users are grouped by their department. You can assign to any department user.
                            </small>
                            <select class="form-select @error('assigned_user_id') is-invalid @enderror" 
                                    id="assigned_user_id" name="assigned_user_id" required>
                                <option value="">📋 Select a department user from the list below...</option>
                                @php
                                    $groupedUsers = $departmentUsers->groupBy('department_id');
                                @endphp
                                @foreach($groupedUsers as $departmentId => $users)
                                    @php
                                        $department = \App\Models\Department::find($departmentId);
                                    @endphp
                                    <optgroup label="📁 {{ $department ? $department->name : 'Unknown Department' }} ({{ $users->count() }} users){{ $department && $department->id == $ticket->department_id ? ' - TICKET DEPARTMENT' : '' }}">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                    {{ $ticket->accepted_by == $user->id ? 'selected' : '' }}>
                                                👤 {{ $user->name }} - {{ $user->email }}
                                                @if($department && $department->id == $ticket->department_id)
                                                    🎯
                                                @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('assigned_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Note:</strong> You can assign this ticket to any department user. The ticket's department is prioritized at the top of the list. Assigning will automatically change the ticket status from "Open" to "In Progress".
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>{{ $ticket->accepted_by ? 'Reassign Ticket' : 'Assign Ticket' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 