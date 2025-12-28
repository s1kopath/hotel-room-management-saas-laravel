<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'scope',
        'created_by',
        'hotel_owner_id',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
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

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->withPivot('assigned_by', 'assigned_at')
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeSystem($query)
    {
        return $query->where('scope', 'system');
    }

    public function scopeHotelOwner($query)
    {
        return $query->where('scope', 'hotel_owner');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}

