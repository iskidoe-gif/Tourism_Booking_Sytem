<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'destination',
        'description',
        'price',
        'duration_days',
        'max_guests',
        'start_date',
        'end_date',
        'includes',
        'itinerary',
        'image_url',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_days' => 'integer',
            'max_guests' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'includes' => 'array',
            'itinerary' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
