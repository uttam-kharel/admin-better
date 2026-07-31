# Domain Map — `v1.sih.com.np` → Vercel (livewire-app)

This document maps the custom domain **`v1.sih.com.np`** (DNS hosted on **Cloudflare**) to the
**Vercel** project `livewire-app` (Shubham International Hospital — Laravel 13 + Livewire 4), and
explains exactly how the mapping works so it can be replicated for any future subdomain.

> **History:** `v1.sih.com.np` was originally attached to the **laravel-vercel** API project. On
> **2026-07-31** it was **moved** to this project (the API still runs at
> `https://laravel-vercel-pink.vercel.app`). The Cloudflare CNAME already existed, so the site went
> live at `https://v1.sih.com.np` the moment the redeploy finished.

---

## 1. Current mapping

| Hostname | Layer | Points to | Provider | Status |
|---|---|---|---|---|
| `v1.sih.com.np` | DNS | CNAME → `cname.vercel-dns.com` | Cloudflare | ✅ **added & propagated** |
| `v1.sih.com.np` | Vercel domain → project | production deployment of `livewire-app` | Vercel | ✅ attached, `verified: true` |
| `v1.sih.com.np` | Vercel alias | production deployment | Vercel | ✅ aliased during deploy |
| `livewire-app-gamma.vercel.app` | Vercel alias | production deployment | Vercel | ✅ active (default URL / backup) |
| `v2.sih.com.np` | Vercel domain → project | production deployment | Vercel | ⚪ attached but **no Cloudflare DNS record** — not in use |

> 🟢 **Status: LIVE** — verified working 2026-07-31: `https://v1.sih.com.np/` → `200` (renders
> "Shubham International Hospital" with a valid SSL cert), `/up` → `200`, CSS/JS served from
> `https://v1.sih.com.np/build/...` (no mixed content). The steps in this doc are exactly how it was
> done.

**Reference values:**

- Vercel project: `livewire-app` (team `the-sickness1`)
- `projectId`: `prj_uahWWDuQWtydeKHS0SLLUehayN78`
- `orgId` / `teamId`: `team_TKO4eklVKvpy5hlk9KtncXoD`
- Vercel CNAME target: `cname.vercel-dns.com` → resolves to `76.76.21.241`, `66.33.60.193`
- Vercel apex A-record target: `76.76.21.21`
- Production env var `APP_URL`: `https://v1.sih.com.np`
- Default deployment URL: `https://livewire-app-gamma.vercel.app`

---

## 2. How the mapping works

```
Browser ── https://v1.sih.com.np/
   │
   ▼  Cloudflare DNS (sih.com.np zone)
CNAME  v1 → cname.vercel-dns.com     (DNS only / grey cloud)
   │
   ▼  Vercel edge (matches Host header v1.sih.com.np → project)
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

## 3. Moving a domain between Vercel projects

A domain can only be attached to **one** Vercel project at a time. To move `v1.sih.com.np` from the
old project (`laravel-vercel`) to this one (`livewire-app`):

### Step 1 — detach from the old project

```bash
# Read the CLI auth token (location varies by CLI version; also try ~/.vercel/auth.json)
TOKEN=$(node -e "console.log(JSON.parse(require('fs').readFileSync(process.env.HOME+'/.local/share/com.vercel.cli/auth.json','utf8')).token)")

curl -s -X DELETE \
  -H "Authorization: Bearer $TOKEN" \
  "https://api.vercel.com/v10/projects/prj_1qegr4gzYIGS0ixz1xIVTsowflFL/domains/v1.sih.com.np?teamId=team_TKO4eklVKvpy5hlk9KtncXoD"
```

(Dashboard alternative: open the old project → **Settings → Domains** → remove the domain.)

### Step 2 — attach to the new project

```bash
# Returns {"name":"v1.sih.com.np","verified":true,...}
curl -s -X POST \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"v1.sih.com.np"}' \
  "https://api.vercel.com/v10/projects/prj_uahWWDuQWtydeKHS0SLLUehayN78/domains?teamId=team_TKO4eklVKvpy5hlk9KtncXoD"
```

### Step 3 — update `APP_URL` and redeploy

```bash
# Update the env var (this project's APP_URL env id: 4EGudVG4rKGKvnah)
curl -s -X PATCH \
  -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"value":"https://v1.sih.com.np"}' \
  "https://api.vercel.com/v9/projects/prj_uahWWDuQWtydeKHS0SLLUehayN78/env/4EGudVG4rKGKvnah?teamId=team_TKO4eklVKvpy5hlk9KtncXoD"

