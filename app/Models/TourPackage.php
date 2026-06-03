<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_id',
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

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('images/package-default.svg');
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // If image already refers to a public path
        if (file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        // If image is stored in the public disk (storage/app/public)
        if (Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . ltrim($this->image, '/'));
        }

        // If only filename was saved (e.g., "photo.jpg"), check common locations
        if (file_exists(public_path('images/' . $this->image))) {
            return asset('images/' . $this->image);
        }

        if (Storage::disk('public')->exists('images/' . $this->image)) {
            return asset('storage/images/' . ltrim($this->image, '/'));
        }

        return asset('images/package-default.svg');
    }

    public function getHasImageAttribute(): bool
    {
        if (! $this->image) {
            return false;
        }

        if (str_starts_with($this->image, 'http')) {
            return true;
        }

        if (file_exists(public_path($this->image))) {
            return true;
        }

        if (Storage::disk('public')->exists($this->image)) {
            return true;
        }

        if (file_exists(public_path('images/' . $this->image))) {
            return true;
        }

        if (Storage::disk('public')->exists('images/' . $this->image)) {
            return true;
        }

        return false;
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_days' => 'integer',
            'max_guests' => 'integer',
            'rating' => 'decimal:2',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
