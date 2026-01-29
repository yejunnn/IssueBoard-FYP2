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

class TicketCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('tickets'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ticket.created';
    }

    public function broadcastWith(): array
    {
        return [
            'ticket' => [
                'id' => $this->ticket->id,
                'name' => $this->ticket->name,
                'description' => $this->ticket->description,
                'status' => $this->ticket->status,
                'location' => $this->ticket->location,
                'department' => $this->ticket->department->name,
                'category' => $this->ticket->category->name,
                'created_at' => $this->ticket->created_at->format('M d, Y H:i'),
                'image_path' => $this->ticket->image_path,
            ],
        ];
    }
}
