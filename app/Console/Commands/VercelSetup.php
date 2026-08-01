<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Self-check for the Vercel free stack (see VERCEL-FREE-STACK.md).
 *
 *   php artisan vercel:setup
 *
 * Reports which of the three free services are configured:
 *   - Neon Postgres  (DATABASE_URL)  → persistent database
 *   - Vercel Blob    (BLOB_READ_WRITE_TOKEN) → image/CV storage
 *   - Vercel KV      (KV_URL)        → cache (redis via predis)
 *
 * Exit code 0 when all three are set (useful in scripts/CI), 1 otherwise.
 */
class VercelSetup extends Command
{
    protected $signature = 'vercel:setup';

    protected $description = 'Check which free Vercel services (Neon, Blob, KV) are configured';

    public function handle(): int
    {
        $this->line('Vercel free stack — configuration check');
        $this->line('---------------------------------------');
        $this->newLine();

        $checks = [
            'DATABASE_URL' => [
                'label' => 'Neon Postgres (persistent database)',
                'done' => 'App uses Postgres — visits, appointments & content survive redeploys.',
                'missing' => 'Create: vercel.com → your project → Storage → Create Database → Neon Postgres. Vercel adds DATABASE_URL to the project env automatically.',
            ],
            'BLOB_READ_WRITE_TOKEN' => [
                'label' => 'Vercel Blob (image/CV storage)',
                'done' => 'Uploads are stored on Blob and served from its CDN (https://... URLs).',
                'missing' => 'Create: vercel.com → your project → Storage → Create Database → Vercel Blob. Copy the token and add it as BLOB_READ_WRITE_TOKEN (Production scope).',
            ],
            'KV_URL' => [
                'label' => 'Vercel KV (cache via redis/predis)',
                'done' => 'Cache uses Redis through predis (pure PHP, no extension needed).',
                'missing' => 'Create: vercel.com → your project → Storage → Create Database → Vercel KV. Add KV_URL as tls://... (TLS required by Upstash).',
            ],
        ];

        $allSet = true;

        foreach ($checks as $var => $info) {
            $set = (string) env($var) !== '';

            if ($set) {
                $this->info("  ✔ {$var} — {$info['label']}");
            } else {
                $allSet = false;
                $this->warn("  ✘ {$var} — {$info['label']} NOT set");
            }

            $this->line('     '.($set ? $info['done'] : $info['missing']));
            $this->newLine();
        }

        $this->line('Effective app settings:');
        $this->line('  database default : '.config('database.default').(config('database.default') === 'pgsql' ? ' (Neon)' : ' (committed SQLite fallback)'));
        $this->line('  cache store      : '.config('cache.default').(config('cache.default') === 'redis' ? ' (KV via predis)' : ' (database fallback)'));
        $this->line('  (if these look stale, run: php artisan config:clear)');
        $this->newLine();

        if ($allSet) {
            $this->info('🎉 All three free Vercel services are configured. Push to main to deploy.');

            return self::SUCCESS;
        }

        $this->line('Add the missing env vars in Vercel → Project → Settings → Environment Variables');
        $this->line('(scope: Production), then push to main — the app picks them up on deploy.');
        $this->line('Full walkthrough: VERCEL-FREE-STACK.md');

        return self::FAILURE;
    }
}
