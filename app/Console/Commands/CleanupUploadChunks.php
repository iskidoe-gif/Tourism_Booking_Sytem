<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupUploadChunks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uploads:cleanup {--hours=24 : Remove uploads older than this many hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove stale chunked upload temporary folders';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = time() - ($hours * 3600);

        $base = storage_path('app/uploads/tmp');
        if (! is_dir($base)) {
            $this->info('No tmp upload directory found.');
            return 0;
        }

        $removed = 0;
        $dirs = glob($base . DIRECTORY_SEPARATOR . '*');
        foreach ($dirs as $dir) {
            if (! is_dir($dir)) continue;
            $mtime = filemtime($dir);
            if ($mtime === false) $mtime = $cutoff - 1;
            if ($mtime < $cutoff) {
                $this->info('Removing stale upload: ' . $dir);
                $this->rrmdir($dir);
                $removed++;
            }
        }

        $this->info("Removed {$removed} stale upload(s).");
        return 0;
    }

    private function rrmdir(string $dir): void
    {
        if (! is_dir($dir)) return;
        $items = scandir($dir);
        if ($items === false) return;
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->rrmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}
