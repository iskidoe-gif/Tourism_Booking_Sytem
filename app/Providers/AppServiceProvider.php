<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // If the configured DB is Postgres but it's unreachable in local/debug,
        // fall back to a local SQLite file so the app can boot for local testing.
        if (env('DB_CONNECTION') === 'pgsql' && (App::environment('local') || env('FORCE_APP_DEBUG', 'false') === 'true')) {
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                // create sqlite file if missing
                $sqlite = database_path('database.sqlite');
                if (! file_exists($sqlite)) {
                    @mkdir(dirname($sqlite), 0775, true);
                    @touch($sqlite);
                }
                Config::set('database.default', 'sqlite');
                Config::set('database.connections.sqlite.database', $sqlite);
            }
        }
    }
}
