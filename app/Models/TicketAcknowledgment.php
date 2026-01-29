<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAcknowledgment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'anonymous_identifier',
        'ip_address',
        'comment'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAcknowledgerNameAttribute()
    {
        if ($this->user_id) {
            return $this->user ? $this->user->name : 'Unknown User';
        }
        return 'Anonymous';
    }
}
