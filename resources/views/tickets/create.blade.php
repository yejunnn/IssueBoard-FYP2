@extends('layouts.app')

@section('title', 'Create Ticket')

@section('content')
<div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Create New Ticket</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-heading me-1"></i>Issue Title *
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Brief description of the issue" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Category *
                                </label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->department->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    The ticket will be automatically assigned to the responsible department.
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>Location *
                                </label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location') }}" 
                                       placeholder="e.g., Building A, Room 101, Lecture Hall 2" required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>Description *
                                </label>
                                  <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" 
                                          placeholder="Please provide detailed information about the issue..." required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">
                                    <i class="fas fa-image me-1"></i>Image (Optional)
                                </label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Upload an image to help describe the issue. Supported formats: JPEG, PNG, JPG, GIF (max 2MB)
                                </small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i>Create Ticket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 