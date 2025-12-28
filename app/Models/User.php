<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements CanResetPassword
{
    use Notifiable, HasApiTokens;
    use CanResetPasswordTrait;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'phone',
        'user_type',
        'parent_user_id',
        'status',
        'created_by',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships
     */
    public function parentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function childUsers(): HasMany
    {
        return $this->hasMany(User::class, 'parent_user_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('assigned_by', 'assigned_at')
            ->withTimestamps();
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function hotelAccess(): HasMany
    {
        return $this->hasMany(UserHotelAccess::class);
    }

    public function accessibleHotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'user_hotel_access')
            ->withPivot('granted_by', 'granted_at', 'revoked_at', 'is_active')
            ->wherePivot('is_active', true);
    }

    public function createdRoles(): HasMany
    {
        return $this->hasMany(Role::class, 'created_by');
    }

    public function createdGuests(): HasMany
    {
        return $this->hasMany(Guest::class, 'created_by');
    }

    public function ownedGuests(): HasMany
    {
        return $this->hasMany(Guest::class, 'hotel_owner_id');
    }

    public function createdReservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'created_by');
    }

    public function cancelledReservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'cancelled_by');
    }

    public function adminReservationsHistory(): HasMany
    {
        return $this->hasMany(AdminReservationHistory::class, 'admin_id');
    }

    public function roomStatusChanges(): HasMany
    {
        return $this->hasMany(RoomStatusHistory::class, 'changed_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeDeleted($query)
    {
        return $query->where('status', 'deleted');
    }

    public function scopeSuperAdmin($query)
    {
        return $query->where('user_type', 'super_admin');
    }

    public function scopeHotelOwner($query)
    {
        return $query->where('user_type', 'hotel_owner');
    }

    public function scopeStaff($query)
    {
        return $query->where('user_type', 'staff');
    }

    /**
     * Helper Methods
     */
    public function isSuperAdmin(): bool
    {
        return $this->user_type === 'super_admin';
    }

    public function isHotelOwner(): bool
    {
        return $this->user_type === 'hotel_owner';
    }

    public function isStaff(): bool
    {
        return $this->user_type === 'staff';
    }

    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    public function hasPermission(string $permissionName): bool
    {
        // Super admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->exists();
    }

    /**
     * Check if user has access to a specific hotel
     */
    public function hasAccessToHotel(int $hotelId): bool
    {
        // Super admin has access to all hotels
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Hotel owners have access to their own hotels
        if ($this->isHotelOwner()) {
            return Hotel::where('id', $hotelId)
                ->where('user_id', $this->id)
                ->exists();
        }

        // Staff must have explicit access via user_hotel_access
        if ($this->isStaff()) {
            return $this->accessibleHotels()->where('hotels.id', $hotelId)->exists();
        }

        return false;
    }
}
