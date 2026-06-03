<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\PackageController;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile as IlluminateUploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

$package = TourPackage::first();
if (! $package) {
    echo "No package found\n";
    exit(1);
}

$sampleImage = __DIR__ . '/public/images/bolinao-church.jpg';
if (! file_exists($sampleImage)) {
    echo "Sample image missing: $sampleImage\n";
    exit(1);
}

$temp = sys_get_temp_dir() . '/' . basename($sampleImage);
copy($sampleImage, $temp);
$symfonyFile = new SymfonyUploadedFile($temp, basename($sampleImage), mime_content_type($temp), UPLOAD_ERR_OK, true);
$file = IlluminateUploadedFile::createFromBase($symfonyFile);

$request = Request::create('/admin/packages/' . $package->id . '/upload-image', 'POST', [], [], ['image_file' => $file], ['HTTP_X-CSRF-TOKEN' => csrf_token()]);
$request->setLaravelSession(app('session.store'));

var_dump($request->files->all());
var_dump($request->hasFile('image_file'));
var_dump($request->file('image_file'));

// $controller = new PackageController();
// $response = $controller->uploadImage($request, $package);

// echo get_class($response) . "\n";
// echo $response->getContent() . "\n";
// echo "Package image now: " . TourPackage::find($package->id)->image . "\n";
