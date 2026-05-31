<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'price',
        'duration_days',
        'max_guests',
        'image',
        'status',
        'rating',
    ];

    protected $casts = [
        'price'   => 'decimal:2',
        'rating'  => 'decimal:2',
    ];

    // One package has many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Scope: only active packages
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
