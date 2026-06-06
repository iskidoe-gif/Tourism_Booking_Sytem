<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\TourPackage;

class SetPackageImage extends Command
{
    protected $signature = 'package:set-image {identifier : Package id or name} {path : Local file path to image}';
    protected $description = 'Copy a local image into public storage and set it as the package image.';

    public function handle(): int
    {
        $idOrName = $this->argument('identifier');
        $path = $this->argument('path');

        // sanitize/normalize the incoming path (handles surrounding quotes, URL-encoding,
        // file:// scheme, and UNC paths)
        $originalPath = $path;
        $candidates = [];
        $candidates[] = $path;
        $candidates[] = trim($path, "\"' ");
        $candidates[] = rawurldecode($path);
        $candidates[] = trim(rawurldecode($path), "\"' ");

        // handle file:// URLs and UNC (network) paths
        if (stripos($path, 'file://') === 0) {
            $parts = parse_url($path);
            if ($parts !== false) {
                // If a host is present this is likely a UNC path: file://server/share/dir/file.png
                if (! empty($parts['host'])) {
                    $filePath = '//' . $parts['host'] . ($parts['path'] ?? '');
                } else {
                    // Typical Windows file URI: file:///C:/path/to/file.png
                    $filePath = $parts['path'] ?? '';
                }
                // strip a leading slash before a Windows drive letter: /C:/ -> C:/
                $filePath = preg_replace('#^/([A-Za-z]:/)#', '$1', $filePath);
                // normalize slashes
                $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
                $candidates[] = $filePath;
                $candidates[] = rawurldecode($filePath);
            }
        }

        // also try converting forward-slash UNC-like paths to Windows UNC form
        if (preg_match('#^//[^/]+/.+#', $path)) {
            $unc = str_replace('/', DIRECTORY_SEPARATOR, $path);
            $candidates[] = $unc;
            $candidates[] = rawurldecode($unc);
        }

        $found = false;
        foreach ($candidates as $cand) {
            if ($cand === '') continue;
            if (file_exists($cand)) {
                $path = $cand;
                $found = true;
                break;
            }
        }

        if (! $found) {
            $this->error('Local file not found: ' . $originalPath);
            $this->error('Checked candidates: ' . implode(' | ', array_unique($candidates)));
            return 1;
        }

        // find package by id or name
        $package = null;
        if (is_numeric($idOrName)) {
            $package = TourPackage::find((int) $idOrName);
        }
        if (! $package) {
            $package = TourPackage::where('name', $idOrName)->first();
        }
        if (! $package) {
            $this->error('Package not found for identifier: ' . $idOrName);
            return 2;
        }

        $filename = time() . '-' . uniqid() . '-' . preg_replace('/[^a-z0-9\-\.]/i', '-', basename($path));
        $dest = 'images/' . $filename;

        try {
            // copy into public disk
            $stream = fopen($path, 'rb');
            if ($stream === false) {
                $this->error('Could not open source file.');
                return 3;
            }
            $ok = Storage::disk('public')->put($dest, $stream);
            if (is_resource($stream)) fclose($stream);
            if (! $ok) {
                $this->error('Failed to store file to public disk.');
                return 4;
            }

            // update package record
            $package->image = $dest;
            $package->save();

            $this->info('Package image updated: ' . $dest);
            $this->info('Preview URL: ' . asset('storage/' . ltrim($dest, '/')));
            return 0;
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
            return 5;
        }
    }
}
