@extends('layouts.app')

@section('title', 'All Tickets')

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb :items="[
        ['label' => 'Tickets', 'url' => route('tickets.index'), 'icon' => 'fas fa-ticket-alt'],
        ['label' => 'All Tickets']
    ]" />

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
    
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card bg-primary stats-card h-100">
                <div class="card-body text-center py-3">
                    <i class="fas fa-ticket-alt d-none d-md-inline-block mb-2" style="font-size: 1.5rem; opacity: 0.8;"></i>
                    <h4 class="mb-1">{{ $totalTickets }}</h4>
                    <p class="mb-0 small">Total</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-info stats-card h-100">
                <div class="card-body text-center py-3">
                    <i class="fas fa-inbox d-none d-md-inline-block mb-2" style="font-size: 1.5rem; opacity: 0.8;"></i>
                    <h4 class="mb-1">{{ $openTickets }}</h4>
                    <p class="mb-0 small">Open</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-warning stats-card h-100">
                <div class="card-body text-center py-3">
                    <i class="fas fa-spinner d-none d-md-inline-block mb-2" style="font-size: 1.5rem; opacity: 0.8;"></i>
                    <h4 class="mb-1">{{ $inProgressTickets }}</h4>
                    <p class="mb-0 small">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-success stats-card h-100">
                <div class="card-body text-center py-3">
                    <i class="fas fa-check-circle d-none d-md-inline-block mb-2" style="font-size: 1.5rem; opacity: 0.8;"></i>
                    <h4 class="mb-1">{{ $completedTickets }}</h4>
                    <p class="mb-0 small">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-header">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md">
                <h2 class="mb-1"><i class="fas fa-list me-2"></i>All Tickets</h2>
                @php $user = Auth::user(); @endphp
                @if($user && $user->department_id && !$user->is_admin)
                    <p class="text-muted mb-0 d-none d-md-block">View all tickets or <a href="{{ route('tickets.assigned') }}" class="text-decoration-none">see only your assigned tickets</a></p>
                @elseif(!$user)
                    <p class="text-muted mb-0 d-none d-md-block">Search and filter tickets to follow up on your submitted issues</p>
                @endif
            </div>
            <div class="col-12 col-md-auto">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @if($user && $user->is_admin)
                        <button type="button" class="btn btn-outline-secondary d-none d-md-inline-flex" onclick="toggleBulkActionsDropdown()" title="Bulk Actions">
                            <i class="fas fa-tasks me-1"></i><span class="d-none d-lg-inline">Bulk Actions</span>
                        </button>
                    @endif
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary flex-grow-1 flex-md-grow-0">
                        <i class="fas fa-plus me-1"></i>Create Ticket
                    </a>
                    <div class="d-none d-lg-inline-block ms-2">
                        <small class="text-muted">
                            <i class="fas fa-circle text-success me-1" id="realtime-indicator"></i>
                            <span id="realtime-status">Live</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!Auth::user())
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Search & Filter Tickets</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('tickets.index') }}" class="row g-3">
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
                        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if(Auth::user())
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Tickets</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('tickets.index') }}" class="row g-3">
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
                    <div class="col-md-3">
                        <label for="my_tickets" class="form-label">Show</label>
                        <select name="my_tickets" id="my_tickets" class="form-select">
                            <option value="">All Tickets</option>
                            <option value="1" {{ request('my_tickets') == '1' ? 'selected' : '' }}>My Created Tickets</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($tickets->count() > 0)
        <!-- Mobile Card View -->
        <div class="ticket-card-mobile d-md-none">
            @foreach($tickets as $ticket)
            <div class="ticket-card-item status-{{ $ticket->status }}" data-ticket-id="{{ $ticket->id }}">
                <div class="ticket-card-header">
                    <div class="ticket-card-title">{{ Str::limit($ticket->name, 50) }}</div>
                    <span class="badge bg-{{ $ticket->status === 'completed' ? 'success' : ($ticket->status === 'in_progress' ? 'warning' : ($ticket->status === 'open' ? 'info' : 'danger')) }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </div>
                <div class="ticket-card-body">
                    <p class="text-muted mb-2" style="font-size: 0.9rem;">{{ Str::limit($ticket->description, 80) }}</p>
                    <div class="ticket-card-meta">
                        <span><i class="fas fa-map-marker-alt"></i> {{ Str::limit($ticket->location, 20) }}</span>
                        <span><i class="fas fa-folder"></i> {{ $ticket->category->name }}</span>
                        <span><i class="fas fa-building"></i> {{ $ticket->department->name }}</span>
                    </div>
                </div>
                <div class="ticket-card-footer">
                    <div class="text-muted" style="font-size: 0.8rem;">
                        @if($ticket->accepted_by)
                            <i class="fas fa-user-check text-info"></i> {{ $ticket->acceptedBy ? $ticket->acceptedBy->name : 'Assigned' }}
                        @else
                            <i class="fas fa-clock"></i> {{ $ticket->created_at->diffForHumans() }}
                        @endif
                    </div>
                    <div class="ticket-card-actions">
                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @if($user && $user->is_admin)
                            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            @if($tickets->hasPages())
                <div class="pagination-container d-flex justify-content-center mt-3">
                    {{ $tickets->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

        <!-- Desktop Table View -->
        <div class="card ticket-table-desktop d-none d-md-block">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Tickets List</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                    <table class="table table-hover mb-0 compact-table">
                        <thead class="table-light">
                            <tr>
                                @if($user && $user->is_admin)
                                    <th width="40" class="text-center">
                                        <input type="checkbox" id="selectAll" class="form-check-input" title="Select All">
                                    </th>
                                @endif
                                <th width="50">Image</th>
                                <th width="200">Issue</th>
                                <th width="120">Location</th>
                                <th width="100">Category</th>
                                <th width="120">Department</th>
                                <th width="100">Status</th>
                                <th width="100">Assigned</th>
                                <th width="60">Ack</th>
                                <th width="80">Date</th>
                                <th width="140">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="tickets-container">
                            @foreach($tickets as $ticket)
                            <tr data-ticket-id="{{ $ticket->id }}" class="align-middle">
                                @if($user && $user->is_admin)
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input ticket-checkbox" value="{{ $ticket->id }}">
                                    </td>
                                @endif
                                <td class="text-center">
                                    @if($ticket->image_path)
                                        <img src="{{ Storage::url($ticket->image_path) }}" 
                                             alt="Ticket Image" 
                                             class="ticket-thumbnail"
                                             title="{{ $ticket->name }}"
                                             onclick="showImageModal('{{ Storage::url($ticket->image_path) }}', '{{ $ticket->name }}')">
                                    @else
                                        <div class="no-image-placeholder">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="ticket-info">
                                        <div class="ticket-name">{{ Str::limit($ticket->name, 25) }}</div>
                                        <div class="ticket-desc">{{ Str::limit($ticket->description, 35) }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="location-info">
                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                        <span>{{ Str::limit($ticket->location, 15) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary small">{{ Str::limit($ticket->category->name, 12) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info small">{{ Str::limit($ticket->department->name, 15) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $ticket->status === 'completed' ? 'success' : ($ticket->status === 'in_progress' ? 'warning' : ($ticket->status === 'open' ? 'info' : 'danger')) }} status-badge small">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    @if($ticket->status === 'cancel')
                                        <br><small class="text-muted">{{ $ticket->updated_at->addDays(7)->diffInDays(now(), false) }}d</small>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->accepted_by)
                                        <div class="assigned-info">
                                            <i class="fas fa-user-check text-info me-1"></i>
                                            <span>{{ Str::limit($ticket->acceptedBy ? $ticket->acceptedBy->name : 'Unknown', 12) }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">Not assigned</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($ticket->acknowledgments->count() > 0)
                                        <span class="badge bg-success small">{{ $ticket->acknowledgments->count() }}</span>
                                    @elseif($ticket->status === 'completed')
                                        <span class="text-warning small">Awaiting</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">{{ $ticket->created_at->format('M d') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @php $user = Auth::user(); @endphp
                                        
                                        @if($user && $user->is_admin)
                                            <div class="btn-group" role="group">
                                                @if($ticket->status !== 'cancel')
                                                    @if(!$ticket->accepted_by)
                                                        <a href="{{ route('admin.tickets.assign', $ticket) }}" class="btn btn-outline-success" title="Assign">
                                                            <i class="fas fa-user-plus"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.tickets.assign', $ticket) }}" class="btn btn-outline-warning" title="Reassign">
                                                            <i class="fas fa-user-edit"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                                
                                                <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-outline-info" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <button type="button" class="btn btn-outline-secondary" onclick="toggleDropdown({{ $ticket->id }})" title="Status">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                            </div>
                                        @elseif($user && $user->department_id && !$user->is_admin && $ticket->department_id == $user->department_id && $ticket->status === 'open')
                                            <form action="{{ route('department.tickets.accept', ['ticket_id' => $ticket->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="Accept">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($ticket->status === 'completed')
                                            @php
                                                $canAcknowledge = true;
                                                if ($user && $user->department_id && !$user->is_admin) {
                                                    $canAcknowledge = false;
                                                }
                                            @endphp
                                            @if($canAcknowledge && !$ticket->acknowledged_by)
                                                <button type="button" class="btn btn-success" onclick="acknowledgeTicket({{ $ticket->id }})" title="Acknowledge" id="ack-btn-{{ $ticket->id }}">
                                                        <i class="fas fa-check-double"></i>
                                                    </button>
                                            @elseif($canAcknowledge && $ticket->acknowledged_by)
                                                <button type="button" class="btn btn-secondary" disabled title="Already Acknowledged" id="ack-btn-{{ $ticket->id }}">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            @endif
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
            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No tickets found</h4>
            <p class="text-muted">Be the first to create a ticket for a campus issue!</p>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Create First Ticket
            </a>
        </div>
    @endif
</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Ticket Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Ticket Image" class="img-fluid" style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Ensure dropdowns are properly positioned and visible
document.addEventListener('DOMContentLoaded', function() {
    // Handle dropdown positioning
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdownMenu = this.nextElementSibling;
            if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                // Close all other dropdowns first
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                    }
                });
                
                // Toggle current dropdown
                const isVisible = dropdownMenu.classList.contains('show');
                if (isVisible) {
                    dropdownMenu.classList.remove('show');
                } else {
                    dropdownMenu.classList.add('show');
                    
                    // Force the dropdown to be visible and positioned correctly
                    dropdownMenu.style.zIndex = '99999';
                    dropdownMenu.style.position = 'absolute';
                    dropdownMenu.style.display = 'block';
                    dropdownMenu.style.background = 'white';
                    dropdownMenu.style.transform = 'none';
                    
                    // Check if this is the last row
                    const currentRow = this.closest('tr');
                    const tbody = currentRow.parentElement;
                    const isLastRow = currentRow && currentRow === tbody.lastElementChild;
                    
                    if (isLastRow) {
                        // Position above for last row
                        dropdownMenu.style.top = 'auto';
                        dropdownMenu.style.bottom = '100%';
                        dropdownMenu.style.marginTop = '0';
                        dropdownMenu.style.marginBottom = '5px';
                        dropdownMenu.style.left = 'auto';
                        dropdownMenu.style.right = '0';
                    } else {
                        // Position below for all other rows
                        dropdownMenu.style.top = '100%';
                        dropdownMenu.style.bottom = 'auto';
                        dropdownMenu.style.marginTop = '2px';
                        dropdownMenu.style.marginBottom = '0';
                        dropdownMenu.style.left = 'auto';
                        dropdownMenu.style.right = '0';
                    }
                }
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
});

// Bulk actions functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const ticketCheckboxes = document.querySelectorAll('.ticket-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            ticketCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Update select all when individual checkboxes change
    ticketCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(ticketCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(ticketCheckboxes).some(cb => cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        });
    });
});

