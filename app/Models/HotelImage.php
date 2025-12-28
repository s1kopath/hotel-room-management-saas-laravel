<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelImage extends Model
{
    use HasFactory;

    public $timestamps = false; // This table uses uploaded_at instead of created_at/updated_at

    protected $fillable = [
        'hotel_id',
        'image_url',
        'image_type',
        'display_order',
        'uploaded_by',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scopes
     */
    public function scopeMain($query)
    {
        return $query->where('image_type', 'main');
    }

    public function scopeGallery($query)
    {
        return $query->where('image_type', 'gallery');
    }

    public function scopeThumbnail($query)
    {
        return $query->where('image_type', 'thumbnail');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}

