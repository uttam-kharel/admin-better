# Vercel Free Database + Image Storage (Neon Postgres & Vercel Blob)

This project is wired so that **the moment you add two environment variables on Vercel, your app gets a real persistent database and real image storage** — no other code changes needed.

| Need | Vercel free option | Free tier | Credit card? |
|---|---|---|---|
| Database | **Neon Postgres** (Vercel Marketplace; native "Vercel Postgres" was retired) | ~0.5 GB storage, multiple branches | No |
| Images / CVs | **Vercel Blob** | ~1 GB storage/month (avg), generous transfer | No |
| Cache/queue (optional) | **Upstash Redis** (Vercel Marketplace; "Vercel KV" retired) | ~10k commands/day | No |

> Vercel's Hobby plan is completely free. If you exceed a limit, the service **pauses** until the monthly cycle resets — it never charges you.

---

## How the app uses them

- **`config/database.php`**: if `DATABASE_URL` is set, the default connection is automatically `pgsql`; otherwise it stays on the committed `database/database.sqlite` (current behaviour). No code change required to switch.
- **`app/Services/BlobStorage.php`**: every image/CV upload goes through it. If `BLOB_READ_WRITE_TOKEN` is set, the file is uploaded to Vercel Blob and the public CDN URL is saved in the DB. Without a token it falls back to embedding a base64 data URI (what the site does today), so uploads keep working everywhere.

---

## Step 1 — Create the Neon Postgres database (free)

1. Go to **vercel.com → your project (livewire-app) → Storage**.
2. Click **Create Database** → choose **Neon Postgres** (it's Vercel's recommended Postgres on the free tier).
   - Pick a region close to you. Keep the free plan selected.
   - It will create the database and ask to link it to your project. Accept.
3. Vercel automatically adds environment variables to your project — including **`DATABASE_URL`** (the `postgresql://...` connection string).
   - Also confirm these are set on **Production**: Project → **Settings → Environment Variables → Production**.

> Because the app already reads `DATABASE_URL` (config/database.php), no redeploy with extra settings is needed — the next deploy picks it up.

### Migrate the schema to Postgres
The tables need to be created on Neon. Two options:

- **Option A — CI step (recommended, repeatable):** add a step in `.github/workflows/deploy-vercel.yml` before `vercel --prod`:
  ```yaml
  - name: Run migrations on Neon
    run: php artisan migrate --force
    env:
      DATABASE_URL: ${{ secrets.DATABASE_URL }}
      DB_CONNECTION: pgsql
  ```
  and store `DATABASE_URL` as a **GitHub repository secret** (Settings → Secrets → Actions).

- **Option B — one-off from your machine:**
  ```bash
  DATABASE_URL="postgresql://..." DB_CONNECTION=pgsql php artisan migrate --force
  ```

> The existing demo data lives in the committed SQLite file; it does not carry over to Neon automatically. After migrating, you can re-seed content if you want the demo data back (the menu seeder etc.). Analytics visits are **real** from this point on and will **survive every redeploy**.

---

## Step 2 — Create Vercel Blob storage (free, for images/CVs)

1. Go to **vercel.com → your project → Storage**.
2. Click **Create Database** → choose **Vercel Blob**.
3. Copy the **`BLOB_READ_WRITE_TOKEN`** (`vercel_blob_rw_...`).
4. Add it as an environment variable on your Vercel project:
   - **Project → Settings → Environment Variables**
   - Name: `BLOB_READ_WRITE_TOKEN`
   - Value: the token
   - Scope: **Production** (and Preview if you want it there too)
5. Redeploy (or just push to `main` — CI/CD redeploys for you).

From the next deploy onward, every image uploaded in the admin (doctor photos, department images, etc.) and every CV uploaded via the Careers form is stored on Vercel Blob and served from its CDN — **no more 404ing /storage URLs, no more base64 blobs in the DB**.

> Optionally also add `BLOB_READ_WRITE_TOKEN` as a GitHub secret if you add the CI migration step above.

---

## Step 3 — Verify

- After pushing `main` with the env vars set, check the deploy is green (Actions → latest run).
- **Database:** log into `/admin` → open **Analytics** → visit a page of the site a few times → the visits counter should grow and **survive the next deploy** (previously it reset because the DB was committed to git).
- **Images:** in the admin, upload a photo to any resource (e.g. Doctors). The saved value should now be a `https://...public.blob.vercel-storage.com/...` URL instead of a `data:` URI, and it should render after every redeploy.

---

## FAQ / gotchas

- **Do I need to keep the SQLite file?** You can leave it; it just won't be used while `DATABASE_URL` is set.
- **Does anything break locally?** No. Local `.env` has no `DATABASE_URL`, so the app stays on SQLite. The Blob service silently falls back to base64 when no token is present.
- **Session/queue drivers:** the app uses `SESSION_DRIVER=database` / `CACHE_STORE=database` — these tables exist in the migrations, so they work on Postgres too.
- **Blob file size:** free plan supports large files; the admin already limits uploads in the Livewire rules.
