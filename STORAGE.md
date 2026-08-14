# STORAGE.md — Where Images, CVs, and Uploads Live

A plain-English map of how this app stores files — specifically **doctor photos**,
**blog/department images**, and **job-application CVs** — plus how to move
uploads onto a real CDN (Vercel Blob).

---

## 1. The short answer

**Nothing is stored as a "file on disk."** Every image is stored as a **string**
in a database column — either:

- a **URL** pointing at someone else's server, or
- a **base64 data URI** with the image bytes embedded directly in the DB row.

The browser loads the image from wherever that string points. There is no
`storage/app/public` in production (see §5 — Vercel's filesystem is read-only).

---

## 2. The three places images actually live

| Where the bytes live | Stored in the DB as | Example value |
|---|---|---|
| **Unsplash CDN** (hotlink) | `https://images.unsplash.com/photo-…` | seeded demo content |
| **Inside the Postgres DB** | `data:image/jpeg;base64,/9j/4AAQ…` | admin upload with no Blob token |
| **Vercel Blob CDN** | `https://blob.vercel-storage.com/uploads/…` | admin upload with a Blob token (goal state) |

### Current production snapshot (doctors)

```text
doctors.photo:
  14 × https://images.unsplash.com/...   ← hotlinked from Unsplash's CDN
   1 × data:image/jpeg;base64,...        ← embedded in the DB (an early upload)
   0 × blob.vercel-storage.com           ← none yet (no token configured)
```

Consequences of hotlinking: if Unsplash removes/renames the photo, the doctor's
image silently breaks — nothing on your server protects it. The site only *works*
because those URLs are still alive.

---

## 3. What happens when you upload a photo in the admin

```
You pick a file
   │
   ▼
Livewire stages it to a temp disk
   (Vercel: /tmp via LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK=livewire-tmp)
   │
   ▼
Save triggers App\Services\BlobStorage::store($file, 'uploads')
   │
   ├─ BLOB_READ_WRITE_TOKEN configured?   ──YES──► PUT to Vercel Blob CDN
   │                                           https://blob.vercel-storage.com/uploads/YYYY/MM/xxx-filename
   │                                           public URL saved into the DB column
   │
   └─ no token (current state)          ──NO───► base64-encode the bytes
                                                 save `data:<mime>;base64,...` INTO the DB row
```

### Where each model's image column lives

| Model | Column | Upload prefix |
|---|---|---|
| `Doctor` | `photo` | `uploads` |
| `JobApplication` (CV/PDF) | `resume_url` | `cvs` |
| Other content (departments, services, blogs…) | image columns (e.g. `image`, `thumbnail`) | `uploads` |

---

## 4. The DB-bloat warning (why data URIs are a fallback, not a goal)

A base64 data URI is the image bytes embedded in the row:

- a **2 MB JPEG** becomes **~2.7 MB of base64 text** in Postgres;
- every page that lists those images pulls that text through the DB → slower
  queries, bigger responses, larger backups;
- it "works everywhere" (local, preview, prod) with **zero setup** — that's why
  it's the fallback — but it is not where uploads should end up long-term.

**Rule of thumb:** data URIs are for convenience; real files belong on Blob.

---

## 5. Why not Laravel's normal `storage/app/public`?

Vercel serverless functions run on a **read-only filesystem** — only `/tmp` is
writable, and `/tmp` is wiped between cold starts. So:

- `storage/app/public` **cannot persist** on Vercel (uploads would 500);
- `public/storage` (the symlink) is excluded via `.vercelignore`;
- `api/index.php` redirects every file-based cache and the Livewire temp upload
  disk to `/tmp` for exactly this reason.

`BlobStorage` exists to solve this: it routes uploads to a real CDN (Blob) when
a token exists, or into the DB otherwise.

---

## 6. Enabling Vercel Blob (the one real upgrade)

1. **Create a Blob store** — Vercel dashboard → Storage → Create Blob Store
   (free tier is fine at this scale).
2. **Copy the token** (`vercel_blob_rw_…`) → Project → Settings →
   Environment Variables → add `BLOB_READ_WRITE_TOKEN` for **Production** (and
   Preview if you want uploads in previews too).
3. That's it — `BlobStorage` reads `config('services.blob.token')` which maps
   to `BLOB_READ_WRITE_TOKEN`; no code change needed. Every new upload then
   lands on the CDN with a clean `https://blob.vercel-storage.com/…` URL.

> ⚠️ The token is a secret — put it in Vercel env vars (and optionally GitHub
> Actions secrets for CI), never in a committed file. See `.env.new.database`
> for the placeholder convention.

### Optional: migrate existing photos onto Blob

The 14 Unsplash hotlinks + the 1 data-URI doctor can be re-uploaded to Blob
with a small artisan command (download the current image bytes, call
`BlobStorage::store`, update the row). This removes the Unsplash dependency and
shrinks the DB. Not done yet — open a task if you want it.

---

## 7. Quick reference

```bash
# Check whether Blob is configured right now
php artisan tinker --execute="echo config('services.blob.token') ? 'blob ON' : 'blob OFF';"

# How many doctors are hotlinked vs embedded vs on Blob
php artisan tinker --execute="
echo 'unsplash: '.\App\Models\Doctor::where('photo','like','%unsplash%')->count().PHP_EOL;
echo 'data-uri: '.\App\Models\Doctor::where('photo','like','data:%')->count().PHP_EOL;
echo 'blob:     '.\App\Models\Doctor::where('photo','like','%blob.vercel-storage%')->count().PHP_EOL;"
```

*Related: `app/Services/BlobStorage.php` (the upload service), `api/index.php`
(temp-disk wiring), `resources/views/admin/resource-manager/index.blade.php`
(`displayUrl()` passthrough).*
