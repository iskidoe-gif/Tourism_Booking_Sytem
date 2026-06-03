<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TourPackage;

$packages = TourPackage::where('status', 'inactive')->get();
$count = 0;
foreach ($packages as $p) {
    $p->status = 'active';
    $p->image = 'sample-' . $p->category . '.jpg';
    $p->save();
    $count++;
}

echo "Updated $count package(s) to active with sample images.\n";