function getSelectedTicketIds() {
    const checkboxes = document.querySelectorAll('.ticket-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function toggleBulkActionsDropdown() {
    console.log('toggleBulkActionsDropdown called');
    
    // Close all other dropdowns first
    const existingDropdowns = document.querySelectorAll('.dynamic-dropdown, .bulk-actions-dropdown');
    existingDropdowns.forEach(dropdown => {
        dropdown.remove();
    });
    
    // Check if dropdown is already open
    const existingDropdown = document.getElementById('bulk-actions-dropdown');
    if (existingDropdown) {
        existingDropdown.remove();
        console.log('Closing existing bulk actions dropdown');
        return;
    }
    
    // Get the button position
    const button = document.querySelector('button[onclick="toggleBulkActionsDropdown()"]');
    console.log('Bulk actions button element:', button);
    
    if (button) {
        const buttonRect = button.getBoundingClientRect();
        console.log('Button position:', buttonRect);
        
        // Create dropdown HTML
        const dropdownHTML = `
            <div id="bulk-actions-dropdown" class="bulk-actions-dropdown" style="
                position: fixed;
                top: ${buttonRect.bottom + 5}px;
                left: ${buttonRect.left}px;
                z-index: 99999999;
                background-color: white;
                border: 1px solid rgba(0,0,0,0.15);
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.175);
                min-width: 250px;
                border-radius: 0.375rem;
                padding: 0.5rem 0;
                margin: 0;
                list-style: none;
                font-family: inherit;
                font-size: 0.875rem;
            ">
                <div style="padding: 0.5rem 1rem; margin-bottom: 0; font-size: 0.875rem; color: #6c757d; white-space: nowrap; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.1);">Bulk Status Update</div>
                <div class="dropdown-item" onclick="bulkUpdateStatus('open'); closeBulkActionsDropdown();" style="display: block; width: 100%; padding: 0.5rem 1rem; clear: both; font-weight: 400; color: #212529; text-align: inherit; text-decoration: none; white-space: nowrap; background-color: transparent; border: 0; cursor: pointer;">
                    <i class="fas fa-circle text-info me-2"></i>Mark Selected as Open
                </div>
                <div class="dropdown-item" onclick="bulkUpdateStatus('in_progress'); closeBulkActionsDropdown();" style="display: block; width: 100%; padding: 0.5rem 1rem; clear: both; font-weight: 400; color: #212529; text-align: inherit; text-decoration: none; white-space: nowrap; background-color: transparent; border: 0; cursor: pointer;">
                    <i class="fas fa-circle text-warning me-2"></i>Mark Selected as In Progress
                </div>
                <div class="dropdown-item" onclick="bulkUpdateStatus('completed'); closeBulkActionsDropdown();" style="display: block; width: 100%; padding: 0.5rem 1rem; clear: both; font-weight: 400; color: #212529; text-align: inherit; text-decoration: none; white-space: nowrap; background-color: transparent; border: 0; cursor: pointer;">
                    <i class="fas fa-circle text-success me-2"></i>Mark Selected as Completed
                </div>
                <div style="height: 0; margin: 0.5rem 0; border: 0; border-top: 1px solid rgba(0,0,0,0.1);"></div>
                <div class="dropdown-item" onclick="bulkCancelTickets(); closeBulkActionsDropdown();" style="display: block; width: 100%; padding: 0.5rem 1rem; clear: both; font-weight: 400; color: #dc3545; text-align: inherit; text-decoration: none; white-space: nowrap; background-color: transparent; border: 0; cursor: pointer;">
                    <i class="fas fa-times me-2"></i>Cancel Selected Tickets
                </div>
            </div>
        `;
        
        // Create and append dropdown to body
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = dropdownHTML;
        const dropdownElement = tempDiv.firstElementChild;
        
        // Add hover effects
        const dropdownItems = dropdownElement.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
                this.style.color = this.style.color === '#dc3545' ? '#dc3545' : '#1e2125';
            });
            item.addEventListener('mouseleave', function() {
                this.style.backgroundColor = 'transparent';
                this.style.color = this.style.color === '#dc3545' ? '#dc3545' : '#212529';
            });
        });
        
        document.body.appendChild(dropdownElement);
        
        // Check if dropdown would go off-screen
        const dropdownRect = dropdownElement.getBoundingClientRect();
        const viewportHeight = window.innerHeight;
        
        if (dropdownRect.bottom > viewportHeight) {
            // Position above the button if it would go off-screen
            dropdownElement.style.top = (buttonRect.top - dropdownElement.offsetHeight - 5) + 'px';
            console.log('Positioning bulk actions dropdown above button');
        }
        
        // Ensure it's not cut off horizontally
        if (dropdownRect.right > window.innerWidth) {
            dropdownElement.style.left = (window.innerWidth - dropdownElement.offsetWidth - 10) + 'px';
            console.log('Adjusting horizontal position');
        }
        
        console.log('Created bulk actions dropdown at position:', dropdownElement.style.top, dropdownElement.style.left);
    } else {
        console.error('Bulk actions button not found');
    }
}

