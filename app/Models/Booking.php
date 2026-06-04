<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Review;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'user_id',
        'tour_package_id',
        'tour_date',
        'num_guests',
        'status',
        'total_price',
        'special_requests',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'tour_date' => 'date',
            'approved_at' => 'datetime',
            'num_guests' => 'integer',
            'total_price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }

    // Backwards-compatible alias for existing relationship name used across views/controllers
    public function tourPackage(): BelongsTo
    {
        return $this->package();
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    // Lazy accessor: the review left by the booking user for this booked tour package
    public function getReviewAttribute()
    {
        return Review::where('tour_package_id', $this->tour_package_id)
            ->where('user_id', $this->user_id)
            ->first();
    }
}
