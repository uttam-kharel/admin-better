# uttam.md — The Complete From-Zero Guide

This guide teaches you how this system works **from the very beginning**: how it was
created, what every piece is for, how the database connects, how it deploys to
Vercel, how CI/CD works, and how to **recreate the entire thing on a fresh machine
or a fresh GitHub repo**.

The project is a **hospital website + full admin panel**:

- **Public site** (`/`) — homepage, doctors, services, departments, contact, etc.
- **Admin panel** (`/admin`) — manage all content (doctors, services, settings, analytics, …)

---

## 1. The stack (what this system is made of)

| Piece | What it is | Why |
|---|---|---|
| **Laravel 13** (PHP 8.3) | Backend framework | Routing, database, auth, Blade templating |
| **Livewire 4** | Full-stack Reactivity | Admin CRUD + public pages without writing JS |
| **Alpine.js 3** | Lightweight UI behavior | Dropdowns, modals, scroll behavior |
| **Tailwind CSS 4** | Styling | Fast, utility-first, ships only what you use |
| **Vite 7** | Frontend bundler | Compiles JS/CSS into tiny production assets |
| **Chart.js** | Charts | Admin analytics graphs (admin-only bundle) |
| **PostgreSQL on Neon** | Database | Serverless Postgres that scales to zero |
| **Vercel (vercel-php runtime)** | Hosting | Serverless PHP + global CDN |
| **GitHub Actions** | CI/CD | Build + deploy on every push |

Check `composer.json` and `package.json` for the exact versions.

---

## 2. Creating the project from scratch (the commands that made this)

If you were starting over, these are the commands:

```bash
# 1. Create a new Laravel project
composer create-project laravel/laravel hospital-site
cd hospital-site

# 2. Add Livewire (full-stack reactive UI)
composer require livewire/livewire

# 3. Add icon packages (heroicons + lucide)
composer require blade-ui-kit/blade-heroicons blade-ui-kit/blade-icons
composer require technikermathe/blade-lucide-icons

# 4. Frontend tooling
npm install
npm install -D tailwindcss @tailwindcss/vite
npm install alpinejs @alpinejs/collapse chart.js

# 5. Configure Tailwind + Vite (resources/css/app.css, vite.config.js)

# 6. Local server
composer run dev          # or: npm run dev  +  php artisan serve
```

### Project layout (know where things live)

```
app/
  Http/Middleware/TrackPageVisits.php   # records every visit (IP, OS, language)
  Livewire/Pages/                      # public pages (HomepageIndex, DoctorsIndex, …)
  Livewire/Admin/                      # admin pages (Dashboard, ResourceManager, …)
  Models/                              # Eloquent models (Doctor, Service, PageVisit, …)
  Support/PublicCache.php              # shared cache keys for public content
  Console/Commands/                    # artisan commands (import SQLite→Postgres, Vercel setup)
api/index.php                          # Vercel PHP entrypoint (boots Laravel)
public/build/                          # compiled frontend assets (Vite output)
resources/views/                       # Blade templates (layouts, components, pages)
resources/js/                          # app.js (public), admin.js (charts), premium.js
routes/web.php                         # all routes
vercel.json                            # Vercel config (PHP runtime, rewrites)
.github/workflows/deploy-vercel.yml    # CI/CD pipeline
database/migrations/                   # schema (all tables)
.env.new.database                      # ← template for connecting ANY Postgres DB
```

---

## 3. The database — how it connects (read this carefully)

### Two databases in one system

- **Local dev:** SQLite (`database/database.sqlite`) — zero setup, just works.
- **Production:** PostgreSQL on **Neon** (serverless, free tier).

The app **auto-switches** the moment `DATABASE_URL` is set
(`config/database.php`):

```php
'default' => env('DB_CONNECTION', env('DATABASE_URL') ? 'pgsql' : 'sqlite'),
```

So: no `DATABASE_URL` → SQLite. `DATABASE_URL` set → Postgres. That's the whole
magic — the same code runs everywhere.

### POOLED vs UNPOOLED — the #1 thing people get wrong

Neon gives you **two** connection strings from the dashboard
(Project → Connect):

| String | What it is | Use it for |
|---|---|---|
| `DATABASE_URL` | **Pooled** (PgBouncer proxy) | The running app — fast, shares connections |
| `DATABASE_URL_UNPOOLED` | **Direct** connection | Migrations, imports, `tinker` — schema changes |

> ⚠️ **Never run `php artisan migrate` against the pooled URL.** The pool proxy
> rejects schema changes with `current transaction is aborted`. This bit us —
> the analytics tables failed to migrate until we used the unpooled string.

### Commands you'll actually run