function closeBulkActionsDropdown() {
    const dropdown = document.getElementById('bulk-actions-dropdown');
    if (dropdown) {
        dropdown.remove();
    }
}

function bulkUpdateStatus(status) {
    const ticketIds = getSelectedTicketIds();
    if (ticketIds.length === 0) {
        showToast('Warning', 'Please select at least one ticket.', 'warning');
        return;
    }
    
    if (confirm(`Are you sure you want to update ${ticketIds.length} ticket(s) to "${status}"?`)) {
        // Show loading state for all selected tickets
        const selectedRows = document.querySelectorAll('.ticket-checkbox:checked');
        const statusBadges = [];
        const originalStatuses = [];
        const originalClasses = [];
        
        selectedRows.forEach(checkbox => {
            const ticketRow = checkbox.closest('tr');
            const statusBadge = ticketRow?.querySelector('.status-badge');
            if (statusBadge) {
                statusBadges.push(statusBadge);
                originalStatuses.push(statusBadge.textContent.trim());
                originalClasses.push(statusBadge.className);
                
                // Show loading state
                statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
                statusBadge.className = 'badge bg-secondary status-badge small';
            }
        });
        
        // Prepare the request data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('status', status);
        formData.append('ticket_ids', JSON.stringify(ticketIds));
        
        // Send AJAX request
        fetch('{{ route("admin.tickets.bulk-status") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Update all status badges
            const statusText = status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            let badgeClass = 'badge status-badge small ';
            
            switch(status) {
                case 'open':
                    badgeClass += 'bg-info';
                    break;
                case 'in_progress':
                    badgeClass += 'bg-warning';
                    break;
                case 'completed':
                    badgeClass += 'bg-success';
                    break;
                default:
                    badgeClass += 'bg-secondary';
            }
            
            statusBadges.forEach(statusBadge => {
                statusBadge.textContent = statusText;
                statusBadge.className = badgeClass;
            });
            
            // Show success message
            showToast('Success', `Successfully updated ${ticketIds.length} ticket(s) to ${statusText}`, 'success');
            
            // Uncheck all checkboxes
            selectedRows.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Update select all checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        })
        .catch(error => {
            console.error('Error updating bulk ticket status:', error);
            
            // Revert all status badges to original state
            statusBadges.forEach((statusBadge, index) => {
                statusBadge.textContent = originalStatuses[index];
                statusBadge.className = originalClasses[index];
            });
            
            // Show error message
            showToast('Error', 'Failed to update ticket statuses. Please try again.', 'error');
        });
    }
}

function bulkCancelTickets() {
    const ticketIds = getSelectedTicketIds();
    if (ticketIds.length === 0) {
        showToast('Warning', 'Please select at least one ticket.', 'warning');
        return;
    }
    
    if (confirm(`Are you sure you want to cancel ${ticketIds.length} ticket(s)?`)) {
        // Show loading state for all selected tickets
        const selectedRows = document.querySelectorAll('.ticket-checkbox:checked');
        const statusBadges = [];
        const originalStatuses = [];
        const originalClasses = [];
        
        selectedRows.forEach(checkbox => {
            const ticketRow = checkbox.closest('tr');
            const statusBadge = ticketRow?.querySelector('.status-badge');
            if (statusBadge) {
                statusBadges.push(statusBadge);
                originalStatuses.push(statusBadge.textContent.trim());
                originalClasses.push(statusBadge.className);
                
                // Show loading state
                statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Cancelling...';
                statusBadge.className = 'badge bg-secondary status-badge small';
            }
        });
        
        // Prepare the request data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('ticket_ids', JSON.stringify(ticketIds));
        
        // Send AJAX request
        fetch('{{ route("admin.tickets.bulk-cancel") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Update all status badges to cancelled
            statusBadges.forEach(statusBadge => {
                statusBadge.textContent = 'Cancel';
                statusBadge.className = 'badge bg-danger status-badge small';
            });
            
            // Show success message
            showToast('Success', `Successfully cancelled ${ticketIds.length} ticket(s)`, 'success');
            
            // Uncheck all checkboxes
            selectedRows.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Update select all checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        })
        .catch(error => {
            console.error('Error cancelling bulk tickets:', error);
            
            // Revert all status badges to original state
            statusBadges.forEach((statusBadge, index) => {
                statusBadge.textContent = originalStatuses[index];
                statusBadge.className = originalClasses[index];
            });
            
            // Show error message
            showToast('Error', 'Failed to cancel tickets. Please try again.', 'error');
        });
    }
}

