<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'user_id',
        'tour_package_id',
        'tour_date',
        'num_guests',
        'total_price',
        'special_requests',
        'status',
    ];

    protected $casts = [
        'tour_date'   => 'date',
        'total_price' => 'decimal:2',
    ];

    // Auto-generate booking number before creating
    protected static function booted()
    {
        static::creating(function ($booking) {
            $booking->booking_number = 'BK-' . strtoupper(Str::random(6));
        });
    }

    // Booking belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Booking belongs to a tour package
    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }

    // Booking has one payment
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