```bash
# Test the connection
DB_CONNECTION=pgsql DATABASE_URL="$DATABASE_URL_UNPOOLED" php artisan tinker
#   → DB::select('select 1');  returns [{?column? => 1}]

# Create all tables (use the UNPOOLED URL!)
DB_CONNECTION=pgsql DATABASE_URL="$DATABASE_URL_UNPOOLED" php artisan migrate --force

# Import old SQLite data into Postgres (one-time migration)
DB_CONNECTION=pgsql DATABASE_URL="$DATABASE_URL_UNPOOLED" php artisan db:import-sqlite-to-pgsql
```

### Where the secrets live (never commit these)

- **Local:** `.env` (gitignored — see `.env.new.database` for the template)
- **Production:** Vercel project → **Settings → Environment Variables**
  - `DATABASE_URL` (pooled)
  - `DATABASE_URL_UNPOOLED` (direct — used by CI migrations)
  - `APP_KEY` — **must be the SAME key** in local, CI and Vercel, or encrypted
    sessions/cookies break
  - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://sih-hospital.vercel.app`
  - `BLOB_READ_WRITE_TOKEN` — Vercel Blob token for image/PDF uploads

### Full template

See **`.env.new.database`** — a copy-paste template with both URL-style and
split-variable options, plus the exact commands. It's committed to the repo
(on purpose — it contains placeholders, not secrets).

---

## 4. How deployment works on Vercel

### The secret sauce: `vercel-php`

Vercel doesn't natively run PHP, so this repo uses the
**`vercel-php` community runtime**. Three files make it work:

1. **`vercel.json`** — declares the PHP function and routes:
   ```json
   {
     "framework": null,
     "outputDirectory": "public",
     "functions": { "api/index.php": { "runtime": "vercel-php@0.7.4" } },
     "routes": [
       { "src": "/build/(.*)", "dest": "/build/$1" },   // static assets
       { "src": "/(.*)", "dest": "/api/index.php" }      // everything else → PHP
     ]
   }
   ```
   All traffic goes through `api/index.php`, which boots Laravel.

2. **`api/index.php`** — the Vercel entrypoint. It:
   - Resets `SCRIPT_NAME` so Laravel sees real URLs (else every route 404s)
   - Points all file caches at `/tmp` (Vercel Functions have a **read-only
     filesystem** — only `/tmp` is writable)
   - Routes Livewire uploads to a `/tmp` disk (otherwise image uploads 500)
   - Sets safe defaults for sessions, cache, queue, logs

3. **`public/` as the output** — Vercel serves the built assets from `public/`,
   and the PHP function handles every other path.

### The build, step by step

When you push to GitHub, the **CI pipeline** (see section 5) runs:

```bash
composer install --no-interaction --prefer-dist --no-progress   # PHP deps
npm ci                                                          # JS deps
npm run build                                                   # Vite → public/build (JS/CSS)
php artisan migrate --force                                     # schema (only if DATABASE_URL secret set)
php artisan view:cache                                          # compile all Blade views
npx vercel --prod --yes                                         # deploy to production
```

Vercel then uploads the project (with `public/build` assets) and spins up the
PHP function. Environment variables from the Vercel dashboard are injected at
runtime.

---

## 5. CI/CD — how GitHub Actions deploys for you

The pipeline lives in **`.github/workflows/deploy-vercel.yml`**. It triggers on:

| Event | What happens |
|---|---|
| Push to `main` | **Production deploy** (`sih-hospital.vercel.app`) |
| Pull request | **Preview deploy** with a unique URL + PR comment |
| Manual (`workflow_dispatch`) | Re-deploy production |

### Required GitHub secrets

`Settings → Secrets and variables → Actions`:

- `VERCEL_TOKEN` — from https://vercel.com/account/tokens
- `VERCEL_ORG_ID` — your Vercel team ID (`team_…` from `.vercel/project.json`)
- `VERCEL_PROJECT_ID` — your project ID (`prj_…` from `.vercel/project.json`)
- `DATABASE_URL` (optional) — if set, CI also runs migrations

### What CI does beyond deploying

1. **JS bundle budget gate** — fails the build if the public JS bundle exceeds
   350KB, so nobody accidentally puts Chart.js back into the public entry.
2. **Runs migrations** on Neon (skips gracefully if the secret is unset).
3. **`view:cache`** — compiles every Blade template at build time (faster cold
   starts + catches broken views before deploy).
4. **Warms caches** after deploy by hitting `/`, `/services`, `/departments`,
   `/doctors`, so the first real visitor doesn't pay the cold-cache cost.

> Why **not** `config:cache` / `route:cache`? `config:cache` bakes build-time
> env, and `route:cache` bakes the APP_KEY-derived Livewire update URL — both
> differ at Vercel runtime. That exact class of bug broke admin updates once.
> View caching is safe; config/route caching is deliberately skipped.

---

## 6. Replicating the whole system (fresh machine / fresh repo)

Follow this exact order and you'll have a working copy in ~10 minutes.

### On a new machine (local dev)