function updateTicketStatus(ticketId, status) {
    if (confirm(`Are you sure you want to change the status to "${status}"?`)) {
        // Find the status badge element
        const ticketRow = document.querySelector(`tr[data-ticket-id="${ticketId}"]`);
        const statusBadge = ticketRow?.querySelector('.status-badge');
        
        if (!statusBadge) {
            console.error('Status badge not found for ticket:', ticketId);
            return;
        }
        
        // Store original status for rollback if needed
        const originalStatus = statusBadge.textContent.trim();
        const originalClasses = statusBadge.className;
        
        // Show loading state
        statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
        statusBadge.className = 'badge bg-secondary status-badge small';
        
        // Prepare the request data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('status', status);
        
        // Send AJAX request
        fetch(`/tickets/${ticketId}/status`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Update the status badge with new status
            const statusText = status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            let badgeClass = 'badge status-badge small ';
            
            switch(status) {
                case 'open':
                    badgeClass += 'bg-info';
                    break;
                case 'in_progress':
                    badgeClass += 'bg-warning';
                    break;
                case 'completed':
                    badgeClass += 'bg-success';
                    break;
                case 'cancel':
                    badgeClass += 'bg-danger';
                    break;
                default:
                    badgeClass += 'bg-secondary';
            }
            
            statusBadge.textContent = statusText;
            statusBadge.className = badgeClass;
            
            // Show success message
            showToast('Success', `Ticket status updated to ${statusText}`, 'success');
            
            // Close the dropdown
            const dropdown = document.getElementById(`dynamic-dropdown-${ticketId}`);
            if (dropdown) {
                dropdown.remove();
            }
        })
        .catch(error => {
            console.error('Error updating ticket status:', error);
            
            // Revert to original status
            statusBadge.textContent = originalStatus;
            statusBadge.className = originalClasses;
            
            // Show error message
            showToast('Error', 'Failed to update ticket status. Please try again.', 'error');
        });
    }
}

