<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_number',
        'room_id',
        'hotel_id',
        'guest_id',
        'check_in_date',
        'check_out_date',
        'actual_check_in',
        'actual_check_out',
        'number_of_guests',
        'reservation_type',
        'status',
        'payment_status',
        'total_amount',
        'paid_amount',
        'special_requests',
        'notes',
        'created_by',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'actual_check_in' => 'datetime',
            'actual_check_out' => 'datetime',
            'cancelled_at' => 'datetime',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    /**
     * Relationships
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function adminHistory(): HasMany
    {
        return $this->hasMany(AdminReservationHistory::class);
    }

    /**
     * Scopes
     */
    public function scopeRegular($query)
    {
        return $query->where('reservation_type', 'regular');
    }

    public function scopeAdminOverride($query)
    {
        return $query->where('reservation_type', 'admin_override');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('status', 'checked_in');
    }

    public function scopeCheckedOut($query)
    {
        return $query->where('status', 'checked_out');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'checked_in']);
    }

    /**
     * Helper Methods
     */
    public function isAdminOverride(): bool
    {
        return $this->reservation_type === 'admin_override';
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'confirmed', 'checked_in']);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }
}

