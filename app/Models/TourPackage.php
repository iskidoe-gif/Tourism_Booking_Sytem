<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'location', 'price',
        'duration_days', 'max_guests', 'image',
        'type', 'status', 'rating', 'review_count',
    ];

    protected $casts = [
        'price'  => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    // Relationships
    public function bookings() { return $this->hasMany(Booking::class); }
    public function reviews()  { return $this->hasMany(Review::class); }

    // Scopes
    public function scopeActive($query)  { return $query->where('status', 'active'); }
    public function scopeByType($query, $type) { return $query->where('type', $type); }

    // Recalculate rating from reviews
    public function updateRating(): void
    {
        $avg   = $this->reviews()->avg('rating') ?? 0;
        $count = $this->reviews()->count();
        $this->update(['rating' => round($avg, 2), 'review_count' => $count]);
    }
}