// Toast notification function
function showToast(title, message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999999;
            max-width: 350px;
        `;
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.style.cssText = `
        background: ${type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1'};
        color: ${type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#0c5460'};
        border: 1px solid ${type === 'success' ? '#c3e6cb' : type === 'error' ? '#f5c6cb' : '#bee5eb'};
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        font-size: 0.875rem;
        animation: slideInRight 0.3s ease-out;
    `;
    
    toast.innerHTML = `
        <div style="font-weight: 600; margin-bottom: 0.25rem;">${title}</div>
        <div>${message}</div>
    `;
    
    // Add animation CSS if not already present
    if (!document.getElementById('toast-animations')) {
        const style = document.createElement('style');
        style.id = 'toast-animations';
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
    
    toastContainer.appendChild(toast);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

function confirmCancelTicket(ticketId, ticketName) {
    if (confirm(`Are you sure you want to cancel ticket "${ticketName}"?`)) {
        // Find the status badge element
        const ticketRow = document.querySelector(`tr[data-ticket-id="${ticketId}"]`);
        const statusBadge = ticketRow?.querySelector('.status-badge');
        
        if (!statusBadge) {
            console.error('Status badge not found for ticket:', ticketId);
            return;
        }
        
        // Store original status for rollback if needed
        const originalStatus = statusBadge.textContent.trim();
        const originalClasses = statusBadge.className;
        
        // Show loading state
        statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Cancelling...';
        statusBadge.className = 'badge bg-secondary status-badge small';
        
        // Prepare the request data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        
        // Send AJAX request
        fetch(`/tickets/${ticketId}/cancel`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Update the status badge to cancelled
            statusBadge.textContent = 'Cancel';
            statusBadge.className = 'badge bg-danger status-badge small';
            
            // Show success message
            showToast('Success', `Ticket "${ticketName}" has been cancelled`, 'success');
            
            // Close the dropdown
            const dropdown = document.getElementById(`dynamic-dropdown-${ticketId}`);
            if (dropdown) {
                dropdown.remove();
            }
        })
        .catch(error => {
            console.error('Error cancelling ticket:', error);
            
            // Revert to original status
            statusBadge.textContent = originalStatus;
            statusBadge.className = originalClasses;
            
            // Show error message
            showToast('Error', 'Failed to cancel ticket. Please try again.', 'error');
        });
    }
}

function confirmRestoreTicket(ticketId, ticketName) {
    if (confirm(`Are you sure you want to restore ticket "${ticketName}"?`)) {
        // Find the status badge element
        const ticketRow = document.querySelector(`tr[data-ticket-id="${ticketId}"]`);
        const statusBadge = ticketRow?.querySelector('.status-badge');
        
        if (!statusBadge) {
            console.error('Status badge not found for ticket:', ticketId);
            return;
        }
        
        // Store original status for rollback if needed
        const originalStatus = statusBadge.textContent.trim();
        const originalClasses = statusBadge.className;
        
        // Show loading state
        statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Restoring...';
        statusBadge.className = 'badge bg-secondary status-badge small';
        
        // Prepare the request data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        
        // Send AJAX request
        fetch(`/tickets/${ticketId}/restore`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Update the status badge to open
            statusBadge.textContent = 'Open';
            statusBadge.className = 'badge bg-info status-badge small';
            
            // Show success message
            showToast('Success', `Ticket "${ticketName}" has been restored`, 'success');
            
            // Close the dropdown
            const dropdown = document.getElementById(`dynamic-dropdown-${ticketId}`);
            if (dropdown) {
                dropdown.remove();
            }
        })
        .catch(error => {
            console.error('Error restoring ticket:', error);
            
            // Revert to original status
            statusBadge.textContent = originalStatus;
            statusBadge.className = originalClasses;
            
            // Show error message
            showToast('Error', 'Failed to restore ticket. Please try again.', 'error');
        });
    }
}

function showImageModal(imageSrc, ticketName) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('imageModalLabel');
    
    modalImage.src = imageSrc;
    modalTitle.textContent = `Ticket Image - ${ticketName}`;
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

function toggleDropdown(ticketId) {
    console.log('toggleDropdown called for ticket:', ticketId);
    
    // Close all other dropdowns first
    const existingDropdowns = document.querySelectorAll('.dynamic-dropdown');
    existingDropdowns.forEach(dropdown => {
        dropdown.remove();
    });
    
    // Check if dropdown is already open for this ticket
    const existingDropdown = document.getElementById(`dynamic-dropdown-${ticketId}`);
    if (existingDropdown) {
        existingDropdown.remove();
        console.log('Closing existing dropdown');
        return;
    }
    
    // Get the button position
    const button = document.querySelector(`button[onclick="toggleDropdown(${ticketId})"]`);
    console.log('Button element:', button);
    
    if (button) {
        const buttonRect = button.getBoundingClientRect();
        console.log('Button position:', buttonRect);
        
        // Create dropdown content based on ticket status
        const ticketStatus = button.closest('tr').querySelector('.status-badge')?.textContent?.trim().toLowerCase() || 'open';
        console.log('Ticket status:', ticketStatus);
        
        // Create dropdown HTML
        let dropdownHTML = `
            <div id="dynamic-dropdown-${ticketId}" class="dynamic-dropdown" style="
                position: fixed;
                top: ${buttonRect.bottom + 5}px;
                left: ${buttonRect.left}px;
                z-index: 99999999;
                background-color: white;
                border: 1px solid rgba(0,0,0,0.15);
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.175);
                min-width: 200px;
                border-radius: 0.375rem;
                padding: 0.5rem 0;
                margin: 0;
                list-style: none;
                font-family: inherit;
                font-size: 0.875rem;
            ">
                <div style="padding: 0.5rem 1rem; margin-bottom: 0; font-size: 0.875rem; color: #6c757d; white-space: nowrap; font-weight: 600; border-bottom: 1px solid rgba(0,0,0,0.1);">Quick Status</div>
        `;
        
        // Add status options
        if (ticketStatus !== 'open') {
            dropdownHTML += `
                <div class="dropdown-item" onclick="updateTicketStatus(${ticketId}, 'open'); closeDropdown(${ticketId});" style="display: block; width: 100%; padding: 0.5rem 1rem; clear: both; font-weight: 400; color: #212529; text-align: inherit; text-decoration: none; white-space: nowrap; background-color: transparent; border: 0; cursor: pointer;">
                    <i class="fas fa-circle text-info me-2"></i>Open
                </div>
            `;
        }
        
        if (ticketStatus !== 'in_progress') {
            dropdownHTML += `
                <div class="dropdown-item" onclick="updateTicketStatus(${ticketId}, 'in_progress'); closeDropdown(${ticketId});" style="display: block; width: 100%; padding: 0.5rem 1rem; clear: both; font-weight: 400; color: #212529; text-align: inherit; text-decoration: none; white-space: nowrap; background-color: transparent; border: 0; cursor: pointer;">
                    <i class="fas fa-circle text-warning me-2"></i>In Progress
                </div>
            `;
        }
        
        if (ticketStatus !== 'completed') {
            dropdownHTML += `
                <div class="dropdown-item" onclick="updateTicketStatus(${ticketId}, 'completed'); closeDropdown(${ticketId});" style="display: block; width: 100%; padding: 0.5rem 1rem; clear: both; font-weight: 400; color: #212529; text-align: inherit; text-decoration: none; white-space: nowrap; background-color: transparent; border: 0; cursor: pointer;">
                    <i class="fas fa-circle text-success me-2"></i>Completed
                </div>
            `;
        }
        
        dropdownHTML += `
            <div style="height: 0; margin: 0.5rem 0; border: 0; border-top: 1px solid rgba(0,0,0,0.1);"></div>
        `;
        
        if (ticketStatus !== 'cancel') {
            dropdownHTML += `
                <div class="dropdown-item" onclick="confirmCancelTicket(${ticketId}, '${button.closest('tr').querySelector('.ticket-name')?.textContent || 'Ticket'}'); closeDropdown(${ticketId});" style="display: block; width: 100%; padding: 0.5rem 1rem; clear: both; font-weight: 400; color: #dc3545; text-align: inherit; text-decoration: none; white-space: nowrap; background-color: transparent; border: 0; cursor: pointer;">
                    <i class="fas fa-times me-2"></i>Cancel
                </div>
            `;
        } else {
            dropdownHTML += `
                <div class="dropdown-item" onclick="confirmRestoreTicket(${ticketId}, '${button.closest('tr').querySelector('.ticket-name')?.textContent || 'Ticket'}'); closeDropdown(${ticketId});" style="display: block; width: 100%; padding: 0.5rem 1rem; clear: both; font-weight: 400; color: #198754; text-align: inherit; text-decoration: none; white-space: nowrap; background-color: transparent; border: 0; cursor: pointer;">
                    <i class="fas fa-undo me-2"></i>Restore
                </div>
            `;
        }
        
        dropdownHTML += '</div>';
        
        // Create and append dropdown to body
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = dropdownHTML;
        const dropdownElement = tempDiv.firstElementChild;
        
        // Add hover effects
        const dropdownItems = dropdownElement.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
                this.style.color = '#1e2125';
            });
            item.addEventListener('mouseleave', function() {
                this.style.backgroundColor = 'transparent';
                this.style.color = this.style.color === '#dc3545' ? '#dc3545' : 
                                  this.style.color === '#198754' ? '#198754' : '#212529';
            });
        });
        
        document.body.appendChild(dropdownElement);
        
        // Check if dropdown would go off-screen
        const dropdownRect = dropdownElement.getBoundingClientRect();
        const viewportHeight = window.innerHeight;
        
        if (dropdownRect.bottom > viewportHeight) {
            // Position above the button if it would go off-screen
            dropdownElement.style.top = (buttonRect.top - dropdownElement.offsetHeight - 5) + 'px';
            console.log('Positioning dropdown above button');
        }
        
        // Ensure it's not cut off horizontally
        if (dropdownRect.right > window.innerWidth) {
            dropdownElement.style.left = (window.innerWidth - dropdownElement.offsetWidth - 10) + 'px';
            console.log('Adjusting horizontal position');
        }
        
        console.log('Created dynamic dropdown at position:', dropdownElement.style.top, dropdownElement.style.left);
    } else {
        console.error('Button not found for ticket:', ticketId);
    }
}

function closeDropdown(ticketId) {
    const dropdown = document.getElementById(`dynamic-dropdown-${ticketId}`);
    if (dropdown) {
        dropdown.remove();
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dynamic-dropdown') && !event.target.closest('button[onclick*="toggleDropdown"]') &&
        !event.target.closest('.bulk-actions-dropdown') && !event.target.closest('button[onclick*="toggleBulkActionsDropdown"]')) {
        const allDropdowns = document.querySelectorAll('.dynamic-dropdown, .bulk-actions-dropdown');
        allDropdowns.forEach(dropdown => {
            dropdown.remove();
        });
    }
});

// Real-time updates system
let lastUpdateTime = new Date().toISOString();
let updateInterval;
let isUpdating = false;

// Start real-time updates
function startRealTimeUpdates() {
    // Check for updates every 3 seconds
    updateInterval = setInterval(checkForUpdates, 3000);
    console.log('Real-time updates started');
    
    // Update UI indicator
    const indicator = document.getElementById('realtime-indicator');
    const status = document.getElementById('realtime-status');
    if (indicator) {
        indicator.className = 'fas fa-circle text-success me-1';
        indicator.style.animation = 'pulse 2s infinite';
    }
    if (status) {
        status.textContent = 'Live Updates Active';
    }
}

// Stop real-time updates
function stopRealTimeUpdates() {
    if (updateInterval) {
        clearInterval(updateInterval);
        updateInterval = null;
        console.log('Real-time updates stopped');
    }
    
    // Update UI indicator
    const indicator = document.getElementById('realtime-indicator');
    const status = document.getElementById('realtime-status');
    if (indicator) {
        indicator.className = 'fas fa-circle text-muted me-1';
        indicator.style.animation = 'none';
    }
    if (status) {
        status.textContent = 'Updates Paused';
    }
}

// Check for updates from server
function checkForUpdates() {
    if (isUpdating) return; // Prevent multiple simultaneous requests
    
    isUpdating = true;
    
    fetch(`{{ route('tickets.index') }}?ajax=1&last_update=${encodeURIComponent(lastUpdateTime)}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.has_updates) {
            console.log('Updates detected, refreshing ticket list...');
            updateTicketList(data.tickets);
            lastUpdateTime = new Date().toISOString();
            
            // Show notification if there are new updates
            if (data.new_tickets_count > 0) {
                showToast('New Tickets', `${data.new_tickets_count} new ticket(s) added`, 'info');
            }
            if (data.updated_tickets_count > 0) {
                showToast('Updates', `${data.updated_tickets_count} ticket(s) updated`, 'info');
            }
        }
    })
    .catch(error => {
        console.error('Error checking for updates:', error);
    })
    .finally(() => {
        isUpdating = false;
    });
}

