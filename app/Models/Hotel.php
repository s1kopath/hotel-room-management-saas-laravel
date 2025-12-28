<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'description',
        'total_rooms',
        'status',
    ];

    /**
     * Relationships
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(HotelImage::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_hotel_access')
            ->withPivot('granted_by', 'granted_at', 'revoked_at', 'is_active')
            ->wherePivot('is_active', true);
    }

    public function allStaffAccess(): HasMany
    {
        return $this->hasMany(UserHotelAccess::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }
}

