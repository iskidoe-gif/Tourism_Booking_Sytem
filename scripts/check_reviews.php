<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Review;

echo "Checking Recent Customer Reviews...\n\n";

$totalReviews = Review::count();
echo "Total reviews in database: {$totalReviews}\n\n";

if ($totalReviews > 0) {
    echo "Fetching recent reviews with relations...\n";
    $recentReviews = Review::with(['user', 'tourPackage'])
        ->latest()
        ->limit(8)
        ->get();

    echo "Found {$recentReviews->count()} recent reviews\n\n";

    foreach ($recentReviews as $review) {
        echo "Review ID: {$review->id}\n";
        echo "  User: " . ($review->user?->name ?? 'N/A') . "\n";
        echo "  Package: " . ($review->tourPackage?->name ?? 'N/A') . "\n";
        echo "  Rating: {$review->rating}\n";
        echo "  Comment: " . ($review->comment ?? 'No comment') . "\n";
        echo "  Created at: {$review->created_at}\n\n";
    }
} else {
    echo "No reviews found in database.\n";
    echo "The Recent Customer Reviews section will not display anything.\n";
}
