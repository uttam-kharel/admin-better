# Guide — Database, Analytics & File Storage

Practical, step-by-step guide for the **Shubham International Hospital** site
(Laravel 13 + Livewire 4, deployed on Vercel). It covers how the database is
connected, how traffic analytics collects real data, and how to store doctor
**images and PDFs** today and in the future.

> Live site: `https://sih-hospital.vercel.app` · Admin: `https://sih-hospital.vercel.app/admin`
> Admin login: `admin@lumina.health` / `admin123` (super-admin) — change it after first login.

---

## 1. Where everything runs

| Piece | Where it lives |
|---|---|
| App (PHP/Laravel) | Vercel function (`vercel-php` runtime), project `sih-hospital` |
| Database | **Neon Postgres** (`neon-indigo-envelope`) — free tier, connected to the project |
| Images / PDFs | Vercel Blob CDN once `BLOB_READ_WRITE_TOKEN` is set (see §5) |
| Sessions | Encrypted cookies (set in `api/index.php`) |
| Cache | In-memory per request (safe on Vercel's read-only filesystem) |

The app is **switch-ready**: it auto-uses Postgres the moment `DATABASE_URL` is
present, and falls back to the committed SQLite file otherwise. That is why
everything "worked" before the database existed but writes (visits, admin CRUD,
forms) silently failed — SQLite is **read-only** on Vercel.

---

## 2. How the database is connected

### 2.1 Connection string & env vars

The connection is driven entirely by environment variables on the Vercel
project (Vercel → Project → Settings → Environment Variables):

| Variable | Purpose | Set by |
|---|---|---|
| `DATABASE_URL` | Pooled Postgres connection (PgBouncer) — used by the app at runtime | Neon integration (auto) |
| `DATABASE_URL_UNPOOLED` | Direct connection — used for **migrations** | Neon integration (auto) |
| `DB_CONNECTION` | Optional; app auto-detects `pgsql` when `DATABASE_URL` is set | — |
| `BLOB_READ_WRITE_TOKEN` | Enables image/PDF uploads to the Blob CDN | Vercel Blob integration (auto) |
| `APP_KEY` | Encrypts cookies/sessions — never change it once live | you |

`config/database.php` decides the driver:

```php
'default' => env('DB_CONNECTION', env('DATABASE_URL') ? 'pgsql' : 'sqlite'),
```

So: set `DATABASE_URL` → app switches to Postgres. No code change needed.

### 2.2 Create & connect the database (first time, ~3 minutes)

1. Vercel dashboard → **Storage → Create Database → Neon Postgres** (free plan).
2. Give it a name (e.g. `sih-hospital-db`) → **Create**.
3. On the database page → **Connect Project** → select `sih-hospital` →
   check **Production** → **Connect**.
4. Vercel automatically adds `DATABASE_URL`, `DATABASE_URL_UNPOOLED`,
   `PGHOST`, `PGUSER`, … to the project's environment.
5. **Deploy once** (env changes only apply to new deployments), then run the
   schema + data steps below **before** the new deployment serves traffic —
   otherwise the app reads from an empty Postgres.

> ⚠️ The Neon integration requires accepting marketplace terms once in the
> browser. From the CLI: `vercel integration add neon` (installs + provisions a
> database non-interactively after the terms are accepted).

### 2.3 Run the migrations (use the UNPOOLED connection!)

**Important gotcha:** the pooled (`DATABASE_URL`, PgBouncer) connection fails
on DDL inside transactions with `current transaction is aborted`. Always run
migrations/imports against **`DATABASE_URL_UNPOOLED`**:

```bash
# from a machine with PHP + composer installed (vendor/ present)
DB_CONNECTION=pgsql DATABASE_URL="postgresql://...UNPOOLED..." php artisan migrate --force
```

All 35 tables will be created (`users`, `doctors`, `departments`, `services`,
`page_visits`, `admin_users`, …). The `page_visits` table is what analytics
writes into.

### 2.4 Copy existing SQLite data into Postgres (one-time)

The repo ships a `database/database.sqlite` with the site's content. To move it
into Postgres (parent tables first, booleans converted, FK retry pass):

```bash
DB_CONNECTION=pgsql DATABASE_URL="postgresql://...UNPOOLED..." php artisan db:import-sqlite-to-pgsql
```

**Known caveats** (fixed manually when they appear):

- Tables with **string IDs** (`appointments`, `contact_submissions` — their
  migrations use `$table->string('id')->primary()`) are skipped by the import's
  "drop the id" logic. Import them manually including the id column:

  ```php
  // php artisan tinker --execute="..."
  $sqlite = DB::connection('sqlite');
  $pg     = DB::connection('pgsql');
  foreach (['appointments', 'contact_submissions'] as $t) {
      foreach ($sqlite->table($t)->get() as $row) { $pg->table($t)->insert((array) $row); }
  }
  ```

- Per-row inserts are slow over the network; for large tables (e.g. 495 rows of
  `page_visits`) bulk-insert in chunks of 100 instead of relying on the command.

Verify parity afterwards by comparing row counts per table.

---

## 3. Analytics — real visitor data

### 3.1 How tracking works

Every public `GET` page view is recorded by `App\Http\Middleware\TrackPageVisits`
(registered in `bootstrap/app.php` on the `web` group) into the **`page_visits`**
table. For each view it stores: path, full URL, referrer, a hashed visitor id,
device, browser, unique-vs-returning flag.

- **Not counted:** admin paths (`/admin/...`), Livewire internal requests,
  `/favicon.ico`, `/robots.txt`, `/sitemap.xml`, and any non-200 response.
- The middleware **never breaks the site**: any recording error is swallowed.

### 3.2 Where to see it

- **`/admin/analytics`** — KPIs (visits, unique visitors, today, avg/day),
  visits-per-day line chart, top pages, top referrers, by-hour, devices,
  browsers, and the latest 50 visits table.
- **`/admin` dashboard** — summary tiles + a 14-day traffic chart.

### 3.3 Real data

Analytics starts from **real data**: the old test/dev visits were removed, so
the numbers you see are actual site traffic from deployment onward. The data
persists in Postgres and **survives every redeploy** (unlike the old SQLite
bundle, which was wiped/read-only on Vercel).

---

## 4. Deploying changes

```bash
npx vercel --prod --yes --scope uttam-kharel     # deploy current folder to production
```

- Env-var changes require a redeploy to take effect.
- The frontend assets are committed in `public/build` — the project's build
  command is a no-op (`true`) because the redundant `npm run build` on Vercel
  fails without `vendor/`. Never re-enable a real build step unless you also
  run `composer install` first.
- New projects on Vercel default to SSO "deployment protection"
  (`all_except_custom_domains`); reset it to the standard preview-only setting
  for a public site.

---

## 5. Storing doctor images & PDFs (now and in the future)

### 5.1 How uploads work today

All uploads go through **`app/Services/BlobStorage.php`**:

- If `BLOB_READ_WRITE_TOKEN` is set → file is uploaded to **Vercel Blob**
  (`PUT https://blob.vercel-storage.com/{prefix}/{date}/{name}`) and the public
  CDN URL (`https://*.public.blob.vercel-storage.com/...`) is saved in the DB.
- If the token is missing → the file is embedded as a **base64 data URI** in
  the DB row (works everywhere, but bloats the DB and has no real CDN).

**To activate real image hosting (one-time):**
1. Vercel → **Storage → Create Database → Vercel Blob** (free tier).
2. **Connect** it to the `sih-hospital` project → `BLOB_READ_WRITE_TOKEN` is
   auto-added to the project env.
3. Deploy. New uploads now return CDN URLs.

### 5.2 Doctor photos (already works)

Admin → **Doctors** → the **Photo** field (`type: 'image'` in the resource
manager) → upload → stored via `BlobStorage::store($file, 'images')`. Existing
seed photos are external Unsplash URLs; any new upload becomes a Blob CDN URL.

### 5.3 Adding doctor PDFs (CV, qualifications, license) — recipe

The admin resource manager is config-driven (`resourceConfig()` in
`resources/views/admin/resource-manager/index.blade.php`). To give doctors a
PDF upload field:

**a) Add a column** (migration):

