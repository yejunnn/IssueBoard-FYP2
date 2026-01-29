@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-bell me-2"></i>Notifications</h2>
        </div>
        <div class="col text-end">
            @if($notifications->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline me-2">
                    @csrf
                    <button type="submit" class="btn btn-outline-success">
                        <i class="fas fa-check me-1"></i>Mark All as Read
                    </button>
                </form>
                <form action="{{ route('notifications.delete-all') }}" method="POST" class="d-inline" 
                      onsubmit="return confirm('Are you sure you want to delete all notifications? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-1"></i>Delete All
                    </button>
                </form>
            @endif
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary ms-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Tickets
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($notifications->count() > 0)
        <div class="row">
            @foreach($notifications as $notification)
                <div class="col-12 mb-3">
                    <div class="card {{ $notification->is_read ? 'border-light' : 'border-primary' }} {{ $notification->is_read ? '' : 'shadow-sm' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        @if(!$notification->is_read)
                                            <span class="badge bg-primary me-2">New</span>
                                        @endif
                                        <h6 class="card-title mb-0">{{ $notification->title }}</h6>
                                    </div>
                                    <p class="card-text text-muted mb-2">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="d-flex gap-2">
                                    @if(!$notification->is_read)
                                        <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as Read">
                                                <i class="fas fa-check me-1"></i>Mark as Read
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('notifications.delete', $notification) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this notification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            @if($notification->data && isset($notification->data['ticket_id']))
                                <div class="mt-3">
                                    <a href="{{ route('tickets.show', $notification->data['ticket_id']) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View Ticket
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($notifications->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-5">
            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No notifications</h4>
            <p class="text-muted">You're all caught up! No new notifications.</p>
        </div>
    @endif
</div>
@endsection 