// Update the ticket list with new data
function updateTicketList(tickets) {
    const tbody = document.querySelector('.tickets-container');
    if (!tbody) return;
    
    // Store current scroll position
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Update each ticket row
    tickets.forEach(ticket => {
        const existingRow = document.querySelector(`tr[data-ticket-id="${ticket.id}"]`);
        
        if (existingRow) {
            // Update existing row
            updateTicketRow(existingRow, ticket);
        } else {
            // Add new row (if it's a new ticket)
            const newRow = createTicketRow(ticket);
            tbody.insertBefore(newRow, tbody.firstChild);
            
            // Add highlight effect for new tickets
            newRow.style.backgroundColor = '#fff3cd';
            setTimeout(() => {
                newRow.style.backgroundColor = '';
                newRow.style.transition = 'background-color 0.5s ease';
            }, 2000);
        }
    });
    
    // Restore scroll position
    window.scrollTo(0, scrollTop);
    
    // Update statistics if they exist
    updateStatistics();
}

// Update a single ticket row
function updateTicketRow(row, ticket) {
    // Update status badge
    const statusBadge = row.querySelector('.status-badge');
    if (statusBadge) {
        const newStatusText = ticket.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        let newBadgeClass = 'badge status-badge small ';
        
        switch(ticket.status) {
            case 'open':
                newBadgeClass += 'bg-info';
                break;
            case 'in_progress':
                newBadgeClass += 'bg-warning';
                break;
            case 'completed':
                newBadgeClass += 'bg-success';
                break;
            case 'cancel':
                newBadgeClass += 'bg-danger';
                break;
            default:
                newBadgeClass += 'bg-secondary';
        }
        
        // Only update if status actually changed
        if (statusBadge.textContent.trim() !== newStatusText) {
            statusBadge.textContent = newStatusText;
            statusBadge.className = newBadgeClass;
            
            // Add highlight effect for status changes
            row.style.backgroundColor = '#d1ecf1';
            setTimeout(() => {
                row.style.backgroundColor = '';
                row.style.transition = 'background-color 0.5s ease';
            }, 1500);
        }
    }
    
    // Update assigned user
    const assignedCell = row.querySelector('td:nth-child(8)'); // Assigned column
    if (assignedCell) {
        const newAssignedText = ticket.accepted_by ? 
            `<div class="assigned-info"><i class="fas fa-user-check text-info me-1"></i><span>${ticket.accepted_by_name || 'Unknown'}</span></div>` :
            '<span class="text-muted small">Not assigned</span>';
        
        if (assignedCell.innerHTML !== newAssignedText) {
            assignedCell.innerHTML = newAssignedText;
        }
    }
    
    // Update acknowledgment count
    const ackCell = row.querySelector('td:nth-child(9)'); // ACK column
    if (ackCell) {
        let newAckText = '';
        if (ticket.acknowledgments_count > 0) {
            newAckText = `<span class="badge bg-success small">${ticket.acknowledgments_count}</span>`;
        } else if (ticket.status === 'completed') {
            newAckText = '<span class="text-warning small">Awaiting</span>';
        } else {
            newAckText = '<span class="text-muted small">-</span>';
        }
        
        if (ackCell.innerHTML !== newAckText) {
            ackCell.innerHTML = newAckText;
        }
    }
    
    // Update acknowledgment button
    updateAcknowledgmentButton(row, ticket);
}

// Update acknowledgment button based on ticket status and acknowledgment state
function updateAcknowledgmentButton(row, ticket) {
    const actionsCell = row.querySelector('td:last-child');
    if (!actionsCell) return;
    
    const existingAckBtn = actionsCell.querySelector(`#ack-btn-${ticket.id}`);
    const isAdmin = {{ Auth::user() && Auth::user()->is_admin ? 'true' : 'false' }};
    const isDepartmentUser = {{ Auth::user() && Auth::user()->department_id && !Auth::user()->is_admin ? 'true' : 'false' }};
    
    // Only show acknowledgment button for admins and anonymous users (not department users)
    const canAcknowledge = isAdmin || (!isAdmin && !isDepartmentUser);
    
    if (ticket.status === 'completed' && canAcknowledge) {
        if (!existingAckBtn) {
            // Create new acknowledgment button
            const btnGroup = actionsCell.querySelector('.btn-group');
            if (btnGroup) {
                const ackBtn = document.createElement('button');
                ackBtn.id = `ack-btn-${ticket.id}`;
                ackBtn.type = 'button';
                ackBtn.className = 'btn btn-success';
                ackBtn.onclick = () => acknowledgeTicket(ticket.id);
                ackBtn.title = 'Acknowledge';
                ackBtn.innerHTML = '<i class="fas fa-check-double"></i>';
                btnGroup.appendChild(ackBtn);
            }
        } else {
            // Update existing button
            if (ticket.acknowledgments_count > 0) {
                existingAckBtn.className = 'btn btn-secondary';
                existingAckBtn.disabled = true;
                existingAckBtn.title = 'Already Acknowledged';
                existingAckBtn.innerHTML = '<i class="fas fa-check-circle"></i>';
                existingAckBtn.onclick = null;
            } else {
                existingAckBtn.className = 'btn btn-success';
                existingAckBtn.disabled = false;
                existingAckBtn.title = 'Acknowledge';
                existingAckBtn.innerHTML = '<i class="fas fa-check-double"></i>';
                existingAckBtn.onclick = () => acknowledgeTicket(ticket.id);
            }
        }
    } else if (existingAckBtn) {
        // Remove acknowledgment button if ticket is not completed or user can't acknowledge
        existingAckBtn.remove();
    }
}

