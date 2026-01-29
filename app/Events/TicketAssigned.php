<?php

namespace App\Events;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $assignedTo;
    public $assignedBy;

    public function __construct(Ticket $ticket, User $assignedTo, User $assignedBy = null)
    {
        $this->ticket = $ticket;
        $this->assignedTo = $assignedTo;
        $this->assignedBy = $assignedBy;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('tickets'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ticket.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'ticket' => [
                'id' => $this->ticket->id,
                'name' => $this->ticket->name,
                'status' => $this->ticket->status,
                'assigned_to' => $this->assignedTo->name,
                'assigned_to_id' => $this->assignedTo->id,
                'assigned_by' => $this->assignedBy ? $this->assignedBy->name : 'System',
                'department' => $this->ticket->department->name,
                'category' => $this->ticket->category->name,
                'created_at' => $this->ticket->created_at->format('M d, Y H:i'),
            ],
        ];
    }
}
