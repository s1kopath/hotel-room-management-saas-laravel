<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminReservationHistory extends Model
{
    use HasFactory;

    protected $table = 'admin_reservations_history';

    protected $fillable = [
        'reservation_id',
        'admin_id',
        'action_type',
        'action_at',
        'notes',
        'archive_month',
    ];

    protected function casts(): array
    {
        return [
            'action_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scopes
     */
    public function scopeCreated($query)
    {
        return $query->where('action_type', 'created');
    }

    public function scopeModified($query)
    {
        return $query->where('action_type', 'modified');
    }

    public function scopeReleased($query)
    {
        return $query->where('action_type', 'released');
    }

    public function scopeLast30Days($query)
    {
        return $query->where('action_at', '>=', now()->subDays(30));
    }

    public function scopeByArchiveMonth($query, string $month)
    {
        return $query->where('archive_month', $month);
    }
}