```bash
# 1. Clone
git clone https://github.com/uttam-kharel/admin-better.git
cd admin-better

# 2. PHP + JS dependencies
composer install
npm install

# 3. Environment
cp .env.example .env
php artisan key:generate
# → For Postgres: copy the relevant lines from .env.new.database into .env

# 4. Database (SQLite for local; Postgres optional)
touch database/database.sqlite
php artisan migrate

# 5. Build frontend assets
npm run build

# 6. Run it
composer run dev    # server + queue + logs + vite in one
```

### Replicate the Vercel deployment (new Vercel project)

```bash
# 1. Login
npx vercel login

# 2. Link the repo to a Vercel project (creates .vercel/project.json)
npx vercel link
#    → answer the prompts; picks up vercel.json automatically

# 3. Set the environment variables in the dashboard
#    (or with the CLI:)
npx vercel env add DATABASE_URL production
npx vercel env add DATABASE_URL_UNPOOLED production
npx vercel env add APP_KEY production
npx vercel env add BLOB_READ_WRITE_TOKEN production

# 4. Deploy
npx vercel --prod
```

### Replicate CI/CD (new GitHub repo)

1. Push the repo to GitHub.
2. Add the four secrets from section 5 to the new repo.
3. Update `vercel.json` / `.vercel/project.json` for the new Vercel project
   (re-run `npx vercel link`).
4. Update the hardcoded warm-up URL in `.github/workflows/deploy-vercel.yml`
   (`https://sih-hospital.vercel.app` → your URL).
5. Push to `main` — CI deploys production automatically.

---

## 7. Performance & optimization (what we did, and why)

Measured and fixed in one pass — this is the "make it fast" playbook:

| Problem | Fix | Result |
|---|---|---|
| Chart.js (~210KB) loaded on every page | Moved to an **admin-only JS bundle** | Public JS **528KB → 318KB** (gzip 180 → 109KB) |
| `SiteSetting::first()` ran 6+ times per page | `SiteSetting::cached()` with DB cache store | Settings queried **once** |
| Menus queried 4× per page | Cached menu trees | Header 1 query, footer 3 → 1 |
| Homepage loaded 17+ tables per request | Entire static homepage content cached | **19 → 2 queries** |
| Doctors/services/departments pages reloaded everything | Cached via `PublicCache` helper | Same 1-read pattern |
| Sidebar badges queried every admin request | Cached 60s | 3 fewer queries |
| Cold caches after deploy | CI warms `/`, `/services`, `/departments`, `/doctors` | First visitor is fast |

**Cache invalidation:** every cache is flushed automatically whenever any admin
save/delete happens (`PublicCache::flush()` in the resource manager) — plus a
1-hour TTL backstop for direct DB edits. Edit a doctor → homepage reflects it
immediately.

**Why `CACHE_STORE=database`?** Vercel serverless functions have ephemeral
filesystems — the `file` cache wouldn't be shared across instances. The
**database cache store** (a table in Postgres) is consistent everywhere, and
Neon's serverless Postgres makes it effectively free at this scale.

---

## 8. Real lessons learned (the bugs that bit us)

1. **Postgres booleans.** PDO binds PHP `true` as integer `1`. Postgres rejects
   `boolean = integer`. Every visit-tracking insert was silently failing because
   of this — the middleware's try/catch hid it. Fix: use driver-correct boolean
   literals (`true` → `TRUE`/`'1'` cast properly), and **never swallow errors —
   `report()` them**.
2. **Pooled vs unpooled migrations.** See section 3. Use `DATABASE_URL_UNPOOLED`
   for anything that changes the schema.
3. **`GROUP BY` on JSON columns fails in Postgres.** Hydrate JSON fields in PHP
   instead of grouping by them in SQL.
4. **Read-only filesystem on Vercel.** Uploads and caches must go to `/tmp`
   (`api/index.php` handles this; `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK`).
5. **`config:cache`/`route:cache` break serverless Livewire** (baked env +
   APP_KEY-derived URLs). Only `view:cache` is safe in CI.
6. **Scroll-position and DOM swaps.** `wire:navigate` destroys and recreates the
   sidebar; an in-memory Alpine store persists the scroll position across
   navigations (no storage APIs needed).

---

## 9. Where to go next (roadmap ideas)

- **Storing doctor images/PDFs properly** — the plumbing is ready:
  `BLOB_READ_WRITE_TOKEN` + the `livewire-tmp` upload disk. Next step is wiring
  doctor uploads to Vercel Blob and storing the returned URL on the model.
- **Auth hardening** — 2FA, roles/permissions per admin.
- **Analytics retention** — raw IPs are personal data; add a retention window or
  IP anonymization toggle for GDPR-style compliance.
- **Search** — full-text search across doctors/services (Postgres `tsvector`).
- **Notifications** — queue job on new contact submissions (queue is ready, just
  needs a worker).

---

*Questions? Start at section 3 (database) — that's where 90% of setup confusion
lives. Everything else is standard Laravel/Livewire.*
