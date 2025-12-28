<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminReservationArchive extends Model
{
    use HasFactory;

    protected $table = 'admin_reservations_archive';

    protected $fillable = [
        'original_history_id',
        'reservation_id',
        'room_id',
        'hotel_id',
        'admin_id',
        'action_type',
        'action_at',
        'archive_month',
        'archived_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'action_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    /**
     * Scopes
     */
    public function scopeByArchiveMonth($query, string $month)
    {
        return $query->where('archive_month', $month);
    }

    public function scopeRecent($query, int $months = 6)
    {
        $cutoffMonth = now()->subMonths($months)->format('Y-m');
        return $query->where('archive_month', '>=', $cutoffMonth);
    }
}

