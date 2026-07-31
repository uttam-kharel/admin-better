# Adding a Custom Domain — Complete Playbook

How to bind a custom domain (e.g. `v1.sih.com.np`) to a Laravel project deployed on **Vercel**,
with DNS hosted on **Cloudflare**. This documents the **exact steps and commands that were used**
on 2026-07-31 to bind `v1.sih.com.np` to the `livewire-app` project, and how to replicate it for
any future subdomain.

> Related: [`DOMAIN-MAP.md`](DOMAIN-MAP.md) is the *current state* of the `v1.sih.com.np` mapping;
> this doc is the *how-to* that produced it.

---

## Table of contents

1. [What "adding a custom domain" involves](#1-what-adding-a-custom-domain-involves)
2. [Prerequisites](#2-prerequisites)
3. [Step 1 — Check what's already attached](#3-step-1--check-whats-already-attached)
4. [Step 2 — Vercel side: attach the domain to the project](#4-step-2--vercel-side-attach-the-domain-to-the-project)
5. [Step 3 — Move a domain between projects (if it's already in use)](#5-step-3--move-a-domain-between-projects-if-its-already-in-use)
6. [Step 4 — Cloudflare side: add the DNS record](#6-step-4--cloudflare-side-add-the-dns-record)
7. [Step 5 — Set `APP_URL` and redeploy](#7-step-5--set-app_url-and-redeploy)
8. [Step 6 — Verify](#8-step-6--verify)
9. [Troubleshooting](#9-troubleshooting)
10. [What was done on 2026-07-31 (this project's actual record)](#10-what-was-done-on-2026-07-31-this-projects-actual-record)

---

## 1. What "adding a custom domain" involves

Two independent sides must both be configured before traffic arrives:

```
Browser ── https://v1.sih.com.np/
   │
   ▼  Cloudflare DNS (sih.com.np zone)
CNAME  v1 → cname.vercel-dns.com     (DNS only / grey cloud)
   │
   ▼  Vercel edge (matches Host header v1.sih.com.np → project)
Vercel project  →  production deployment
   │
   ▼  vercel.json route "/(.*)" → /api/index.php
Laravel  →  HTML page / JSON
```

1. **Vercel side** — the domain is *attached* to the project. Vercel *aliases* the production
   deployment to it and auto-issues an SSL certificate (asynchronously).
2. **Cloudflare side** — a DNS record *points* the hostname at Vercel's edge so traffic arrives.

Then Laravel's `APP_URL` env var is updated so generated URLs use the domain, and the project is
redeployed.

---

## 2. Prerequisites

- The **Vercel CLI** logged in (`vercel login`) or an **auth token** for the API. The token lives
  in `~/.local/share/com.vercel.cli/auth.json` (CLI version dependent; also try `~/.vercel/auth.json`):
  ```bash
  TOKEN=$(node -e "console.log(JSON.parse(require('fs').readFileSync(process.env.HOME+'/.local/share/com.vercel.cli/auth.json','utf8')).token)")
  ```
- The **project ID + team ID** (in `.vercel/project.json` after `vercel link --yes`). For this
  project: `projectId=prj_uahWWDuQWtydeKHS0SLLUehayN78`, `teamId=team_TKO4eklVKvpy5hlk9KtncXoD`.
- Access to the **Cloudflare** dashboard for the domain's zone (`sih.com.np`).
- **`npx vercel`** works even when `vercel` is not on `PATH` (as in some shells here).

---

## 3. Step 1 — Check what's already attached

Before adding, list the domains on the project so you don't duplicate or collide:

```bash
curl -s -H "Authorization: Bearer $TOKEN" \
  "https://api.vercel.com/v10/projects/prj_uahWWDuQWtydeKHS0SLLUehayN78/domains?teamId=team_TKO4eklVKvpy5hlk9KtncXoD" \
  | node -e "let d='';process.stdin.on('data',c=>d+=c).on('end',()=>{JSON.parse(d).domains.forEach(x=>console.log(x.name,'verified:',x.verified))})"
```

If the domain you want is attached to **another** project, see Step 3 (move it) before attaching.

---

## 4. Step 2 — Vercel side: attach the domain to the project

### Option A — Vercel dashboard
Project → **Settings → Domains** → **Add** → type the domain → Save.

### Option B — Vercel API (what was used here)

```bash
# Add the domain to the project
curl -s -X POST \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"v1.sih.com.np"}' \
  "https://api.vercel.com/v10/projects/prj_uahWWDuQWtydeKHS0SLLUehayN78/domains?teamId=team_TKO4eklVKvpy5hlk9KtncXoD"
```

A successful response includes `"verified": true`:

```json
{"name":"v1.sih.com.np","apexName":"sih.com.np","projectId":"prj_uahWWDuQWtydeKHS0SLLUehayN78","verified":true,...}
```

### Option C — Vercel CLI

```bash
vercel domains add v1.sih.com.np     # registers at team level — still must attach to the project
```

> ⚠️ This only **registers** the domain on your team — you must still **attach** it to the project
> (dashboard → Settings → Domains, or the API call above).

---

## 5. Step 3 — Move a domain between projects (if it's already in use)

A domain can only be attached to **one** Vercel project. `v1.sih.com.np` was originally on the
`laravel-vercel` project, so the flow was: **detach → attach → update APP_URL → redeploy**.

```bash
# 1. Detach from the old project (laravel-vercel)
curl -s -X DELETE \
  -H "Authorization: Bearer $TOKEN" \
  "https://api.vercel.com/v10/projects/prj_1qegr4gzYIGS0ixz1xIVTsowflFL/domains/v1.sih.com.np?teamId=team_TKO4eklVKvpy5hlk9KtncXoD"
# → {}  (success)

# 2. Attach to the new project (livewire-app) — see Step 2

# 3. Update APP_URL on the NEW project — see Step 5
```

Dashboard alternative: open the old project → **Settings → Domains** → remove, then add on the new
one.

> ⚠️ **Also fix the OLD project's `APP_URL`** after a move — see the note in §7.

---

## 6. Step 4 — Cloudflare side: add the DNS record

Login to Cloudflare → open the **`sih.com.np`** zone → **DNS → Records → Add record**:

| Field | Value |
|---|---|
| **Type** | `CNAME` |
| **Name** | `v1` (the subdomain) |
| **Target** | `cname.vercel-dns.com` |
| **Proxy status** | ⚪ **DNS only** (grey cloud — NOT orange) |
| **TTL** | `Auto` |

> ⚠️ **Proxy status must be "DNS only"** (grey cloud). If proxied (orange cloud), Cloudflare hides
> the CNAME behind its own IPs and **Vercel's SSL certificate validation fails**.

**Propagation:** usually a few minutes; worst case 24–48h. Shorten TTL to `60s` for a fast cutover.

### Apex vs subdomain

| Hostname | Record |
|---|---|
| Subdomain (`v1.`, `v2.`, `www.`, …) | CNAME → `cname.vercel-dns.com` |
| Apex (`sih.com.np` itself) | A → `76.76.21.21`, or a Cloudflare-flattened CNAME at the apex |

---

## 7. Step 5 — Set `APP_URL` and redeploy

Laravel uses `APP_URL` for absolute-URL generation. Set it to the new domain (production env), then
redeploy so the alias applies:

```bash
# CLI: if APP_URL already exists, `env add` creates a SECOND value instead of replacing it —
# remove the old value first, or use the API PATCH below.
vercel env rm APP_URL production --yes
vercel env add APP_URL production <<< "https://v1.sih.com.np"

# Or API (this project's APP_URL env id: 4EGudVG4rKGKvnah) — this REPLACES the value:
curl -s -X PATCH \
  -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"value":"https://v1.sih.com.np"}' \
  "https://api.vercel.com/v9/projects/prj_uahWWDuQWtydeKHS0SLLUehayN78/env/4EGudVG4rKGKvnah?teamId=team_TKO4eklVKvpy5hlk9KtncXoD"
```

> ⚠️ **Also fix the OLD project's `APP_URL`** after a move — its value still points at the moved
> domain. When `v1` moved to `livewire-app`, the `laravel-vercel` project's `APP_URL` (env id
> `KZwRbDDOwbEZWe7K`) was still `https://v1.sih.com.np` and had to be patched to
> `https://laravel-vercel-pink.vercel.app`, otherwise that API would generate hospital-site URLs.
> If you move a domain, check the old project's `APP_URL` and update it too.

> 💡 Env ids are stable until the variable is deleted/recreated. If the PATCH 404s, find the current
> id with `curl .../v9/projects/<PROJECT_ID>/env?teamId=<TEAM_ID>` and look for the `APP_URL` entry.

```bash
# Redeploy (npx works if vercel isn't on PATH)
npx vercel --prod --yes
```

The deploy output confirms the alias:

```
▲ Aliased         https://v1.sih.com.np
✓ Ready in 32s
We are attempting to create an SSL certificate for ... asynchronously.
```

> ⚠️ **CLI gotcha:** do **not** append `</dev/null` to `echo | vercel env add` pipelines — the null
> redirect overrides the pipe and the value is silently dropped.

---

## 8. Step 6 — Verify

```bash
# DNS propagates?
node -e "require('dns').promises.resolveCname('v1.sih.com.np').then(console.log).catch(e=>console.log(e.code))"
# → [ 'cname.vercel-dns.com' ]

# Site live?
curl -I https://v1.sih.com.np/            # HTTP/2 200
curl -s  https://v1.sih.com.np/up         # {"status":"ok",...}

# SSL cert issued?
echo | timeout 10 openssl s_client -connect v1.sih.com.np:443 -servername v1.sih.com.np 2>/dev/null \
  | openssl x509 -noout -subject             # CN = v1.sih.com.np

# No mixed content? (assets must be https:// on the same host)
curl -s https://v1.sih.com.np/ | grep -o 'href="[^"]*build[^"]*"' | head -2
```

---

## 9. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| Domain doesn't load (NXDOMAIN / blank) | DNS record missing or not propagated | Add the CNAME (§6) and wait |
| `ERR_SSL_PROTOCOL_ERROR` / cert pending | Proxied (orange) or record invisible to Vercel | Set CNAME to **DNS only**; wait for async cert issuance |
| "Domain already registered" / can't attach | Domain still attached to another project | Detach from the old project first (§5) |
| Site works on `.vercel.app` but links use the wrong host | `APP_URL` not updated | Set `APP_URL` (§7) + redeploy |
| `v1` works but apex doesn't | Apex needs an A record | Add A `sih.com.np` → `76.76.21.21` |
| CSS/JS served as `http://` (mixed content) | `trustProxies` missing | `bootstrap/app.php`: `$middleware->trustProxies(at: '*')` |
| `vercel: command not found` | CLI not on `PATH` in this shell | Use `npx vercel ...` |

---

## 10. What was done on 2026-07-31 (this project's actual record)

The exact sequence that bound `v1.sih.com.np` to the `livewire-app` hospital site:

| # | Action | Command (abridged) | Result |
|---|---|---|---|
| 1 | List domains on both projects | `GET /v10/projects/{id}/domains` | `v1` on `laravel-vercel`; `v2` on `livewire-app` |
| 2 | Detach `v1` from `laravel-vercel` | `DELETE .../prj_1qegr4gzYIGS0ixz1xIVTsowflFL/domains/v1.sih.com.np` | `{}` — gone from that project |
| 3 | Attach `v1` to `livewire-app` | `POST .../prj_uahWWDuQWtydeKHS0SLLUehayN78/domains` | `"verified": true` |
| 4 | Set `APP_URL` on `livewire-app` | `PATCH .../env/4EGudVG4rKGKvnah` → `https://v1.sih.com.np` | key/target intact |
| 5 | Redeploy | `npx vercel --prod --yes` | `▲ Aliased https://v1.sih.com.np` |
| 6 | Fix API project's `APP_URL` | `PATCH .../prj_1qegr4gzYIGS0ixz1xIVTsowflFL/env/KZwRbDDOwbEZWe7K` → `https://laravel-vercel-pink.vercel.app` | keeps API URLs correct |
| 7 | Redeploy API project | `npx vercel --prod --yes` | `▲ Aliased https://laravel-vercel-pink.vercel.app` |
| 8 | Verify | curls on all 11 routes | All **200**, `CN=v1.sih.com.np`, assets over `https://` |

**Why it worked instantly:** the Cloudflare CNAME `v1 → cname.vercel-dns.com` already existed
(added during the API project's deployment), so DNS was already pointed at Vercel — only the
Vercel-side attachment had to change.

**Notes for future subdomains (`v3`, `www`, etc.):** repeat Steps 2 → 4 → 5 → 6. If the new domain
is currently attached to another project, insert Step 3 first.
