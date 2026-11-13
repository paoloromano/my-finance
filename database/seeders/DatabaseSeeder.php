<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\FinanceBackup;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Carica parametri, scadenze e budget iniziali (solo se il database è vuoto).
     * L'utente admin si crea con: php artisan app:create-admin
     */
    public function run(): void
    {
        if (Setting::query()->exists()) {
            $this->command?->warn('Dati già presenti: seeder saltato.');

            return;
        }

        app(FinanceBackup::class)->resetToDefaults();
    }
}
