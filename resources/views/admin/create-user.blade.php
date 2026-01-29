@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Create New User</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-1"></i>Name *
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email Address *
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Password *
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-1"></i>Confirm Password *
                            </label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="mb-3" id="department-group">
                            <label for="department_id" class="form-label">
                                <i class="fas fa-building me-1"></i>Department *
                            </label>
                            <select class="form-select @error('department_id') is-invalid @enderror" 
                                    id="department_id" name="department_id" required>
                                <option value="">Select a department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="department-note" class="form-text text-muted" style="display:none;">
                                Department is not required for admin users.
                            </div>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" {{ old('is_admin') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_admin">
                                <i class="fas fa-shield-alt me-1"></i>Make this user an admin
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
    function toggleDepartmentField() {
        const isAdmin = document.getElementById('is_admin').checked;
        const deptGroup = document.getElementById('department-group');
        const deptSelect = document.getElementById('department_id');
        const deptNote = document.getElementById('department-note');
        if (isAdmin) {
            deptSelect.required = false;
            deptSelect.disabled = true;
            deptNote.style.display = 'block';
        } else {
            deptSelect.required = true;
            deptSelect.disabled = false;
            deptNote.style.display = 'none';
        }
    }
    document.getElementById('is_admin').addEventListener('change', toggleDepartmentField);
    window.addEventListener('DOMContentLoaded', toggleDepartmentField);
</script>
@endpush 