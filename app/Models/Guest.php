<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'phone_secondary',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'id_type',
        'id_number',
        'date_of_birth',
        'nationality',
        'preferences',
        'notes',
        'vip_status',
        'created_by',
        'hotel_owner_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'vip_status' => 'boolean',
            'preferences' => 'array',
        ];
    }

    /**
     * Relationships
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hotelOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hotel_owner_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Scopes
     */
    public function scopeVip($query)
    {
        return $query->where('vip_status', true);
    }

    public function scopeByHotelOwner($query, $hotelOwnerId)
    {
        return $query->where('hotel_owner_id', $hotelOwnerId);
    }

    /**
     * Helper Methods
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}

