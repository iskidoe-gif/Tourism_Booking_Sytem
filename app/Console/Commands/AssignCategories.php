<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TourPackage;

class AssignCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packages:assign-categories {--create-samples : Create one sample package for categories with no packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign categories to existing tour packages using simple keyword heuristics and optionally create sample packages for missing categories.';

    public function handle(): int
    {
        $this->info('Starting category assignment...');

        $labels = TourPackage::categoryLabels();

        $keywordMap = [
            'natural' => ['beach', 'waterfall', 'falls', 'island', 'lagoon', 'reef', 'cave', 'mountain', 'lake', 'river'],
            'cultural' => ['church', 'heritage', 'museum', 'monument', 'churches', 'histor', 'heritage', 'ancestral'],
            'recreational' => ['park', 'adventure', 'zipline', 'rafting', 'hike', 'surf', 'dive', 'kayak', 'snorkel', 'tour'],
            'accommodation' => ['hotel', 'resort', 'inn', 'homestay', 'lodg', 'villa', 'bungalow', 'guesthouse', 'hostel'],
            'events' => ['festival', 'event', 'celebration', 'fiesta'],
            'ecotourism' => ['sanctuary', 'reserve', 'conservation', 'eco', 'protected'],
        ];

        $updated = 0;
        $packages = TourPackage::all();
        foreach ($packages as $p) {
            $text = strtolower($p->name . ' ' . ($p->description ?? '') . ' ' . ($p->location ?? ''));
            $found = null;
            foreach ($keywordMap as $cat => $keywords) {
                foreach ($keywords as $kw) {
                    if (str_contains($text, $kw)) {
                        $found = $cat;
                        break 2;
                    }
                }
            }

            if ($found) {
                if ($p->category !== $found) {
                    $p->category = $found;
                    $p->save();
                    $updated++;
                }
            }
        }

        $this->info("Updated {$updated} package(s).");

        if ($this->option('create-samples')) {
            $created = 0;
            foreach (array_keys($labels) as $cat) {
                $exists = TourPackage::where('category', $cat)->exists();
                if (! $exists) {
                    TourPackage::create([
                        'destination_id' => null,
                        'name' => 'Sample ' . $labels[$cat],
                        'description' => $labels[$cat] . ' sample package created automatically.',
                        'location' => 'Bolinao, Pangasinan',
                        'price' => 0.00,
                        'duration_days' => 1,
                        'max_guests' => 1,
                        'image' => null,
                        'category' => $cat,
                        'status' => 'inactive',
                        'rating' => 0.00,
                    ]);
                    $created++;
                }
            }
            $this->info("Created {$created} sample package(s) for missing categories.");
        }

        $this->info('Done.');
        return 0;
    }
}
