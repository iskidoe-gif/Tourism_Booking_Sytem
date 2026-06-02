<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number', 'user_id', 'tour_package_id',
        'tour_date', 'num_guests', 'total_price',
        'special_requests', 'status',
    ];

    protected $casts = [
        'tour_date'   => 'date',
        'total_price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($booking) {
            $booking->booking_number = 'BLN-' . strtoupper(Str::random(7));
        });
    }

    // Relationships
    public function user()        { return $this->belongsTo(User::class); }
    public function tourPackage() { return $this->belongsTo(TourPackage::class); }
    public function payment()     { return $this->hasOne(Payment::class); }
    public function review()      { return $this->hasOne(Review::class); }
}
