<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Copies every table from the committed SQLite database into the current
 * Postgres connection (e.g. Neon Postgres on Vercel).
 *
 * Usage (after DATABASE_URL is set to the Neon connection string):
 *   DB_CONNECTION=pgsql DATABASE_URL="postgresql://..." php artisan db:import-sqlite-to-pgsql
 *
 * Run `php artisan migrate --force` on Postgres first so the schema exists.
 */
class ImportSqliteToPgsql extends Command
{
    protected $signature = 'db:import-sqlite-to-pgsql
        {--truncate : Truncate destination tables before importing}
        {--tables=* : Only import these tables (comma-separated or repeatable)}';

    protected $description = 'Copy all data from the committed SQLite DB into Postgres (e.g. Neon on Vercel)';

    public function handle(): int
    {
        $sqlite = DB::connection('sqlite');
        $pg = DB::connection('pgsql');

        $tables = $this->tables($sqlite);

        $only = collect($this->option('tables'))
            ->flatMap(fn ($t) => explode(',', $t))
            ->map('trim')
            ->filter()
            ->values();

        if ($only->isNotEmpty()) {
            $tables = $tables->filter(fn ($t) => $only->contains($t));
        }

        if ($tables->isEmpty()) {
            $this->error('No tables to import.');

            return self::FAILURE;
        }

        $this->info('Importing into: '.$pg->getDatabaseName());
        $this->line('Tables: '.$tables->implode(', '));

        $imported = [];
        $failed = [];

        // Pass 1: try every table (parents first, thanks to the priority sort).
        foreach ($tables as $table) {
            $count = $this->importTable($sqlite, $pg, $table);

            if ($count === null) {
                $failed[] = $table;
            } else {
                $imported[$table] = $count;
            }
        }

        // Pass 2: retry failures once — FK parents may now exist in Postgres.
        if ($failed !== []) {
            $retry = $failed;
            $failed = [];

            foreach ($retry as $table) {
                $count = $this->importTable($sqlite, $pg, $table);

                if ($count === null) {
                    $failed[] = $table;
                } else {
                    $imported[$table] = $count;
                }
            }
        }

        foreach ($imported as $table => $count) {
            $this->info("  ✔ {$table}: {$count} rows");
        }

        foreach ($failed as $table) {
            $this->warn("  ✘ {$table}: FAILED (see error above) — inspect manually");
        }

        $this->newLine();
        $this->info('Done. '.count($imported).' tables imported, '.count($failed).' failed.');

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    protected function tables($sqlite): \Illuminate\Support\Collection
    {
        $rows = $sqlite->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name NOT IN ('migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'sessions')");

        // Parent tables first so FK references exist when children are inserted.
        $priority = ['admin_users', 'users', 'roles', 'departments', 'services', 'categories', 'blog_categories', 'doctors', 'blogs', 'posts', 'careers', 'appointments', 'contacts', 'page_visits', 'settings', 'menu_items', 'menus'];

        return collect($rows)->pluck('name')->sortBy(fn ($t) => array_search($t, $priority, true) === false ? 999 : array_search($t, $priority, true))->values();
    }

    /**
     * @return int|null Number of rows imported, or null on failure.
     */
    protected function importTable($sqlite, $pg, string $table): ?int
    {
        try {
            if ($this->option('truncate')) {
                $pg->statement("TRUNCATE TABLE \"{$table}\" CASCADE");
            }

            $columns = collect($sqlite->select("PRAGMA table_info(\"{$table}\")"))
                ->map(fn ($c) => $c->name)
                ->reject(fn ($c) => $c === 'id')
                ->values();

            $total = 0;

            $sqlite->table($table)->orderBy('id')->chunk(500, function ($rows) use ($pg, $table, $columns, &$total) {
                foreach ($rows as $row) {
                    $data = [];

                    foreach ($columns as $col) {
                        $value = $row->{$col};

                        // SQLite stores booleans as 0/1; PG needs true/false.
                        if (is_int($value) && in_array($value, [0, 1], true) && $this->isBooleanColumn($pg, $table, $col)) {
                            $value = (bool) $value;
                        }

                        // SQLite stores timestamps as 'Y-m-d H:i:s'; PG accepts that format.
                        $data[$col] = $value;
                    }

                    if ($data !== []) {
                        $pg->table($table)->insert($data);
                    }

                    $total++;
                }
            });

            return $total;
        } catch (\Throwable $e) {
            $this->error("  [{$table}] ".$e->getMessage());

            return null;
        }
    }

    protected function isBooleanColumn($pg, string $table, string $column): bool
    {
        try {
            $row = $pg->selectOne("SELECT data_type FROM information_schema.columns WHERE table_name = ? AND column_name = ?", [$table, $column]);

            return $row !== null && strtolower((string) $row->data_type) === 'boolean';
        } catch (\Throwable $e) {
            return false;
        }
    }
}