// Create a new ticket row (for new tickets)
function createTicketRow(ticket) {
    const row = document.createElement('tr');
    row.setAttribute('data-ticket-id', ticket.id);
    row.className = 'align-middle';
    
    // Create row content based on current user permissions
    const isAdmin = {{ Auth::user() && Auth::user()->is_admin ? 'true' : 'false' }};
    const user = {{ Auth::user() ? 'true' : 'false' }};
    
    let rowContent = '';
    
    // Checkbox column (admin only)
    if (isAdmin) {
        rowContent += `
            <td class="text-center">
                <input type="checkbox" class="form-check-input ticket-checkbox" value="${ticket.id}">
            </td>
        `;
    }
    
    // Image column
    if (ticket.image_path) {
        rowContent += `
            <td class="text-center">
                <img src="/storage/${ticket.image_path}" 
                     alt="Ticket Image" 
                     class="ticket-thumbnail"
                     title="${ticket.name}"
                     onclick="showImageModal('/storage/${ticket.image_path}', '${ticket.name}')">
            </td>
        `;
    } else {
        rowContent += `
            <td class="text-center">
                <div class="no-image-placeholder">
                    <i class="fas fa-image text-muted"></i>
                </div>
            </td>
        `;
    }
    
    // Issue column
    rowContent += `
        <td>
            <div class="ticket-info">
                <div class="ticket-name">${ticket.name.length > 25 ? ticket.name.substring(0, 25) + '...' : ticket.name}</div>
                <div class="ticket-desc">${ticket.description.length > 35 ? ticket.description.substring(0, 35) + '...' : ticket.description}</div>
            </div>
        </td>
    `;
    
    // Location column
    rowContent += `
        <td>
            <div class="location-info">
                <i class="fas fa-map-marker-alt text-muted me-1"></i>
                <span>${ticket.location.length > 15 ? ticket.location.substring(0, 15) + '...' : ticket.location}</span>
            </div>
        </td>
    `;
    
    // Category column
    rowContent += `
        <td>
            <span class="badge bg-secondary small">${ticket.category_name.length > 12 ? ticket.category_name.substring(0, 12) + '...' : ticket.category_name}</span>
        </td>
    `;
    
    // Department column
    rowContent += `
        <td>
            <span class="badge bg-info small">${ticket.department_name.length > 15 ? ticket.department_name.substring(0, 15) + '...' : ticket.department_name}</span>
        </td>
    `;
    
    // Status column
    const statusText = ticket.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    let statusClass = 'badge bg-';
    switch(ticket.status) {
        case 'completed':
            statusClass += 'success';
            break;
        case 'in_progress':
            statusClass += 'warning';
            break;
        case 'open':
            statusClass += 'info';
            break;
        case 'cancel':
            statusClass += 'danger';
            break;
        default:
            statusClass += 'secondary';
    }
    
    rowContent += `
        <td>
            <span class="${statusClass} status-badge small">${statusText}</span>
            ${ticket.status === 'cancel' ? '<br><small class="text-muted">-</small>' : ''}
        </td>
    `;
    
    // Assigned column
    if (ticket.accepted_by) {
        rowContent += `
            <td>
                <div class="assigned-info">
                    <i class="fas fa-user-check text-info me-1"></i>
                    <span>${ticket.accepted_by_name ? (ticket.accepted_by_name.length > 12 ? ticket.accepted_by_name.substring(0, 12) + '...' : ticket.accepted_by_name) : 'Unknown'}</span>
                </div>
            </td>
        `;
    } else {
        rowContent += `
            <td>
                <span class="text-muted small">Not assigned</span>
            </td>
        `;
    }
    
    // ACK column
    if (ticket.acknowledgments_count > 0) {
        rowContent += `
            <td class="text-center">
                <span class="badge bg-success small">${ticket.acknowledgments_count}</span>
            </td>
        `;
    } else if (ticket.status === 'completed') {
        rowContent += `
            <td class="text-center">
                <span class="text-warning small">Awaiting</span>
            </td>
        `;
    } else {
        rowContent += `
            <td class="text-center">
                <span class="text-muted small">-</span>
            </td>
        `;
    }
    
    // Date column
    const date = new Date(ticket.created_at);
    const dateText = date.toLocaleDateString('en-US', { month: 'short', day: '2-digit' });
    rowContent += `
        <td class="text-center">
            <small class="text-muted">${dateText}</small>
        </td>
    `;
    
    // Actions column
    rowContent += `
        <td>
            <div class="btn-group" role="group">
                <a href="/tickets/${ticket.id}" class="btn btn-outline-primary" title="View">
                    <i class="fas fa-eye"></i>
                </a>
    `;
    
    // Admin actions
    if (isAdmin) {
        if (ticket.status !== 'cancel') {
            if (!ticket.accepted_by) {
                rowContent += `
                    <a href="/admin/tickets/${ticket.id}/assign" class="btn btn-outline-success" title="Assign">
                        <i class="fas fa-user-plus"></i>
                    </a>
                `;
            } else {
                rowContent += `
                    <a href="/admin/tickets/${ticket.id}/assign" class="btn btn-outline-warning" title="Reassign">
                        <i class="fas fa-user-edit"></i>
                    </a>
                `;
            }
        }
        
        rowContent += `
            <a href="/tickets/${ticket.id}/edit" class="btn btn-outline-secondary" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" class="btn btn-outline-secondary" onclick="toggleDropdown(${ticket.id})" title="Status">
                <i class="fas fa-cog"></i>
            </button>
        `;
    }
    
    rowContent += `
            </div>
        </td>
    `;
    
    row.innerHTML = rowContent;
    
    // Add event listeners for the new row
    const checkbox = row.querySelector('.ticket-checkbox');
    if (checkbox) {
        checkbox.addEventListener('change', updateSelectAllState);
    }
    
    return row;
}

// Update statistics display
function updateStatistics() {
    fetch('{{ route("admin.statistics") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(stats => {
        // Update statistics cards if they exist
        const totalElement = document.getElementById('total-tickets');
        const openElement = document.getElementById('open-tickets');
        const inProgressElement = document.getElementById('in-progress-tickets');
        const completedElement = document.getElementById('completed-tickets');
        
        if (totalElement) totalElement.textContent = stats.total;
        if (openElement) openElement.textContent = stats.open;
        if (inProgressElement) inProgressElement.textContent = stats.in_progress;
        if (completedElement) completedElement.textContent = stats.completed;
    })
    .catch(error => {
        console.error('Error updating statistics:', error);
    });
}

// AJAX acknowledgment function
function acknowledgeTicket(ticketId) {
    const button = document.getElementById(`ack-btn-${ticketId}`);
    if (!button) return;
    
    // Store original button state
    const originalHTML = button.innerHTML;
    const originalClass = button.className;
    const originalTitle = button.title;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.className = 'btn btn-secondary';
    button.disabled = true;
    button.title = 'Acknowledging...';
    
    // Prepare the request data
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    
    // Send AJAX request
    fetch(`/tickets/${ticketId}/acknowledge`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Update button to show acknowledged state
        button.innerHTML = '<i class="fas fa-check-circle"></i>';
        button.className = 'btn btn-secondary';
        button.disabled = true;
        button.title = 'Already Acknowledged';
        
        // Update ACK count in the table
        const ticketRow = document.querySelector(`tr[data-ticket-id="${ticketId}"]`);
        const ackCell = ticketRow?.querySelector('td:nth-child(9)'); // ACK column
        if (ackCell) {
            ackCell.innerHTML = '<span class="badge bg-success small">1</span>';
        }
        
        // Show success message
        showToast('Success', 'Ticket acknowledged successfully!', 'success');
        
        // Add highlight effect
        if (ticketRow) {
            ticketRow.style.backgroundColor = '#d4edda';
            setTimeout(() => {
                ticketRow.style.backgroundColor = '';
                ticketRow.style.transition = 'background-color 0.5s ease';
            }, 1500);
        }
    })
    .catch(error => {
        console.error('Error acknowledging ticket:', error);
        
        // Revert button to original state
        button.innerHTML = originalHTML;
        button.className = originalClass;
        button.disabled = false;
        button.title = originalTitle;
        
        // Show error message
        showToast('Error', 'Failed to acknowledge ticket. Please try again.', 'error');
    });
}

// Update select all checkbox state
function updateSelectAllState() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const ticketCheckboxes = document.querySelectorAll('.ticket-checkbox');
    
    if (selectAllCheckbox && ticketCheckboxes.length > 0) {
        const allChecked = Array.from(ticketCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(ticketCheckboxes).some(cb => cb.checked);
        
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
    }
}

// Initialize real-time updates when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Start real-time updates after a short delay
    setTimeout(startRealTimeUpdates, 1000);
    
    // Stop updates when page becomes hidden (user switches tabs)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopRealTimeUpdates();
        } else {
            startRealTimeUpdates();
        }
    });
    
    // Stop updates when user leaves the page
    window.addEventListener('beforeunload', function() {
        stopRealTimeUpdates();
    });
});
</script>
@endsection 

