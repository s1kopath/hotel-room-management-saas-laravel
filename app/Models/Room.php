<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_number',
        'room_type',
        'floor_number',
        'capacity',
        'description',
        'status',
        'last_status_change',
        'status_updated_by',
    ];

    protected function casts(): array
    {
        return [
            'last_status_change' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(RoomStatusHistory::class);
    }

    public function statusUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_updated_by');
    }

    /**
     * Scopes
     */
    public function scopeVacant($query)
    {
        return $query->where('status', 'vacant');
    }

    public function scopeReserved($query)
    {
        return $query->where('status', 'reserved');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeAdminReserved($query)
    {
        return $query->where('status', 'admin_reserved');
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['vacant', 'reserved']);
    }

    /**
     * Helper Methods
     */
    public function isAvailable(): bool
    {
        return in_array($this->status, ['vacant', 'reserved']);
    }

    public function isAdminReserved(): bool
    {
        return $this->status === 'admin_reserved';
    }
}