```php
// database/migrations/xxxx_add_cv_to_doctors_table.php
Schema::table('doctors', function (Blueprint $table) {
    $table->string('cv_url')->nullable();   // or certificate_url, license_url …
});
```

**b) Add the field** in the `doctors` config (`resourceConfig()`):

```php
$this->field('cv_url', 'CV / Qualification (PDF)', 'file', helper: 'PDF, DOC or DOCX'),
```

**c) Handle the `file` type on save** — in `coercedFormData()`, next to the
existing `image` branch:

```php
if ($field['type'] === 'file' && $value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
    $data[$name] = \App\Services\BlobStorage::store($value, 'pdfs');
    continue;
}
```

**d) Render a file input** in the form (near the existing `image` input around
line 905): add a branch that renders

```html
<input type="file" wire:model="form.cv_url" accept=".pdf,.doc,.docx" />
```

**e) Display it** on the doctor detail page (`resources/views/pages/doctors/show.blade.php`):

```blade
@if($doctor->cv_url)
    <a href="{{ $doctor->cv_url }}" target="_blank" rel="noreferrer">Download CV / qualifications</a>
@endif
```

**f) Deploy.** Uploaded PDFs then live on the Blob CDN and the public site can
link/download them. (The existing job-application flow already stores CVs this
way — `CareersShow` uses `BlobStorage::store($this->resume, 'cvs')`.)

