# Deploy to InfinityFree

This guide walks through deploying this Laravel 12 Livewire application to InfinityFree using GitHub Actions and FTP.

## ✅ What's Already Done

These deployment files are ready at the **project root** level (not inside `livewire-app/`):

- **`.htaccess`** — Routes all requests to `livewire-app/public/` for Laravel routing
- **`.github/workflows/infinity-free.yml`** — GitHub Actions FTP deploy workflow
- **`livewire-app/.gitignore`** — Already commented out (so `vendor/`, `.env`, `node_modules` are uploaded)
- **`livewire-app/.env.example.production`** — Production environment template
- **`livewire-app/DEPLOYMENT.md`** — This guide

---

## Prerequisites

- An InfinityFree account
- FTP credentials from InfinityFree
- A GitHub repository

---

## Step-by-Step

### 1. Configure .env for Production

```bash
cp livewire-app/.env.example.production livewire-app/.env
```

Edit `livewire-app/.env` with your InfinityFree MySQL database credentials:

```
APP_ENV=production
APP_DEBUG=false
APP_NAME=Shubham International Hospital
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=sqlXXX.epizy.com
DB_PORT=3306
DB_DATABASE=epiz_XXXXXXXX_dbname
DB_USERNAME=epiz_XXXXXXXX
DB_PASSWORD=your_db_password

SESSION_DRIVER=file
```

### 2. Export & Import Database

1. Export your local database via phpMyAdmin or `mysqldump livewire-app/database/database.sqlite > export.sql`
2. Log in to InfinityFree Control Panel → **MySQL Databases**
3. Create a database and note credentials (host, name, username, password)
4. Go to InfinityFree's phpMyAdmin → select your database
5. Import the `.sql` file via the **Import** tab

### 3. Add Your Domain on InfinityFree

- **Main domain:** Server directory is `/htdocs/`
- **Addon domain:** Server directory is `/yourdomain.com/htdocs/`

### 4. Add GitHub Secrets

In your GitHub repository:
1. Go to **Settings → Secrets and variables → Actions**
2. Add two **repository secrets**:
   - `FTP_USERNAME` — Your InfinityFree FTP username (e.g. `epiz_12345678`)
   - `FTP_PASSWORD` — Your InfinityFree FTP password

### 5. Update server-dir in Workflow

Edit `.github/workflows/infinity-free.yml` and set the correct `server-dir`:

- **Main domain:** `/htdocs/` (default — no change needed)
- **Addon domain:** `/yourdomain.com/htdocs/`

### 6. Create GitHub Repository & Push

```bash
# Create a new repo on GitHub first, then:
git init
git add .
git commit -m "Ready for production deploy"
git branch -M main
git remote add origin https://github.com/your-username/your-repo.git
git push -u origin main
```

### 7. Trigger Deployment

The GitHub Action triggers **automatically** on push to `main`. You can also trigger manually:
1. Go to **Actions** tab in your GitHub repository
2. Select **Deploy to InfinityFree** workflow
3. Click **Run workflow**

---

## ⚠️ Important Notes

| Item | Detail |
|------|--------|
| **No Composer** | InfinityFree has no shell access. `vendor/` is uploaded directly (`.gitignore` is already commented out) |
| **File size limit** | ~1MB per file — split large assets if needed |
| **Deployment time** | 30 min to 5 hours depending on file count and server load |
| **No artisan commands** | Run all migrations locally before exporting the database |
| **APP_KEY** | Keep the same `APP_KEY` from local `.env` in production — changing it will invalidate all encrypted data |
| **Branch** | The workflow triggers on pushes to `main`. Use `git branch -M main` |

## ⚡ Local Optimization (Already Done)

```bash
php artisan optimize          # Cache config, routes, views, events
php artisan view:cache        # Compile all Blade templates
php artisan route:cache       # Cache route registration
php artisan config:cache      # Cache configuration
php artisan icons:cache       # Cache blade icons
npm run build                 # Build frontend assets for production
```

## 📁 Project Structure for Deployment

```
your-repo-root/
├── .github/workflows/infinity-free.yml   ← GitHub Action workflow
├── .htaccess                              ← Routes to livewire-app/public/
├── livewire-app/                          ← The Laravel application
│   ├── .env.example.production            ← Production env template
│   ├── .env                               ← Your production env (create this)
│   ├── public/
│   │   ├── .htaccess                      ← Laravel's built-in routing (already exists)
│   │   ├── index.php                      ← Laravel entry point
│   │   └── build/                         ← Compiled frontend assets
│   ├── vendor/                            ← PHP dependencies (uploaded)
│   ├── node_modules/                      ← Node dependencies (uploaded)
│   └── ...rest of Laravel files
```
