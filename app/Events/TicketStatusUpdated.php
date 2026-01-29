<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $oldStatus;
    public $newStatus;

    public function __construct(Ticket $ticket, string $oldStatus, string $newStatus)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('tickets'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ticket.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'ticket' => [
                'id' => $this->ticket->id,
                'name' => $this->ticket->name,
                'status' => $this->ticket->status,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
                'updated_at' => $this->ticket->updated_at->format('M d, Y H:i'),
            ],
        ];
    }
}
