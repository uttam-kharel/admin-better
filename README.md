# Shubham International Hospital — Laravel + Livewire (Vercel Deployment)

> **Live now:** https://livewire-app-gamma.vercel.app &nbsp;·&nbsp; **Custom domain:** `v2.sih.com.np` (pending one Cloudflare DNS record — see §6)

This is a **Laravel 13 + Livewire 4** hospital website (public site + admin panel) deployed to
**Vercel serverless functions** using the community `vercel-php` runtime. This README documents
**everything that was done** so the exact setup can be replicated on another project.

> 📚 **Companion docs:** [**DOMAIN-MAP.md**](DOMAIN-MAP.md) — full mapping of the custom domain
> `v2.sih.com.np` → Vercel (what's done, the one Cloudflare record you add). &nbsp;·&nbsp;
> [**DATABASE-SETUP.md**](DATABASE-SETUP.md) — connect a Postgres database to make the site fully
> writable (fixes the read-only SQLite limitation in §7). &nbsp;·&nbsp; The alternative InfinityFree
> hosting path is in [**DEPLOYMENT.md**](DEPLOYMENT.md).

---

## Table of contents

1. [What this project is](#1-what-this-project-is)
2. [How the Vercel deployment works](#2-how-the-vercel-deployment-works)
3. [The deployment files](#3-the-deployment-files)
4. [Production environment variables](#4-production-environment-variables)
5. [Deploying (step by step)](#5-deploying-step-by-step)
6. [Custom domain (v2.sih.com.np)](#6-custom-domain-v2sihcomnp)
7. [Known limitations on Vercel](#7-known-limitations-on-vercel)
8. [Troubleshooting](#8-troubleshooting)
9. [Replicating on another project](#9-replicating-on-another-project)

---

## 1. What this project is

- **Stack:** Laravel 13.16, Livewire 4.3, Blade, Tailwind CSS v4 via Vite, SQLite locally.
- **Public pages** (all Livewire page components in `app/Livewire/Pages/`): Home, Services,
  Doctors, Departments, Blogs, Health Packages, Gallery, Careers, Contact, Appointment, CMS pages.
- **Admin panel** at `/admin` guarded by `auth:admin` (custom `Authenticate` middleware +
  `AdminUser` model). ~20 resources (doctors, services, blogs, gallery, testimonials, FAQs,
  job openings, menus, settings, admin users…) share one generic Livewire
  `admin::resource-manager` component.
- **Data:** content lives in **SQLite** (`database/database.sqlite`, ~35 tables, full content).
  On Vercel this file is uploaded as-is and served **read-only** — pages render with real data.
- The site is deployed on branch `refactors`.

---

## 2. How the Vercel deployment works

Vercel runs PHP code as **serverless functions**. Laravel is deployed as a single function whose
entrypoint is `api/index.php`. A rewrite in `vercel.json` sends **every** request to that function,
and `api/index.php` simply reuses Laravel's normal front controller (`public/index.php`) — exactly
like `php artisan serve`, but on Vercel's infrastructure.

```
Browser → https://livewire-app-gamma.vercel.app/
              ▼  (vercel.json route "/(.*)" → /api/index.php)
         api/index.php   (reset SCRIPT_NAME, point caches at /tmp, require public/index.php)
              ▼  (require public/index.php → boots Laravel, handles the request)
         Laravel routes (Livewire) → HTML / JSON
```

### The three serverless constraints that shape this setup

| Constraint | Consequence | Fix |
|---|---|---|
| **Read-only filesystem** (only `/tmp` is writable) | Laravel caches/logs/sessions can't write to `storage/` | `api/index.php` points every cache + compiled views at `/tmp` via `putenv` |
| **PHP runtime reports `SCRIPT_NAME` as `/api/index.php`** | Laravel treats `/api` as the base URL and strips it from every path → all routes 404 | `api/index.php` resets `SCRIPT_NAME`/`PHP_SELF` to `/index.php` |
| **Vercel proxies HTTPS at the edge** | Laravel sees `http://` internally and generates `http://` asset URLs → browsers block mixed content, CSS dies | `bootstrap/app.php` adds `$middleware->trustProxies(at: '*')` |
| **Vite dev artifacts can ship** | `public/hot` makes `@vite` render `localhost:5173` links → no CSS | `.vercelignore` excludes `/public/hot` |
| **No build step on Vercel** (`framework: null`) | Compiled frontend isn't generated remotely | Run `npm run build` locally so `public/build/` uploads with the deploy |

---

## 3. The deployment files

### 3a. `api/index.php` — the Vercel PHP entrypoint

```php
<?php

/*
 * Vercel entrypoint for Laravel (vercel-php runtime).
 *
 * Every request is rewritten here by vercel.json, then handed to Laravel's
 * normal front controller (public/index.php) — exactly like `php artisan serve`.
 */

// The PHP runtime reports SCRIPT_NAME as /api/index.php; Laravel derives its
// base URL from it and would strip /api from every path (all routes 404).
// Reset it so Laravel sees the real URLs.
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Vercel Functions run on a read-only filesystem (only /tmp is writable), so
// point every file-based cache at /tmp and log to stderr (Vercel Function Logs).
// Each is guarded so an explicit env var set in the Vercel dashboard wins.
if (!getenv('APP_CONFIG_CACHE'))    putenv('APP_CONFIG_CACHE=/tmp/config.php');
if (!getenv('APP_ROUTES_CACHE'))    putenv('APP_ROUTES_CACHE=/tmp/routes-v7.php');
if (!getenv('APP_EVENTS_CACHE'))    putenv('APP_EVENTS_CACHE=/tmp/events.php');
if (!getenv('APP_PACKAGES_CACHE'))  putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
if (!getenv('APP_SERVICES_CACHE'))  putenv('APP_SERVICES_CACHE=/tmp/services.php');
if (!getenv('VIEW_COMPILED_PATH'))  putenv('VIEW_COMPILED_PATH=/tmp/views');
if (!getenv('LOG_CHANNEL'))         putenv('LOG_CHANNEL=stderr');
if (!getenv('SESSION_DRIVER'))      putenv('SESSION_DRIVER=cookie');
if (!getenv('CACHE_STORE'))         putenv('CACHE_STORE=array');
if (!getenv('QUEUE_CONNECTION'))    putenv('QUEUE_CONNECTION=sync');

require __DIR__.'/../public/index.php';
```

| Line | Why it exists |
|---|---|
| `SCRIPT_NAME` / `PHP_SELF` → `/index.php` | Without it, Laravel derives `baseUrl = /api` from `SCRIPT_NAME=/api/index.php` and strips `/api` from **every** path → all routes 404. |
| `APP_CONFIG_CACHE` … `APP_SERVICES_CACHE` → `/tmp` | Read-only filesystem would break Laravel when it tries to write cached config/routes/events/packages/services files. `/tmp` is writable. |
| `VIEW_COMPILED_PATH` → `/tmp/views` | Compiled Blade views must be written somewhere writable. |
| `LOG_CHANNEL=stderr` | Logs to the function's stderr, which appears in **Vercel Function Logs**. |
| `SESSION_DRIVER=cookie` | Database/file sessions can't work (read-only + ephemeral). Cookie sessions persist in the browser and survive cold starts. |
| `CACHE_STORE=array` | In-memory cache per-request — no persistence needed for a serverless site. |
| `QUEUE_CONNECTION=sync` | Jobs run inline instead of needing a queue worker. |
| `getenv()` guards | A value set in the Vercel dashboard **overrides** these defaults. |

### 3b. `vercel.json`

```json
{
  "$schema": "https://openapi.vercel.sh/vercel.json",
  "version": 2,
  "framework": null,
  "outputDirectory": "public",
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.7.4",
      "maxDuration": 30
    }
  },
  "routes": [
    {
      "src": "/build/(.*)",
      "dest": "/build/$1"
    },
    {
      "src": "/(.*)",
      "dest": "/api/index.php"
    }
  ]
}
```

| Key | Purpose |
|---|---|
| `"framework": null` | Tells Vercel **not** to auto-detect a framework. Without this, Vercel sees the Vite `package.json` and tries to run `npm run build` → fails because `vendor/` isn't present during the build. |
| `"outputDirectory": "public"` | Satisfies Vercel's build output check and marks `public/` as the static output. |
| `"functions"` → `runtime: "vercel-php@0.7.4"` | The community PHP runtime. **0.7.4 = PHP 8.3** (matches this project's `composer.json` `"php": "^8.3"`). See the ⚠️ note below about **why not 0.9.0**. |
| `"maxDuration": 30` | Function execution limit. |
| `"routes"` → `/build/(.*)` → `/build/$1` | Serves compiled Vite assets (in `public/build/`) statically from Vercel's edge. **Must come before the catch-all** or CSS requests get swallowed by PHP and 404. |
| `"routes"` → `/(.*)` → `/api/index.php` | The catch-all rewrite that sends every request to the Laravel function. |

> ⚠️ **Why `vercel-php@0.7.4` and not `0.9.0`?** (This was the hardest bug in this deployment.)
> A fresh build with `vercel-php@0.9.0` **succeeds** (composer installs, `Creating lambda`,
> `Build Completed`) but **every request 500s** with:
> `Error [ERR_MODULE_NOT_FOUND]: Cannot find module '/var/task/launcher.launcher' imported from /opt/rust/nodejs.js`
> The 0.9.0 builder produces a broken function bundle on Vercel's current infrastructure when
> the lambda is built **fresh**. (The other project, `laravel-vercel`, still used 0.9.0 and worked
> **only because** each deploy restored a **build cache** containing a pre-built lambda.)
> **Fix:** pin `vercel-php@0.7.4` (PHP 8.3 — a stable, widely-deployed version) and redeploy.
> The runtime change forces a fresh, correctly-built lambda. Verified live.

> 🎨 **Assets:** `framework: null` means Vercel runs **no** build step, so the compiled frontend
> is **not** generated remotely. Run `npm run build` locally before deploying — it creates
> `public/build/` (manifest + compiled CSS/fonts) which uploads with the deploy.

### 3c. `.vercelignore`

```gitignore
# Vercel re-installs Composer dependencies during the build.
/vendor

# Local development files — never upload.
/.freebuff
/.env
/.env.local
/node_modules
/tests
/.git
/.kilo
/.github
/.htaccess
/php-dev.ini

# Local runtime artifacts (logs, compiled views, caches).
/storage/logs
/storage/framework/cache
/storage/framework/sessions
/storage/framework/views

# Vite dev-mode marker — never upload. Its presence makes @vite render links
# to the dev server (localhost:5173) and break CSS.
/public/hot

# public/storage is a symlink to storage/app/public — breaks on Vercel.
/public/storage

# NOTE: database/database.sqlite is intentionally NOT ignored — it carries the
# site's content (read-only on Vercel) so the pages render with real data.
```

| Entry | Why |
|---|---|
| `/vendor` | Vercel re-installs Composer dependencies during the build itself. |
| `/.env`, `/.env.local`, `/.freebuff`, `/node_modules`, `/tests`, `/.git`, `/.kilo`, `/.github`, `/.htaccess`, `/php-dev.ini` | Local/development/host-only files that must never ship. |
| `/storage/*` | Local runtime artifacts (logs, caches) — regenerated or redirected to `/tmp` at runtime. |
| `/public/hot` | A 17-byte Vite **dev-mode** marker. If uploaded, `@vite` renders `localhost:5173` links instead of built assets → CSS silently breaks. |
| `/public/storage` | Symlink to `storage/app/public` — broken on Vercel's filesystem. |
| *(kept)* `database/database.sqlite` | **Intentionally uploaded.** It holds all the site's content. Vercel serves it read-only, so pages render with real data. |

### 3d. `bootstrap/app.php` — one addition

```php
->withMiddleware(function (Middleware $middleware): void {
    // Trust Vercel's edge proxy so Laravel sees the original https scheme
    // (X-Forwarded-Proto) and generates https:// asset/route URLs instead
    // of http:// ones (otherwise browsers block assets as mixed content).
    $middleware->trustProxies(at: '*');

    $middleware->alias([
        'auth' => \App\Http\Middleware\Authenticate::class,
    ]);
})
```

**Why:** without `trustProxies(at: '*')`, the homepage's `<link rel="stylesheet">` rendered as
`http://livewire-app-gamma.vercel.app/...`, which Chrome blocks as **mixed content** — CSS dead
even though the files existed. Now everything renders as `https://`.

---

## 4. Production environment variables

Set on Vercel → project **livewire-app** → **Settings → Environment Variables**
(for the **Production** environment). All are marked **Sensitive/Encrypted**.

| Variable | Value | Why |
|---|---|---|
| `APP_KEY` | (from local `.env`, a `base64:...` value) | Laravel encryption key — must match local so existing content works. |
| `APP_ENV` | `production` | Production mode. |
| `APP_DEBUG` | `false` | Hide stack traces. |
| `APP_URL` | `https://v2.sih.com.np` | Canonical URL for absolute-URL generation. |
| `SESSION_DRIVER` | `cookie` | Serverless-safe sessions (persist in the browser). |
| `CACHE_STORE` | `array` | No persistent cache needed. |
| `QUEUE_CONNECTION` | `sync` | Jobs run inline. |
| `LOG_CHANNEL` | `stderr` | Logs appear in Vercel Function Logs. |
| `DB_CONNECTION` | `sqlite` | Uses the uploaded `database/database.sqlite` (read-only). |

CLI equivalent (from the project folder, after `vercel login` / `vercel link`):

```bash
echo "base64:...your-key..." | vercel env add APP_KEY production
echo "production"             | vercel env add APP_ENV production
echo "false"                  | vercel env add APP_DEBUG production
echo "https://v2.sih.com.np"  | vercel env add APP_URL production
echo "cookie"                 | vercel env add SESSION_DRIVER production
echo "array"                  | vercel env add CACHE_STORE production
echo "sync"                   | vercel env add QUEUE_CONNECTION production
echo "stderr"                 | vercel env add LOG_CHANNEL production
echo "sqlite"                 | vercel env add DB_CONNECTION production
```

> 💡 **CLI gotcha:** do **not** append `</dev/null` to these `echo | vercel env add` pipelines —
> the null redirect overrides the pipe and the values are silently dropped (verified in this
> deployment — the env vars had to be re-added without it).

---

## 5. Deploying (step by step)

### Option A — Deploy without git (Vercel CLI) — **what was used here**

```bash
# 1. Install the CLI (or use `npx vercel` everywhere instead)
npm i -g vercel

# 2. Log in (device flow: open the printed URL, authorize)
vercel login

# 3. From the project folder, link to a (new) project
vercel link --yes

# 4. Set production env vars (see §4)

# 5. Make sure compiled assets exist (no remote build step!)
npm run build

# 6. Deploy to production
vercel --prod --yes
```

### Fix the framework auto-detection (only needed once, per project)

If the first deploy fails with a Vite-related error (e.g. `Could not resolve ...` while running
`npm run build`, or `No Output Directory named 'dist' found`), the project was auto-detected as
**Vite**. `vercel.json` already declares `framework: null`, but Vercel's *project settings* may
still carry the detected defaults. Fix it via the API:

```bash
TOKEN=$(node -e "console.log(JSON.parse(require('fs').readFileSync(process.env.HOME+'/.local/share/com.vercel.cli/auth.json','utf8')).token)")
curl -s -X PATCH \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"framework":null,"outputDirectory":"public","buildCommand":"","devCommand":""}' \
  "https://api.vercel.com/v9/projects/livewire-app?teamId=<YOUR_TEAM_ID>"
```

(Find `teamId`/`projectId` in `.vercel/project.json`.) Dashboard alternative:
**Settings → Build & Development Settings → Framework Preset: Other → Output Directory: `public`.**

### Option B — Deploy with git (GitHub import)

1. `git init && git add -A && git commit -m "..." && git remote add origin <repo> && git push -u origin main`
2. Go to https://vercel.com/new, import the GitHub repo.
3. Set **Framework Preset: Other** and **Output Directory: public** if the import mis-detects Vite.
4. Add the env vars from §4 in the dashboard (Production).
5. Push to the branch → auto-deploys. The `vercel.json` `functions` config makes the PHP function work.

---

## 6. Custom domain (v2.sih.com.np)

`v1.sih.com.np` is already bound to the **laravel-vercel** API project, so this site uses
**`v2.sih.com.np`** (both are subdomains of `sih.com.np`, whose DNS is on **Cloudflare**).

### Vercel side (done — via the API)

```bash
curl -s -X POST \
  -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"name":"v2.sih.com.np"}' \
  "https://api.vercel.com/v10/projects/<PROJECT_ID>/domains?teamId=<TEAM_ID>"
```

Response showed `"verified": true` — the domain is attached to the project. Then:
`APP_URL=https://v2.sih.com.np` was added and the project redeployed; the deploy output aliased
`v2.sih.com.np` and Vercel began creating the SSL certificate asynchronously.

Dashboard alternative: **Project → Settings → Domains → Add**.

### Cloudflare side (one record — your step)

In Cloudflare, zone `sih.com.np` → **DNS → Records → Add record**:

| Field | Value |
|---|---|
| **Type** | `CNAME` |
| **Name** | `v2` |
| **Target** | `cname.vercel-dns.com` |
| **Proxy status** | ⚪ **DNS only** (grey cloud — NOT orange/proxied) |
| **TTL** | `Auto` |

**Why "DNS only":** if the record is proxied (orange cloud), Cloudflare hides the CNAME behind
its own IPs and Vercel's certificate validation can fail. Once the record propagates, Vercel
finishes the SSL cert automatically and `https://v2.sih.com.np` serves the site.

> Apex note: `sih.com.np` itself would need an **A record → `76.76.21.21`** instead of a CNAME.

---

## 7. Known limitations on Vercel

| Limitation | Detail | Real fix |
|---|---|---|
| **SQLite is read-only** | The uploaded `database/database.sqlite` can be **read** (pages render with real content) but **cannot be written**. **Admin CRUD saves, appointments, contact submissions, and job applications will 500** with `attempt to write a readonly database`. | Connect a real database (Neon/Supabase Postgres): set `DB_CONNECTION=pgsql` + `DB_URL`, run `php artisan migrate --force`, then session/cache can also move to DB. |
| **Cookie sessions ≈ 4KB cap** | Livewire admin pages with image uploads (gallery, doctors, blogs) store temporary upload metadata in the session; cookie payloads over ~4KB silently fail. | Use DB-backed sessions once Postgres is connected. |
| **No remote build step** | Frontend changes require `npm run build` locally before deploying. | (By design — `framework: null`.) |

---

## 8. Troubleshooting

| Symptom | Root cause | Fix |
|---|---|---|
| `Cannot find module '/var/task/launcher.launcher' imported from /opt/rust/nodejs.js` on **every** request (500), even though the build succeeded | `vercel-php@0.9.0` fresh builds produce a broken function bundle on current Vercel infra | Pin `vercel-php@0.7.4` in `vercel.json` and redeploy (forces a fresh, working lambda) |
| Build runs `npm run build` / `vite build` and fails | Vercel auto-detected the Vite framework | `framework: null` in `vercel.json` + PATCH project settings (`framework:null`, `buildCommand:""`, `outputDirectory:public`) |
| `No Output Directory named 'dist' found` | Same framework detection, `outputDirectory: dist` default | Set `outputDirectory: public` in `vercel.json` and/or project settings |
| All `/` routes 404 (Laravel treats `/api` as base URL) | `SCRIPT_NAME` reported as `/api/index.php` | `api/index.php` resets `SCRIPT_NAME`/`PHP_SELF` to `/index.php` |
| Homepage renders but CSS is missing / unstyled in Chrome | (a) `public/hot` shipped → `@vite` renders `localhost:5173` links; (b) assets served as `http://` → mixed-content block | (a) `.vercelignore` excludes `/public/hot`; (b) `trustProxies(at: '*')` in `bootstrap/app.php` |
| Admin/contact/appointment save → 500 `attempt to write a readonly database` | Read-only SQLite on Vercel | Connect Postgres (`DB_CONNECTION=pgsql` + `DB_URL`) — see §7 |
| Where do I see errors? | — | Vercel dashboard → project → **Logs**, or `vercel logs <deployment-url>` (logs go to stderr via `LOG_CHANNEL=stderr`) |
| `vercel env add` values seem dropped | `</dev/null` on the pipeline overrode the `echo` pipe | Run `echo "value" | vercel env add KEY production` without the null redirect |

---

## 9. Replicating on another project

1. **Copy the four pieces:** `api/index.php`, `vercel.json`, `.vercelignore`, and the
   `trustProxies(at: '*')` line in `bootstrap/app.php`.
2. **Pin the runtime:** keep `"runtime": "vercel-php@0.7.4"` (PHP 8.3) — do **not** bump to 0.9.0
   without testing a fresh build.
3. **Ship your data:** if you want content without a DB, keep `database/database.sqlite`
   un-ignored (read-only on Vercel). For a write-capable site, use Postgres instead.
4. **Build assets locally:** run `npm run build` so `public/build/` uploads.
5. **Env vars:** see §4 (always `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_DRIVER=cookie`,
   `CACHE_STORE=array`, `QUEUE_CONNECTION=sync`, `LOG_CHANNEL=stderr`).
6. **Deploy:** `vercel login && vercel link --yes && vercel --prod --yes`.
7. **Custom domain:** attach via dashboard/API, set `APP_URL`, add the matching Cloudflare CNAME
   (DNS-only), and let Vercel issue the SSL cert.

---

*Generated documentation for the `refactors` branch deployment. The site was verified live on
2026-07-31: all public routes + `/up` + `/admin/login` return 200, assets served over `https://`.*
