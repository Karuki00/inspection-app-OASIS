<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Check if running as a desktop sidecar or in desktop mode
        if (config('app.env') === 'production') {
            // Get user's Windows AppData path (e.g. C:\Users\Username\AppData\Roaming\InspectionApp)
            $appDataDir = env('APPDATA') . '/InspectionApp';
            $dbPath = $appDataDir . '/database.sqlite';

            // Create folder if it doesn't exist
            if (!file_exists($appDataDir)) {
                mkdir($appDataDir, 0755, true);
            }

            // Create empty database.sqlite file if missing
            if (!file_exists($dbPath)) {
                touch($dbPath);
            }

            // Dynamically override SQLite database path
            Config::set('database.connections.sqlite.database', $dbPath);
        }
    }
}
