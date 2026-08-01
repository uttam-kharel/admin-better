# Vercel Free Stack — Persistent Database, Images & Cache (100% free)

This guide wires **all three free Vercel services** into the Laravel app so your data, images and cache survive every redeploy — no credit card, no paid tier.

| Need | Vercel free option | Free tier | Credit card? |
|---|---|---|---|
| Database | **Neon Postgres** (Vercel Marketplace; "Vercel Postgres" was retired) | ~0.5 GB storage, shared compute | No |
| Images / CVs | **Vercel Blob** | ~1 GB storage/month, generous transfer | No |
| Cache | **Vercel KV** (Upstash Redis; "Vercel KV" retired) | ~10k commands/day | No |

> If you exceed a free limit, the service **pauses** until the monthly cycle resets — it never charges you.

---

## What's already wired in the code

Everything is **switch-ready**: the app detects each service's env var and starts using it automatically, falling back to the current setup (committed SQLite + base64 images) when the var is absent. So nothing breaks until you add the vars — and no code changes are needed when you do.

1. **Neon Postgres** — `config/database.php`:
   - `'default' => env('DB_CONNECTION', env('DATABASE_URL') ? 'pgsql' : 'sqlite')`
   - the `pgsql` connection reads `env('DATABASE_URL')`.
2. **Vercel Blob** — `app/Services/BlobStorage.php`:
   - every image/CV upload goes through it. With `BLOB_READ_WRITE_TOKEN` set it uploads to Blob (documented `PUT https://blob.vercel-storage.com/{pathname}` API) and saves the public CDN URL; without a token it falls back to a base64 data URI.
3. **Vercel KV (cache)** — `config/cache.php` + `config/database.php`:
   - with `KV_URL` (or `REDIS_URL`) set, `CACHE_STORE` auto-switches to `redis` and the redis client becomes **predis** (pure PHP — `predis/predis ^3.5` is a dependency) so it works on the vercel-php runtime with no PHP extensions.
4. **SQLite → Neon migration** — `app/Console/Commands/ImportSqliteToPgsql.php`:
   - `php artisan db:import-sqlite-to-pgsql` copies every table from the committed SQLite DB into Postgres (parent tables first, boolean conversion, FK-failure retry pass, `--tables=` / `--truncate` options).

---

## Step 1 — Create the Neon Postgres database (free)

1. Go to **vercel.com → your project (livewire-app) → Storage**.
2. Click **Create Database** → choose **Neon Postgres**.
   - Pick a region near you. Keep the free plan. Accept the link to your project.
3. Vercel auto-adds env vars including **`DATABASE_URL`** (`postgresql://...`).
   - Confirm it's set on **Production**: Project → **Settings → Environment Variables → Production**.

### Migrate the schema to Neon

**Option A — CI (recommended, repeatable):** the deploy workflow (`.github/workflows/deploy-vercel.yml`) already contains a migration step that runs **only when the `DATABASE_URL` GitHub secret exists**. Enable it:

1. In your GitHub repo: **Settings → Secrets and variables → Actions → New repository secret**.
2. Name: `DATABASE_URL` — value: your Neon connection string (copy it from Vercel → Storage → your Neon DB → Connect).
3. Next push to `main` will run `php artisan migrate --force` against Neon before deploying.

> If you later add migrations that use encryption, also add an `APP_KEY` repository secret (the CI step copies `.env.example`, whose `APP_KEY` is empty).

**Option B — one-off from your machine:**
```bash
DATABASE_URL="postgresql://..." DB_CONNECTION=pgsql php artisan migrate --force
```

### Copy your existing data (menus, doctors, visits, etc.)
The committed SQLite file still holds your current data. Copy it to Neon once:
```bash
DB_CONNECTION=pgsql DATABASE_URL="postgresql://..." php artisan db:import-sqlite-to-pgsql
```
- Skips framework tables (`migrations`, `sessions`, `cache`, `jobs`, …) automatically.
- Imports parent tables first, converts SQLite 0/1 booleans to Postgres `boolean`, and retries FK failures in a second pass.
- Add `--tables=doctors,departments` to import only some tables; add `--truncate` to wipe the destination tables first.

> **New visits tracked after this point are real and survive every redeploy.** (Previously they were wiped because the DB was committed to git.)

---

## Step 2 — Create Vercel Blob storage (free, for images/CVs)

1. **vercel.com → your project → Storage → Create Database → Vercel Blob.**
2. Copy the **`BLOB_READ_WRITE_TOKEN`** (`vercel_blob_rw_...`).
3. Add it to your Vercel project: **Project → Settings → Environment Variables** → name `BLOB_READ_WRITE_TOKEN`, scope **Production**.
4. Push to `main` (CI/CD redeploys).

From then on, every image uploaded in the admin (doctors, departments, services, …) and every CV from the Careers form is stored on Blob and served from its CDN — **no more base64 blobs in the DB, no more 404 /storage URLs.**

> Optionally also add `BLOB_READ_WRITE_TOKEN` as a GitHub secret (not required — the app reads it from Vercel env at runtime).

---

## Step 3 — Create Vercel KV (free, for cache)

1. **vercel.com → your project → Storage → Create Database → Vercel KV.**
2. Copy the **`KV_URL`** (`redis://default:...@...:6379`) from the Connect tab.
3. **TLS note:** Upstash/Vercel KV requires TLS — change the scheme from `redis://` to `tls://` in the value before saving (e.g. `tls://default:...@...:6379`). predis supports `tls://` natively.
4. Add it as a Vercel env var: name `KV_URL`, scope **Production**.
5. Push to `main`.

With `KV_URL` set, `CACHE_STORE` automatically becomes `redis` (via predis) and Laravel's cache (views, config, route cache etc.) lives in KV instead of the database.

> The app's **sessions** stay in the database by design (`SESSION_DRIVER=database`) — on Neon they persist and share the free storage, leaving your KV command budget for cache. To move sessions to KV too, set `SESSION_DRIVER=redis`.

---

## Step 4 — Verify

1. Push to `main` and watch **Actions** — deploy should be green (and, if you added the `DATABASE_URL` secret, the migration step runs too).
2. **Database:** visit a few pages, then check **/admin → Analytics** — the visit counter should grow and **survive the next deploy**.
3. **Images:** in the admin, upload a photo to any resource (e.g. Doctors). The stored value should be a `https://...public.blob.vercel-storage.com/...` URL, and it should still render after a redeploy.
4. **Cache:** `php artisan cache:clear` runs against KV once `KV_URL` is set (no errors = connected).

---

## FAQ / gotchas

- **Do I need to keep SQLite?** No — while `DATABASE_URL` is set it's simply unused. Keeping the file is harmless.
- **Does anything break locally?** No. Local `.env` has no `DATABASE_URL`/`KV_URL`/token, so the app stays on SQLite + base64 + database cache.
- **Why predis?** The `vercel-php` runtime has no `phpredis` extension; predis is pure PHP and works everywhere. `REDIS_CLIENT` still defaults to `phpredis` locally if you prefer the extension.
- **KV free limit reached?** It pauses until the monthly reset — nothing breaks, just cache stops persisting for that window.
- **Migrate command failed on a table?** It reports per-table results; rerun after fixing the cause, or use `--tables=` for the failing ones. FK failures are retried automatically in pass 2.
- **Session/queue tables on Postgres?** `sessions`, `jobs`, `cache` tables all exist in the migrations, so the database drivers work on Neon unchanged.
