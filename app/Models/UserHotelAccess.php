<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHotelAccess extends Model
{
    use HasFactory;

    protected $table = 'user_hotel_access';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'granted_by',
        'granted_at',
        'revoked_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'granted_at' => 'datetime',
            'revoked_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRevoked($query)
    {
        return $query->where('is_active', false)
            ->whereNotNull('revoked_at');
    }
}

