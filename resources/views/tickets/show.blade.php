@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb :items="[
        ['label' => 'Tickets', 'url' => route('tickets.index'), 'icon' => 'fas fa-ticket-alt'],
        ['label' => $ticket->name]
    ]" />

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Ticket Details</h4>
                    <a href="{{ route('tickets.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $ticket->name }}</h5>
                    <p class="card-text"><strong>Description:</strong> {{ $ticket->description }}</p>
                    <p class="card-text"><strong>Category:</strong> {{ $ticket->category->name }}</p>
                    <p class="card-text"><strong>Department:</strong> {{ $ticket->department->name }}</p>
                    <p class="card-text"><strong>Location:</strong> {{ $ticket->location }}</p>
                    <p class="card-text"><strong>Status:</strong> <span class="badge bg-{{ $ticket->status === 'completed' ? 'success' : ($ticket->status === 'in_progress' ? 'warning' : ($ticket->status === 'open' ? 'info' : 'danger')) }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></p>

                    <p class="card-text"><strong>Created at:</strong> {{ $ticket->created_at->format('M d, Y H:i') }}</p>
                    <p class="card-text"><strong>Last updated:</strong> {{ $ticket->updated_at->format('M d, Y H:i') }}</p>
                    
                    @if($ticket->status === 'cancel')
                        @php
                            $deletionDate = $ticket->updated_at->addDays(7);
                            $daysUntilDeletion = now()->diffInDays($deletionDate, false);
                        @endphp
                        <div class="alert alert-warning mt-3">
                            <h6><i class="fas fa-clock me-1"></i>Auto-Deletion Notice</h6>
                            <p class="mb-1">This cancelled ticket will be automatically deleted on: <strong>{{ $deletionDate->format('M d, Y H:i') }}</strong></p>
                            @if($daysUntilDeletion > 0)
                                <p class="mb-0"><small>Time remaining: <strong>{{ $daysUntilDeletion }} days</strong></small></p>
                            @else
                                <p class="mb-0"><small><strong>This ticket will be deleted soon!</strong></small></p>
                            @endif
                        </div>
                    @endif
                    @if($ticket->image_path)
                        <div class="mb-3">
                            <img src="{{ Storage::url($ticket->image_path) }}" class="img-fluid rounded" alt="Ticket Image">
                        </div>
                    @else
                        <div class="mb-3">
                            <img src="{{ asset('images/placeholder.png') }}" class="img-fluid rounded" alt="No Image" style="opacity: 0.5; max-height: 200px;">
                        </div>
                    @endif
                    @php $user = Auth::user(); @endphp
                    
                    @if($user && $user->department_id && !$user->is_admin && $ticket->department_id == $user->department_id && !$ticket->accepted_by)
                        <div class="alert alert-warning mt-3">
                            <h6><i class="fas fa-info-circle me-1"></i>Ticket Assignment</h6>
                            <p class="mb-2">This ticket is assigned to your department and is ready to be accepted.</p>
                            <form action="{{ route('department.tickets.accept', ['ticket_id' => $ticket->id]) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-1"></i>Accept Ticket
                                </button>
                            </form>
                        </div>
                    @elseif($ticket->accepted_by)
                        <div class="alert alert-info mt-3">
                            <h6><i class="fas fa-user-check me-1"></i>Ticket Accepted</h6>
                            <p class="mb-0">This ticket has been accepted by: <strong>{{ $ticket->acceptedBy ? $ticket->acceptedBy->name : 'Unknown' }}</strong></p>
                        </div>
                    @endif
                    
                    @php $user = Auth::user(); @endphp
                    @if($ticket->status === 'completed')
                        @php
                            $canAcknowledge = true;
                            $hasAcknowledged = false;
                            
                            if ($user && $user->department_id && !$user->is_admin) {
                                $canAcknowledge = false;
                            }
                            
                            if ($user) {
                                $hasAcknowledged = $ticket->hasBeenAcknowledgedBy($user);
                            } else {
                                $anonymousIdentifier = 'anon_' . md5(request()->ip() . request()->userAgent() . $ticket->id);
                                $hasAcknowledged = $ticket->hasBeenAcknowledgedBy(null, $anonymousIdentifier);
                            }
                        @endphp
                        
                        @if($ticket->acknowledgments->count() > 0)
                            <div class="alert alert-success mt-3">
                                <h6><i class="fas fa-check-circle me-1"></i> Acknowledged ({{ $ticket->acknowledgments->count() }} times)</h6>
                                <div class="acknowledgment-list mt-2">
                                    @foreach($ticket->acknowledgments->take(5) as $acknowledgment)
                                        <div class="acknowledgment-item">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user me-2"></i>
                                                <span class="text-success fw-bold">{{ $acknowledgment->acknowledger_name }}</span>
                                                <small class="text-muted ms-2">{{ $acknowledgment->created_at->format('M d, Y H:i') }}</small>
                                            </div>
                                            @if($acknowledgment->comment)
                                                <div class="acknowledgment-comment">
                                                    "{{ $acknowledgment->comment }}"
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($ticket->acknowledgments->count() > 5)
                                        <div class="text-center mt-2">
                                            <small class="text-muted">... and {{ $ticket->acknowledgments->count() - 5 }} more acknowledgments</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if($canAcknowledge && !$hasAcknowledged)
                            <form action="{{ route('tickets.acknowledge', $ticket) }}" method="POST" class="mt-3">
                                @csrf
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Add a comment (optional):</label>
                                    <textarea name="comment" id="comment" class="form-control" rows="2" placeholder="Add your acknowledgment comment..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-1"></i>Acknowledge Ticket
                                </button>
                            </form>
                        @elseif($hasAcknowledged)
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>You have already acknowledged this ticket.</strong>
                            </div>
                        @elseif($user && $user->department_id && !$user->is_admin)
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>Cannot Acknowledge:</strong> Department users cannot acknowledge tickets.
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 