<style>
/* Compact table styles */
.compact-table {
    font-size: 0.875rem;
    width: 100%;
    min-width: 1000px; /* Minimum width to accommodate all columns */
    max-width: 100%;
}

.compact-table th,
.compact-table td {
    padding: 0.5rem 0.25rem;
    vertical-align: middle;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.compact-table th {
    font-size: 0.8rem;
    font-weight: 600;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

/* Ticket info styling */
.ticket-info {
    max-width: 180px;
}

.ticket-name {
    font-weight: 600;
    color: #212529;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ticket-desc {
    font-size: 0.75rem;
    color: #6c757d;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Image styling */
.ticket-thumbnail {
    width: 35px;
    height: 35px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.ticket-thumbnail:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.no-image-placeholder {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border: 1px dashed #dee2e6;
    border-radius: 4px;
    color: #6c757d;
}

.no-image-placeholder i {
    font-size: 0.9rem;
}

/* Location info styling */
.location-info {
    display: flex;
    align-items: center;
    max-width: 110px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.location-info span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Assigned info styling */
.assigned-info {
    display: flex;
    align-items: center;
    max-width: 90px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.assigned-info span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Badge styling */
.compact-table .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Button group styling */
.compact-table .btn-group .btn {
    padding: 0.4rem 0.6rem;
    font-size: 0.8rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.compact-table .btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.compact-table .btn-group .btn i {
    font-size: 0.8rem;
}

/* Table container */
.table-responsive {
    overflow-x: auto;
    max-width: 100%;
    -webkit-overflow-scrolling: touch;
    /* Ensure horizontal scrollbar appears when needed */
    scrollbar-width: thin;
    scrollbar-color: #6c757d #f8f9fa;
}

.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #6c757d;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #495057;
}

/* Ensure table fits in container */
.card-body.p-0 {
    overflow: hidden;
}

/* Hover effects */
.compact-table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Status badge colors */
.status-badge.bg-info {
    background-color: #17a2b8 !important;
}

.status-badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.status-badge.bg-success {
    background-color: #28a745 !important;
}

.status-badge.bg-danger {
    background-color: #dc3545 !important;
}

/* Responsive breakpoints - Only hide less critical columns on very small screens */
@media (max-width: 768px) {
    /* Only hide location and acknowledgment columns on very small screens */
    .compact-table th:nth-child(4),
    .compact-table td:nth-child(4),
    .compact-table th:nth-child(9),
    .compact-table td:nth-child(9) {
        display: none;
    }
    
    .compact-table .btn-group .btn {
        padding: 0.3rem 0.5rem;
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    /* On extra small screens, hide a few more columns but keep essential ones */
    .compact-table th:nth-child(5),
    .compact-table td:nth-child(5),
    .compact-table th:nth-child(6),
    .compact-table td:nth-child(6) {
        display: none;
    }
}

/* Ensure dropdown menus are visible and appear in front of everything */
.dropdown-menu {
    z-index: 9999999 !important;
    position: fixed !important;
    background-color: white !important;
    border: 1px solid rgba(0,0,0,0.15) !important;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.175) !important;
    min-width: 200px !important;
        display: none;
    list-style: none !important;
    padding: 0.5rem 0 !important;
    margin: 0 !important;
    border-radius: 0.375rem !important;
    transform: translateZ(0) !important;
    will-change: transform !important;
}

/* Ensure dropdown positioning works correctly */
.dropdown {
    position: relative;
    display: inline-block;
}

/* Force dropdown to appear above table and all other elements */
.compact-table .dropdown {
    position: relative;
    z-index: 9999999;
}

/* Ensure table rows have a very low stacking context */
.compact-table {
    position: relative;
    z-index: 1;
}

.compact-table tbody tr {
    position: relative;
    z-index: 1;
}

.compact-table tbody td {
    position: relative;
    z-index: 1;
}

/* Make sure dropdown appears above all table content */
.compact-table .dropdown-menu {
    z-index: 9999999 !important;
    position: fixed !important;
    background-color: white !important;
    border: 1px solid rgba(0,0,0,0.15) !important;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.175) !important;
    min-width: 200px !important;
    transform: translateZ(0) !important;
    will-change: transform !important;
}

/* Additional rule to ensure dropdown appears above everything */
.compact-table .dropdown-menu[style*="display: block"] {
    z-index: 9999999 !important;
    position: fixed !important;
    background-color: white !important;
    border: 1px solid rgba(0,0,0,0.15) !important;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.175) !important;
    min-width: 200px !important;
    transform: translateZ(0) !important;
    will-change: transform !important;
}

/* Style dropdown items */
.dropdown-item {
    display: block !important;
    width: 100% !important;
    padding: 0.5rem 1rem !important;
    clear: both !important;
    font-weight: 400 !important;
    color: #212529 !important;
    text-align: inherit !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    background-color: transparent !important;
    border: 0 !important;
    cursor: pointer !important;
}

.dropdown-item:hover {
    background-color: #f8f9fa !important;
    color: #1e2125 !important;
}

.dropdown-header {
    display: block !important;
    padding: 0.5rem 1rem !important;
    margin-bottom: 0 !important;
    font-size: 0.875rem !important;
    color: #6c757d !important;
    white-space: nowrap !important;
    font-weight: 600 !important;
}

.dropdown-divider {
    height: 0 !important;
    margin: 0.5rem 0 !important;
    border: 0 !important;
    border-top: 1px solid rgba(0,0,0,0.1) !important;
}

/* Additional responsive fixes */
@media (max-width: 1600px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

@media (max-width: 1200px) {
    .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}

/* Dynamic dropdown styling */
.dynamic-dropdown {
    z-index: 99999999 !important;
    position: fixed !important;
    background-color: white !important;
    border: 1px solid rgba(0,0,0,0.15) !important;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.175) !important;
    min-width: 200px !important;
    border-radius: 0.375rem !important;
    padding: 0.5rem 0 !important;
    margin: 0 !important;
    list-style: none !important;
    font-family: inherit !important;
    font-size: 0.875rem !important;
    transform: translateZ(0) !important;
    will-change: transform !important;
    pointer-events: auto !important;
}

.dynamic-dropdown .dropdown-item {
    display: block !important;
    width: 100% !important;
    padding: 0.5rem 1rem !important;
    clear: both !important;
    font-weight: 400 !important;
    color: #212529 !important;
    text-align: inherit !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    background-color: transparent !important;
    border: 0 !important;
    cursor: pointer !important;
    transition: background-color 0.15s ease-in-out !important;
}

.dynamic-dropdown .dropdown-item:hover {
    background-color: #f8f9fa !important;
    color: #1e2125 !important;
}

/* Ensure table has low stacking context */
.compact-table {
    position: relative;
    z-index: 1;
}

.compact-table tbody tr {
    position: relative;
    z-index: 1;
}

.compact-table tbody td {
    position: relative;
    z-index: 1;
}

/* Real-time indicator animations */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

/* Highlight effects for updates */
.ticket-row-updated {
    background-color: #d1ecf1 !important;
    transition: background-color 0.5s ease;
}

.ticket-row-new {
    background-color: #fff3cd !important;
    transition: background-color 0.5s ease;
}
</style> 