<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Safe production seeding: runs the full DatabaseSeeder ONLY when the database
 * is empty. The seeders use raw `insert()` calls with explicit IDs, so running
 * them against an already-populated database fails with duplicate-key errors
 * (or worse, duplicates content). This command is what CI runs after migrate.
 *
 * Usage:
 *   php artisan db:seed-if-empty
 */
class DbSeedIfEmpty extends Command
{
    protected $signature = 'db:seed-if-empty {--force : Run even in production}';

    protected $description = 'Seed the database only when it contains no content data';

    /**
     * Sentinel tables. If ANY of these has rows, the database is considered
     * already populated and seeding is skipped.
     */
    private const CONTENT_TABLES = [
        'site_settings',
        'departments',
        'doctors',
        'services',
    ];

    public function handle(): int
    {
        $existing = collect(self::CONTENT_TABLES)
            ->filter(fn (string $table) => DB::table($table)->exists())
            ->values();

        if ($existing->isNotEmpty()) {
            $this->line(sprintf(
                'Database already has data in: <fg=yellow>%s</> — skipping seeding.',
                $existing->implode(', ')
            ));
            $this->line('To force a re-seed, run: <fg=cyan>php artisan db:seed --force</>');

            return self::SUCCESS;
        }

        $this->info('Database is empty — running the full DatabaseSeeder…');
        $this->call('db:seed', ['--force' => true]);

        $this->info('Seeding complete.');

        return self::SUCCESS;
    }
}
