@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Ticket</h4>
                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Details
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('tickets.update', $ticket) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label"><i class="fas fa-heading me-1"></i>Issue Title *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $ticket->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label"><i class="fas fa-tag me-1"></i>Category *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $ticket->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->department->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Location *</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $ticket->location) }}" required>
                            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label"><i class="fas fa-align-left me-1"></i>Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $ticket->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label"><i class="fas fa-info-circle me-1"></i>Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="in_progress" {{ old('status', $ticket->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $ticket->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancel" {{ old('status', $ticket->status) == 'cancel' ? 'selected' : '' }}>Cancel</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label"><i class="fas fa-image me-1"></i>Image (Optional)</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($ticket->image_path)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($ticket->image_path) }}" class="img-fluid rounded" alt="Ticket Image" style="max-height: 200px;">
                                </div>
                            @else
                                <div class="mt-2">
                                    <img src="{{ asset('images/placeholder.png') }}" class="img-fluid rounded" alt="No Image" style="max-height: 200px; opacity: 0.5;">
                                </div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 