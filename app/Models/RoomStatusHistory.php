<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'room_status_history';

    protected $fillable = [
        'room_id',
        'previous_status',
        'new_status',
        'changed_by',
        'changed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Scopes
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('changed_at', '>=', now()->subDays($days));
    }

    public function scopeByRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('changed_by', $userId);
    }
}

