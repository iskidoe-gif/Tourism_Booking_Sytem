<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tour_package_id', 'booking_id', 'rating', 'comment',
    ];

    // Relationships
    public function user()        { return $this->belongsTo(User::class); }
    public function tourPackage() { return $this->belongsTo(TourPackage::class); }
    public function booking()     { return $this->belongsTo(Booking::class); }
}
