# Database Setup — Postgres for the Livewire site on Vercel

A step-by-step guide to connect a **managed Postgres database** to the Shubham International Hospital
site (Laravel 13 + Livewire 4) deployed on Vercel — including migrations, environment variables, and
live verification. Use this to replicate the setup on any project.

> Current project reference: database for `https://v1.sih.com.np` / `https://livewire-app-gamma.vercel.app`
> (project `livewire-app`).
> After this guide you'll have: a Postgres database, `DB_CONNECTION` + `DB_URL` env vars on Vercel,
> the schema migrated, and **writable** admin CRUD / appointments / contact forms over HTTPS.

---

## Table of contents

1. [Why you need a hosted database](#1-why-you-need-a-hosted-database)
2. [What changes once Postgres is connected](#2-what-changes-once-postgres-is-connected)
3. [Create a free Postgres database](#3-create-a-free-postgres-database)
4. [Connection string format](#4-connection-string-format)
5. [Add the env vars to Vercel](#5-add-the-env-vars-to-vercel)
6. [Run the migrations](#6-run-the-migrations)
7. [Redeploy and verify](#7-redeploy-and-verify)
8. [Troubleshooting](#8-troubleshooting)
9. [Automating migrations (CI, optional)](#9-automating-migrations-ci-optional)

---

## 1. Why you need a hosted database

Vercel Functions run on a **read-only filesystem** — only `/tmp` is writable. The bundled SQLite file
(`database/database.sqlite`) is uploaded with the deploy so the site **reads** real content, but it
**cannot be written to**. Any write fails with:

```
SQLSTATE[HY000] … attempt to write a readonly database
```

This currently affects **every** write in the site:

- Admin panel CRUD (doctors, services, departments, blogs, gallery, testimonials, FAQs, health
  packages, job openings, menus, site settings, admin users…)
- **Appointments** (`appointments` table)
- **Contact submissions** (`contact_submissions` table)
- **Job applications** (`job_applications` table)
- Patient stories, quick actions, authors, hero slides…

The fix is a **managed database outside Vercel** (over the internet). Postgres is the natural
choice: the `vercel-php` runtime ships with the `pdo_pgsql` extension, and Laravel's `pgsql` driver
connects straight to a `DATABASE_URL`-style connection string.

## 2. What changes once Postgres is connected

Once `DB_CONNECTION=pgsql` + `DB_URL` are set and migrations are run:

| Aspect | Before (SQLite) | After (Postgres) |
|---|---|---|
| Reads | ✅ work | ✅ work |
| Admin CRUD / appointments / contact / jobs | ❌ 500 `readonly database` | ✅ **fully writable** |
| Sessions | cookie (~4KB cap; Livewire uploads risk it) | can switch to `database` sessions |
| Cache | `array` (per-request) | can switch to `database` cache |
| Data persistence across deploys | ❌ (content ships inside the bundle) | ✅ (lives in the DB) |

After migrating, you may also switch `SESSION_DRIVER=database` and `CACHE_STORE=database` to remove
the cookie-session 4KB limit and get persistent cache — both just need the `sessions` and `cache`
tables, which this project's migrations already create.

## 3. Create a free Postgres database

### Option A — Neon (recommended, generous free tier)

1. Sign up at [neon.tech](https://neon.tech) (GitHub login is fine).
2. **Create a project** → give it a name → pick a region close to your Vercel function region
   (Vercel runs in `iad1` — US East; pick the nearest Neon region, e.g. `aws-us-east-2`).
3. In the dashboard, open **Connection Details** and copy the **connection string**, which looks
   like:

   ```
   postgresql://neondb_owner:AbCdEf123...@ep-some-name.us-east-2.aws.neon.tech/neondb?sslmode=require
   ```

   > Use the **direct** (non-pooled) connection for CLI migrations; the pooled one (`-pooler`) is
   > for high-concurrency serverless traffic and also works.

### Option B — Supabase

1. Sign up at [supabase.com](https://supabase.com) → **New project** → pick a region → create.
2. Go to **Project Settings → Database → Connection string → URI** and copy it (it contains your DB
   password, so keep it secret).

### Option C — Vercel Postgres

Available on paid plans. In the Vercel dashboard: **Storage → Create Database → Postgres**. Vercel
can auto-inject the `DATABASE_URL` env var into the project.

## 4. Connection string format

```
postgresql://USER:PASSWORD@HOST:5432/DATABASE?sslmode=require
```

- The `?sslmode=require` suffix is fine — Laravel's `pgsql` driver passes it through.
- **Special characters in the password must be URL-encoded** (e.g. `@` → `%40`, `:` → `%3A`,
  `/` → `%2F`). Otherwise the connection string breaks.

## 5. Add the env vars to Vercel

> ⚠️ **Two variables are required.** Laravel's default connection is `sqlite` — setting only
> `DB_URL` is **not** enough. You must also set `DB_CONNECTION=pgsql`, otherwise the app keeps trying
> SQLite and fails.

### Via CLI (from the project folder)

```bash
echo "pgsql" | vercel env add DB_CONNECTION production
echo "postgresql://USER:PASS@HOST:5432/DB?sslmode=require" | vercel env add DB_URL production
```

> ⚠️ **CLI gotcha (learned the hard way):** do **not** append `</dev/null` to the pipeline — the
> null redirect overrides the pipe and the value is silently dropped.

### Via dashboard

Project **livewire-app** → **Settings → Environment Variables** → add for the **Production**
environment:

| Name | Value |
|---|---|
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | `postgresql://USER:PASS@HOST:5432/DB?sslmode=require` |

> You can also split into individual vars (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`,
> `DB_PASSWORD`) instead of `DB_URL` — Laravel supports both. A single `DB_URL` is simplest.

## 6. Run the migrations

Vercel does **not** run migrations during the build (there is no build step — `framework: null`),
so run them yourself from your machine, pointed at the production database:

```bash
DB_CONNECTION=pgsql DB_URL="postgresql://USER:PASS@HOST:5432/DB?sslmode=require" php artisan migrate --force
```

- `--force` is required because the app runs in production mode (non-interactive).
- The migrations create all **35 tables** this site needs (users, admin_users, doctors, services,
  departments, blogs, gallery_items, appointments, contact_submissions, job_applications, cache,
  jobs, sessions, …). No seeder is required for the site to function — but if you want to move your
  *existing* SQLite content into Postgres, see the **data migration** note below.

### Optional: move existing SQLite content into Postgres

The site currently renders content from the bundled `database/database.sqlite`. To keep that content
after switching to Postgres, export it to SQL and import into the new DB:

```bash
# Prerequisites: the sqlite3 and psql CLI tools must be installed locally.

# 1. Dump the SQLite data (rows only — recreate schema via migrations instead)
sqlite3 database/database.sqlite ".dump" > /tmp/dump.sql

# 2. Import into Postgres — pass the URL directly (an inline DB_URL=... prefix does
#    NOT make $DB_URL available to the shell, so `psql "$DB_URL"` would be empty).
psql "postgresql://USER:PASS@HOST:5432/DB?sslmode=require" < /tmp/dump.sql
```

> ⚠️ `.dump` includes `CREATE TABLE`/`PRAGMA` statements that don't translate 1:1 to Postgres
> (e.g. `AUTOINCREMENT`, backtick quoting). The clean path is: run `migrate --force` first, then
> insert **only the `INSERT INTO` rows** from the dump (e.g. `grep '^INSERT'`), which works because
> the schema from migrations matches the original migrations' column layout. Test on a throwaway DB
> first if the data is precious.

## 7. Redeploy and verify

Env changes need a new deployment to take effect:

```bash
vercel --prod --yes
```

Then verify writes work against the live site:

```bash
# Health + public pages still fine
curl -s https://v1.sih.com.np/up

# A write-bearing page loads (contact/appointment forms, admin login page)
curl -s -o /dev/null -w "%{http_code}\n" https://v1.sih.com.np/contact      # 200
curl -s -o /dev/null -w "%{http_code}\n" https://v1.sih.com.np/admin/login  # 200
```

> ⚠️ **Don't test writes with a plain `curl -X POST` to `/contact`.** The contact & appointment
> pages are **Livewire components**, not JSON APIs — they expect the `wire:snapshot` + CSRF token
> payload a browser submits, so a bare JSON `curl` won't create a row (it'll redirect or 419).
> Verify writes instead by (a) submitting the form in a real browser and watching Vercel
> **Function Logs** for any `readonly database` error, and (b) checking the row landed in Postgres
> from your machine:

```bash
DB_CONNECTION=pgsql DB_URL="postgresql://USER:PASS@HOST:5432/DB?sslmode=require" \
  php artisan tinker --execute="echo \App\Models\ContactSubmission::count();"
```

If you get a **500** with a database error, check the Vercel **Function Logs** (project → Deployments
→ Logs) — `LOG_CHANNEL=stderr` surfaces the SQL error there.

## 8. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| `SQLSTATE[08006] … connection refused` | Wrong host/port, or the DB region blocks your IP | Check host/port in the connection string; Neon/Supabase usually allow all IPs |
| `SQLSTATE[08004] … password authentication failed` | Wrong password or unencoded special chars | URL-encode special chars in the password (`@`→`%40`, `:`→`%3A`, `/`→`%2F`) |
| `fe_sendauth: no password supplied` | Password missing from the string | Ensure `user:pass@` is present |
| `could not translate host name` | Typo in the host | Re-copy the connection string from the provider |
| Still hitting `attempt to write a readonly database` | `DB_CONNECTION` not set (app is still on sqlite) | Set `DB_CONNECTION=pgsql` on Vercel + redeploy |
| `sqlite` driver error at runtime | `DB_CONNECTION` env var not applied | Verify env is set for **Production** and redeploy; check Function Logs |
| `ssl … not supported` / SSL errors | `sslmode` not set for providers that require it | Append `?sslmode=require` to `DB_URL` |
| Migrations fail with `relation already exists` | Re-running partial migrations | Use `php artisan migrate:status` to inspect; `migrate --force` is otherwise idempotent |
| Live site 500 only on write routes (reads still fine) | DB not reachable from the function | Check env vars are set for the **Production** environment and redeployed; check Function Logs |
| Admin login breaks after the switch | Sessions still cookie-based or `users`/`sessions` table missing | After migrate, optionally set `SESSION_DRIVER=database` + redeploy |
| `psql: could not connect` during import | Import host differs from app host (Neon pooled vs direct) | Use the same connection string the app uses |

## 9. Automating migrations (CI, optional)

So migrations run automatically on every push, add a GitHub Actions workflow
(`.github/workflows/deploy.yml`) that migrates before Vercel redeploys:

```yaml
name: Deploy
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-interaction --prefer-dist
      - run: php artisan migrate --force
        env:
          DB_CONNECTION: pgsql
          DB_URL: ${{ secrets.DB_URL }}
      - run: npx vercel --prod --yes --token ${{ secrets.VERCEL_TOKEN }}
```

(Add `DB_URL` and `VERCEL_TOKEN` as repository secrets.)
