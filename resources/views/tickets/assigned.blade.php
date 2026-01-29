@extends('layouts.app')

@section('title', 'My Assigned Tickets')

@section('content')
<div class="container-fluid px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $totalTickets = $stats['total'];
        $openTickets = $stats['open'];
        $inProgressTickets = $stats['in_progress'];
        $completedTickets = $stats['completed'];
    @endphp
    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary stats-card">
                <div class="card-body text-center">
                    <h4>{{ $totalTickets }}</h4>
                    <p class="mb-0">Total Assigned</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info stats-card">
                <div class="card-body text-center">
                    <h4>{{ $openTickets }}</h4>
                    <p class="mb-0">Open</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning stats-card">
                <div class="card-body text-center">
                    <h4>{{ $inProgressTickets }}</h4>
                    <p class="mb-0">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success stats-card">
                <div class="card-body text-center">
                    <h4>{{ $completedTickets }}</h4>
                    <p class="mb-0">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h2><i class="fas fa-user-check me-2"></i>My Assigned Tickets</h2>
                <p class="text-muted mb-0">Tickets that have been assigned to you for handling</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-1"></i>View All Tickets
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Assigned Tickets</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('tickets.assigned') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Tickets</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Search by ticket name, description, or location..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancel" {{ request('status') == 'cancel' ? 'selected' : '' }}>Cancel</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                    <a href="{{ route('tickets.assigned') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($tickets->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Assigned Tickets List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;" class="text-center">Image</th>
                                <th style="width: 250px;">Issue Name</th>
                                <th style="width: 140px;">Location</th>
                                <th style="width: 120px;">Category</th>
                                <th style="width: 180px;">Department</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 120px;">Assigned to</th>
                                <th style="width: 120px;">Acknowledge</th>
                                <th style="width: 100px;">Assigned</th>
                                <th style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="tickets-container">
                            @foreach($tickets as $ticket)
                            <tr data-ticket-id="{{ $ticket->id }}" class="align-middle">
                                <td class="text-center">
                                    @if($ticket->image_path)
                                        <img src="{{ Storage::url($ticket->image_path) }}" 
                                             class="img-thumbnail" alt="Ticket Image" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/placeholder.png') }}" 
                                             class="img-thumbnail" alt="No Image" 
                                             style="width: 60px; height: 60px; object-fit: cover; opacity: 0.5;">
                                    @endif
                                </td>
                                <td>
                                    <div class="mb-2">
                                        <h6 class="mb-1 fw-bold text-dark">{{ Str::limit($ticket->name, 30) }}</h6>
                                        <p class="text-muted small mb-0">{{ Str::limit($ticket->description, 50) }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                        <span class="text-dark">{{ Str::limit($ticket->location, 20) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary fs-6 px-3 py-2">{{ Str::limit($ticket->category->name, 20) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info fs-6 px-3 py-2">{{ Str::limit($ticket->department->name, 25) }}</span>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge bg-{{ $ticket->status === 'completed' ? 'success' : ($ticket->status === 'in_progress' ? 'warning' : ($ticket->status === 'open' ? 'info' : 'danger')) }} status-badge fs-6 px-3 py-2">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                        @if($ticket->status === 'cancel')
                                            @php
                                                $deletionDate = $ticket->updated_at->addDays(7);
                                                $daysUntilDeletion = now()->diffInDays($deletionDate, false);
                                            @endphp
                                            <br>
                                            <small class="text-muted" title="Will be deleted on {{ $deletionDate->format('M d, Y') }}">
                                                <i class="fas fa-clock me-1"></i>{{ $daysUntilDeletion > 0 ? $daysUntilDeletion . 'd' : 'Soon' }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($ticket->accepted_by)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-check text-info me-2"></i>
                                            <span class="text-dark">{{ Str::limit($ticket->acceptedBy ? $ticket->acceptedBy->name : 'Unknown', 15) }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->acknowledgments->count() > 0)
                                        <div class="acknowledgment-count">
                                            <i class="fas fa-check-circle"></i>
                                            <span>{{ $ticket->acknowledgments->count() }}</span>
                                        </div>
                                    @elseif($ticket->status === 'completed')
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock text-warning me-2"></i>
                                            <span class="text-warning">Awaiting</span>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Not completed</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <span class="small">{{ $ticket->updated_at->format('M d') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($ticket->status !== 'completed' && $ticket->status !== 'cancel')
                                            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-outline-warning" title="Update">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($tickets->hasPages())
                    <div class="pagination-container d-flex justify-content-between align-items-center mt-4">
                        <div class="pagination-info">
                            Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} results
                        </div>
                        <nav aria-label="Tickets pagination">
                            {{ $tickets->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-user-check fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No assigned tickets found</h4>
            <p class="text-muted">You don't have any tickets assigned to you yet.</p>
            <a href="{{ route('tickets.index') }}" class="btn btn-primary">
                <i class="fas fa-list me-1"></i>View All Tickets
            </a>
        </div>
    @endif
</div>
@endsection 