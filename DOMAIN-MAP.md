# Domain Map — `v2.sih.com.np` → Vercel (livewire-app)

This document maps the custom domain **`v2.sih.com.np`** (DNS hosted on **Cloudflare**) to the
**Vercel** project `livewire-app` (Shubham International Hospital — Laravel 13 + Livewire 4), and
explains exactly how the mapping works so it can be replicated for any future subdomain.

> `v1.sih.com.np` is already bound to the **laravel-vercel** API project — that project has its own
> `DOMAIN-MAP.md`. This subdomain (`v2`) serves the hospital **website**.

---

## 1. Current mapping

| Hostname | Layer | Points to | Provider | Status |
|---|---|---|---|---|
| `v2.sih.com.np` | DNS | CNAME → `cname.vercel-dns.com` | Cloudflare | ⏳ **record to add (see §4)** |
| `v2.sih.com.np` | Vercel domain → project | production deployment of `livewire-app` | Vercel | ✅ attached, `verified: true` |
| `v2.sih.com.np` | Vercel alias | production deployment | Vercel | ✅ aliased during deploy |
| `livewire-app-gamma.vercel.app` | Vercel alias | production deployment | Vercel | ✅ active (default URL) |

> 🟡 **Status: attached, awaiting DNS** — as of 2026-07-31 the domain is attached to the Vercel
> project (`verified: true`), `APP_URL` is set to `https://v2.sih.com.np`, and the deployment was
> aliased to it. Vercel is creating the SSL certificate **asynchronously** — it completes only once
> the Cloudflare CNAME from §4 is publicly resolvable. Until then the site serves at
> **https://livewire-app-gamma.vercel.app** (verified live, all routes 200).

**Reference values:**

- Vercel project: `livewire-app` (team `the-sickness1`)
- `projectId`: `prj_uahWWDuQWtydeKHS0SLLUehayN78`
- `orgId` / `teamId`: `team_TKO4eklVKvpy5hlk9KtncXoD`
- Vercel CNAME target: `cname.vercel-dns.com` → resolves to `76.76.21.241`, `66.33.60.193`
- Vercel apex A-record target: `76.76.21.21`
- Production env var `APP_URL`: `https://v2.sih.com.np`
- Default deployment URL: `https://livewire-app-gamma.vercel.app`

---

## 2. How the mapping works

```
Browser ── https://v2.sih.com.np/
   │
   ▼  Cloudflare DNS (sih.com.np zone)
CNAME  v2 → cname.vercel-dns.com     (DNS only / grey cloud)
   │
   ▼  Vercel edge (matches Host header v2.sih.com.np → project)
Project: livewire-app  →  production deployment
   │
   ▼  vercel.json route "/(.*)" → /api/index.php
Laravel (Livewire)  →  HTML page
```

Two independent sides must both be configured:

1. **Vercel side** — the domain is *attached* to the project, and Vercel *aliases* the production
   deployment to it, then auto-issues an SSL certificate.
2. **Cloudflare side** — a DNS record must *point* the hostname at Vercel's edge so traffic
   actually arrives.

The SSL certificate is created **asynchronously** and completes once the DNS record is publicly
resolvable. Vercel shows `verified: true` for the domain as soon as it's attached to the project.

---

## 3. Vercel side — add a domain to the project (done ✅)

### Option A: Vercel dashboard
Project → **Settings → Domains** → **Add** → type `v2.sih.com.np` → Save.

### Option B: Vercel API (what was used here)

```bash
# Read the CLI auth token (location varies by CLI version; also try ~/.vercel/auth.json)
TOKEN=$(node -e "console.log(JSON.parse(require('fs').readFileSync(process.env.HOME+'/.local/share/com.vercel.cli/auth.json','utf8')).token)")

# Add the domain to the project (returned {"name":"v2.sih.com.np","verified":true,...})
curl -s -X POST \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"v2.sih.com.np"}' \
  "https://api.vercel.com/v10/projects/prj_uahWWDuQWtydeKHS0SLLUehayN78/domains?teamId=team_TKO4eklVKvpy5hlk9KtncXoD"

# List domains attached to the project
curl -s -H "Authorization: Bearer $TOKEN" \
  "https://api.vercel.com/v10/projects/prj_uahWWDuQWtydeKHS0SLLUehayN78/domains?teamId=team_TKO4eklVKvpy5hlk9KtncXoD"
```

### Option C: Vercel CLI

```bash
vercel domains add v2.sih.com.np     # registers the domain at team level
```

> ⚠️ This only **registers** the domain on your team — you must still **attach** it to the project
> (dashboard → Settings → Domains, or the API call above) for it to serve the deployment.

Then deploy so the production alias is applied — the deploy output confirms it:

```
▲ Aliased         https://v2.sih.com.np
✓ Ready in 25s
We are attempting to create an SSL certificate for v2.sih.com.np asynchronously.
```

