<?php

namespace App\Events;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketAccepted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $acceptedBy;

    public function __construct(Ticket $ticket, User $acceptedBy)
    {
        $this->ticket = $ticket;
        $this->acceptedBy = $acceptedBy;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('tickets'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ticket.accepted';
    }

    public function broadcastWith(): array
    {
        return [
            'ticket' => [
                'id' => $this->ticket->id,
                'name' => $this->ticket->name,
                'status' => $this->ticket->status,
                'accepted_by' => $this->acceptedBy->name,
                'accepted_by_id' => $this->acceptedBy->id,
                'department' => $this->ticket->department->name,
                'category' => $this->ticket->category->name,
                'created_at' => $this->ticket->created_at->format('M d, Y H:i'),
            ],
        ];
    }
}