# Redeploy — the output confirms the alias:
npx vercel --prod --yes
# ▲ Aliased         https://v1.sih.com.np
# ✓ Ready in 32s
```

> 💡 The env var id (`4EGudVG4rKGKvnah`) is stable until that variable is deleted/recreated. If the
> PATCH ever returns a 404, find the current id with
> `curl -s -H "Authorization: Bearer $TOKEN" "https://api.vercel.com/v9/projects/prj_uahWWDuQWtydeKHS0SLLUehayN78/env?teamId=team_TKO4eklVKvpy5hlk9KtncXoD"`
> and look for the `APP_URL` entry.

> 💡 Vercel CLI note: the `vercel` binary may not be on `PATH` in every shell — use
> `npx --yes vercel ...` if `vercel: command not found`.

---

## 4. Cloudflare side — the DNS record (already in place ✅)

The CNAME for `v1` was added when the API project was deployed, so **no change is needed**. For
reference, in Cloudflare → zone **`sih.com.np`** → **DNS → Records**:

| Field | Value |
|---|---|
| **Type** | `CNAME` |
| **Name** | `v1` |
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

## 5. Adding a *new* domain from scratch (e.g. `v2.sih.com.np`)

If you need a second subdomain, the full flow is:

1. **Vercel:** attach the domain to the project — dashboard (**Settings → Domains → Add**) or the
   API `POST` in §3 (skip the detach step if the domain is new).
2. **Cloudflare:** add a **CNAME** record (see the table in §4, substituting the name), **DNS only**,
   TTL Auto.
3. **Env:** set `APP_URL` to the new domain (production) — or leave it as `v1` if the new domain is
   a staging/branch alias.
4. **Deploy:** `npx vercel --prod --yes`.
5. **Verify:** `dig +short <name>.sih.com.np CNAME` → `curl -I https://<name>.sih.com.np/`.

> ⚠️ A Vercel-attached domain with **no DNS record** (like the leftover `v2.sih.com.np` in this
> project) is harmless but inert — traffic simply never reaches it until the CNAME exists. Remove it
> via the dashboard if you don't plan to use it.

---

## 6. Verification

### Check DNS propagation

```bash
# Node (no dig required)
node -e "require('dns').promises.resolveCname('v1.sih.com.np').then(console.log).catch(e=>console.log(e.code))"

# Or with dig (if installed)
dig +short v1.sih.com.np CNAME
```

Expected result:

```
cname.vercel-dns.com
```

### Check the live site

```bash
curl -I https://v1.sih.com.np/
# HTTP/2 200  (Laravel homepage — 'Shubham International Hospital')

curl -s https://v1.sih.com.np/up
# {"status":"ok",...}  (Laravel health route)

curl -s -o /dev/null -w "%{http_code}\n" https://v1.sih.com.np/admin/login
# 200
```

---

## 7. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| `v1.sih.com.np` doesn't load (NXDOMAIN / blank) | DNS record missing or not propagated | Add the CNAME (§4) and wait for propagation |
| `ERR_SSL_PROTOCOL_ERROR` / cert not issued | Proxy status is orange (proxied) or record invisible to Vercel | Set the CNAME to **DNS only**; wait for Vercel's async cert issuance |
| Vercel dashboard shows certificate "pending" | Cert is created asynchronously after DNS exists | Wait a few minutes after the record propagates; check project → Settings → Domains |
| Site works on `.vercel.app` but links inside pages point to the wrong host | `APP_URL` not updated | Set `APP_URL=https://v1.sih.com.np` (production) + redeploy |
| "Domain already registered" / can't attach to the new project | Domain still attached to the old project | Detach from the old project first (§3 step 1) |
| The *other* project's site now 404s at this domain | The domain was moved to this project (one-project-per-domain) | Expected — point the domain back, or give the other project a new subdomain |
| `v1` works but apex `sih.com.np` doesn't | Apex needs an A record, not CNAME | Add A `sih.com.np` → `76.76.21.21` |
| CSS/JS loaded over `http://` (mixed content) after mapping | `trustProxies` missing | `bootstrap/app.php` must have `$middleware->trustProxies(at: '*')` (already in this project) |

---

## 8. Replicating for a future subdomain (e.g. `v3.sih.com.np`)

1. **Vercel:** add `v3.sih.com.np` to the project (dashboard, CLI, or the API `POST` in §3).
2. **Cloudflare:** add CNAME `v3` → `cname.vercel-dns.com`, **DNS only**, TTL Auto.
3. **Env:** update `APP_URL` to `https://v3.sih.com.np` (production) — or leave it as `v1` if v3 is
   a staging/branch alias.
4. **Deploy:** `npx vercel --prod --yes`.
5. **Verify:** `dig +short v3.sih.com.np CNAME` → `curl -I https://v3.sih.com.np/`.

For environment-specific mappings (e.g. `staging.v1.sih.com.np` → a preview deployment), use Vercel
**Domains** in the dashboard and set the **Git Branch** on the domain so it only serves that
environment.
