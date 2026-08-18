# Docker deployment on Vercel (branch: `production`)

This branch runs the app on Vercel as a **Docker container** instead of the old
`vercel-php` serverless functions. `main` is untouched — the serverless config
(`api/index.php`, `vercel-php` runtime) still lives there and can be restored at
any time by redeploying `main`.

## What changed (vs `main`)

| File | Purpose |
| --- | --- |
| `Dockerfile.vercel` | Multi-stage image: Composer deps → Vite assets → FrankenPHP runtime |
| `Caddyfile` | FrankenPHP site config; binds to Vercel's `$PORT`, serves `public/`, rewrites everything else to Laravel |
| `docker/entrypoint.sh` | Boot script: ephemeral `/tmp` cache defaults (Vercel env wins), checks `APP_KEY`, caches config, starts FrankenPHP |
| `docker/php.ini` | Upload caps (12M, aligned with Livewire) + OPcache tuned for an immutable image |
| `.dockerignore` | Keeps secrets/noise out of the image (`.env`, node_modules, vendor, tests…) |
| `compose.yaml` | Run the production image locally: `docker compose up --build` → http://localhost:3000 |
| `vercel.json` | Declares a container service (`runtime: container`, entrypoint `Dockerfile.vercel`) + catch-all rewrite |

`api/index.php` and `api/php.ini` are still present but **unused** — they belong
to the old serverless runtime.

## How it works

- Vercel builds `Dockerfile.vercel`, stores the image in the Vercel Container
  Registry, and runs it as a Function that autoscales and scales to zero.
- FrankenPHP (Caddy + PHP 8.4) serves `/app/public`; `php_server` hands every
  non-static request to Laravel's front controller.
- The container filesystem is **not persistent**, so the entrypoint keeps every
  file-backed cache in `/tmp`, sessions in cookies, and logs to stderr
  (same policy as the old `api/index.php`).
- `config:cache` runs at **boot** (not build) so the env vars Vercel injects at
  runtime are honored. `route:cache` is skipped because `routes/admin.php` uses
  a closure.

## Deploying

1. Push this branch:
   ```bash
   git push -u origin production
   ```
2. In **Vercel → Project → Settings → Git**, connect the `production` branch so
   pushes to it create production deployments. (Or, from this branch locally:
   `vercel deploy --prod`.)
3. Make sure the project's **Production** environment variables are set —
   the same ones the serverless runtime used, at minimum `APP_KEY`, plus the
   free stack (`DATABASE_URL` for Neon Postgres, `BLOB_READ_WRITE_TOKEN` for
   Blob, `KV_URL` for KV/Redis). Verify with `php artisan vercel:setup`.
4. Deploy and check `/up` and `/admin/login`.

## Testing locally

```bash
docker compose up --build
# open http://localhost:3000
```

`compose.yaml` reads `.env` so the app boots with your local config. To mimic
Vercel exactly, run without `env_file` and pass `-e` vars instead.

## Troubleshooting

- **502 Bad Gateway** → Caddy must listen on `:{$PORT:80}` — don't hardcode the
  port in `Caddyfile`.
- **Routes other than `/` 404** → `root * /app/public` + `php_server` must be
  present in `Caddyfile`.
- **`APP_KEY` missing at boot** → the entrypoint refuses to start; add it under
  Vercel → Project → Settings → Environment Variables (Production scope).
- **Changes not reflected** → the image is immutable and OPcache
  `validate_timestamps=0`; every deploy rebuilds the image.