> Free-tier limits: Vercel Blob ≈1 GB storage/month, Neon ≈0.5 GB. If a limit
> is hit the service pauses until the monthly reset — it never charges.

---

## 6. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| `current transaction is aborted` on migrate | Pooled (PgBouncer) connection + DDL-in-transaction | Use `DATABASE_URL_UNPOOLED` for migrations/imports |
| Admin 500 with `column "unknown" does not exist` | SQLite string literals `"..."` treated as PG identifiers | Use single quotes in `selectRaw` |
| Analytics 500, `strftime` unknown | `strftime` is SQLite-only | Use `extract(hour from created_at)` on Postgres (driver-aware switch is in the analytics page) |
| Visits not recording | App still on read-only SQLite (no `DATABASE_URL` yet) | Connect Neon, redeploy |
| Env var changes ignored | Env applies only at deploy time | Redeploy |
| Uploads stored as base64 | `BLOB_READ_WRITE_TOKEN` missing | Create + connect Vercel Blob, redeploy |
| Pages render but admin CRUD/forms fail | Postgres schema missing while app already points at it | Run migrations before the first deploy that has `DATABASE_URL` |

---

## 7. CI/CD — how a push reaches production

### 7.1 The pipeline

The repo ships a GitHub Actions workflow (`.github/workflows/deploy-vercel.yml`)
that deploys to Vercel automatically:

- **push to `main`** → production deploy
- **pull request** → preview deploy with a unique `.vercel.app` URL
- **manual** → `Actions → Deploy to Vercel → Run workflow`

On a push to `main` the runner does, in order:

1. `composer install` + `npm ci`
2. `npm run build` (regenerates `public/build` frontend assets)
3. **if the `DATABASE_URL` secret exists:** `cp .env.example .env` then
   `php artisan migrate --force` — the schema is applied to Neon **before**
   the deploy, so the new code never runs against a missing column/table
4. `npx vercel --prod --token=$VERCEL_TOKEN` — deploys to the project
   identified by the `VERCEL_ORG_ID` + `VERCEL_PROJECT_ID` secrets

### 7.2 GitHub secrets vs Vercel env vars

Two different stores, two different purposes — don't mix them up:

| Store | Variables | Used by |
|---|---|---|
| **GitHub repo secrets** (Settings → Secrets → Actions) | `VERCEL_TOKEN`, `VERCEL_ORG_ID`, `VERCEL_PROJECT_ID`, optional `DATABASE_URL`, optional `APP_KEY` | The CI workflow only — the app never reads these |
| **Vercel project env** (Project → Settings → Environment Variables) | `DATABASE_URL`, `DATABASE_URL_UNPOOLED`, `BLOB_READ_WRITE_TOKEN`, `KV_URL`, `APP_KEY`, … | The app at runtime — this is what the running site actually uses |

GitHub secrets tell CI *which* Vercel project to deploy and let it migrate the
DB; Vercel env vars tell the *deployed app* how to behave.

### 7.3 Which site does CI deploy?

This repo's CI is wired to the **client's Vercel account** (`livewire-app` →
`v1.sih.com.np`), because the `VERCEL_ORG_ID`/`VERCEL_PROJECT_ID` secrets point
there. Pushing to GitHub therefore updates the client's site — it does **not**
touch `sih-hospital.vercel.app`, which is deployed directly from this machine:

```bash
npx vercel --prod --yes --scope uttam-kharel
```

To have CI deploy to `sih-hospital` instead (or as well):

1. Get its IDs: Vercel → sih-hospital → Settings → General → `org_id` / `project_id`.
2. Add a `VERCEL_TOKEN` for the account that owns it, `VERCEL_ORG_ID`, `VERCEL_PROJECT_ID`, and `DATABASE_URL` as GitHub secrets.
3. Replicate the project settings on sih-hospital that the client project has
   (build command `true`, standard deployment protection) — they don't transfer
   automatically because it's a different project.

### 7.4 Checklist — end-to-end "push and it just works"

- [ ] `DATABASE_URL` + `DATABASE_URL_UNPOOLED` set on the Vercel project (Neon integration does this)
- [ ] `BLOB_READ_WRITE_TOKEN` set on the Vercel project (Blob integration does this)
- [ ] GitHub secrets: `VERCEL_TOKEN`, `VERCEL_ORG_ID`, `VERCEL_PROJECT_ID` for the project you want CI to deploy
- [ ] GitHub secret `DATABASE_URL` set → CI applies migrations on every push
- [ ] `APP_KEY` stable and identical in Vercel env + (if migrations use encryption) as a GitHub secret
- [ ] Commit + push → watch Actions → production deploy green

> Everything in this section is also documented, in compact form, at the bottom
> of `.env.example`.