> 💡 The domain can also be attached directly during deployment in the Vercel dashboard
> ("Add custom domain" in the deploy flow).

---

## 4. Cloudflare side — the DNS record (user action required)

Login to Cloudflare → open the **`sih.com.np`** zone → **DNS → Records → Add record**:

| Field | Value |
|---|---|
| **Type** | `CNAME` |
| **Name** | `v2` |
| **Target** | `cname.vercel-dns.com` |
| **Proxy status** | ⚪ **DNS only** (grey cloud — NOT orange) |
| **TTL** | `Auto` |

> ⚠️ **Proxy status must be "DNS only"** (grey cloud). If it's proxied (orange cloud), Cloudflare
> hides the CNAME behind its own IPs and **Vercel's SSL certificate validation fails** (it can't see
> the record to confirm ownership).

**Propagation:** usually a few minutes with Cloudflare; worst case 24–48h. Shorten TTL to `60s` if
you need fast cutover.

### Apex vs subdomain

| Hostname | Record |
|---|---|
| Subdomain (`v1.`, `v2.`, `www.`, …) | CNAME → `cname.vercel-dns.com` |
| Apex (`sih.com.np` itself) | A → `76.76.21.21`, or a Cloudflare-flattened CNAME at the apex (Cloudflare supports root CNAMEs and flattens them to A records) |

---

## 5. Update `APP_URL` and redeploy (done ✅)

Laravel uses `APP_URL` for URL generation (e.g. canonical links, Livewire action URLs). This is
already set on Vercel:

```bash
echo "https://v2.sih.com.np" | vercel env add APP_URL production
vercel --prod --yes
```

(The existing `vercel.json` route rewrite and `/tmp` cache config need no changes — the domain maps
to the same deployment.)

> ⚠️ **CLI gotcha (learned the hard way):** do **not** append `</dev/null` to the
> `echo | vercel env add` pipeline — the null redirect overrides the pipe and the value is silently
> dropped. Run it exactly as above.

---

## 6. Verification

### Check DNS propagation

```bash
# Node (no dig required)
node -e "require('dns').promises.resolveCname('v2.sih.com.np').then(console.log).catch(e=>console.log(e.code))"

# Or with dig (if installed)
dig +short v2.sih.com.np CNAME
```

Expected result once the Cloudflare record is live:

```
cname.vercel-dns.com
```

### Check the live site

```bash
curl -I https://v2.sih.com.np/
# HTTP/2 200  (Laravel homepage — 'Shubham International Hospital')

curl -s https://v2.sih.com.np/up
# {"status":"ok",...}  (Laravel health route)

curl -s -o /dev/null -w "%{http_code}\n" https://v2.sih.com.np/admin/login
# 200
```

Until the DNS record exists, `https://v2.sih.com.np` won't resolve — use
`https://livewire-app-gamma.vercel.app` to verify the same deployment.

---

## 7. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| `v2.sih.com.np` doesn't load (NXDOMAIN / blank) | DNS record missing or not propagated | Add the CNAME (§4) and wait for propagation |
| `ERR_SSL_PROTOCOL_ERROR` / cert not issued | Proxy status is orange (proxied) or record invisible to Vercel | Set the CNAME to **DNS only**; wait for Vercel's async cert issuance |
| Vercel dashboard shows certificate "pending" | Cert is created asynchronously after DNS exists | Wait a few minutes after the record propagates; check project → Settings → Domains |
| Site works on `.vercel.app` but links inside pages point to the wrong host | `APP_URL` not updated | `echo "https://v2.sih.com.np" | vercel env add APP_URL production` + redeploy |
| "Domain already registered" when adding to a new project | Domain attached to another project/account | Remove it from the old project first, or use the dashboard to move it |
| `v2` works but apex `sih.com.np` doesn't | Apex needs an A record, not CNAME | Add A `sih.com.np` → `76.76.21.21` |
| CSS/JS loaded over `http://` (mixed content) after mapping | `trustProxies` missing | `bootstrap/app.php` must have `$middleware->trustProxies(at: '*')` (already in this project) |

---

## 8. Replicating for a future subdomain (e.g. `v3.sih.com.np`)

1. **Vercel:** add `v3.sih.com.np` to the project (dashboard, CLI, or the API `POST` in §3).
2. **Cloudflare:** add CNAME `v3` → `cname.vercel-dns.com`, **DNS only**, TTL Auto.
3. **Env:** update `APP_URL` to `https://v3.sih.com.np` (production) — or leave it as `v2` if v3 is
   a staging/branch alias.
4. **Deploy:** `vercel --prod --yes`.
5. **Verify:** `dig +short v3.sih.com.np CNAME` → `curl -I https://v3.sih.com.np/`.

For environment-specific mappings (e.g. `staging.v2.sih.com.np` → a preview deployment), use Vercel
**Domains** in the dashboard and set the **Git Branch** on the domain so it only serves that
environment.
