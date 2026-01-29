<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'location',
        'image_path',
        'department_id',
        'status',
        'acknowledged_by',
        'accepted_by',
        'created_by',
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function acknowledgments(): HasMany
    {
        return $this->hasMany(TicketAcknowledgment::class);
    }

    public function getAcknowledgmentCountAttribute()
    {
        return $this->acknowledgments()->count();
    }

    public function hasBeenAcknowledgedBy($user = null, $anonymousIdentifier = null)
    {
        if ($user) {
            return $this->acknowledgments()->where('user_id', $user->id)->exists();
        }
        
        if ($anonymousIdentifier) {
            return $this->acknowledgments()->where('anonymous_identifier', $anonymousIdentifier)->exists();
        }
        
        return false;
    }
}
