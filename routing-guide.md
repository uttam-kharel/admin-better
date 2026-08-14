# Routing Guide — `Route::livewire` vs `Route::get` / `Route::post`

A deep, project-specific comparison for **Shubham International Hospital**
(Laravel 13 + Livewire v4.3.1). Read this before adding any new route.

---

## 0. The one-line answer for this project

> **Pages → `Route::livewire`. App actions → Livewire actions (`wire:click` / `wire:submit`), NOT routes.**
> Reach for `Route::get` / `Route::post` only for the five things Livewire can't or shouldn't do:
> logout without JS, downloads, external callbacks/webhooks, plain non-Livewire pages, and
> URL-addressable actions you want to link, curl, or cache.

Your project already does exactly this — 20 public pages + 8 admin sections via
`Route::livewire`, one `Route::post('/logout')`, and every CRUD action as a
Livewire component action. This guide explains *why* that shape is right, and
where you'd deliberately break out of it.

---

## 1. What each thing actually is

### `Route::livewire('/path', Component)` — the page macro

In Livewire v4, `Route::livewire` is a Laravel **macro** (defined by the
`HandleRouting` mechanism) that registers a **`GET` route** handled by
`Livewire\Mechanisms\HandleRouting\LivewirePageController`.

```php
// What you write (routes/web.php:15)
Route::livewire('/', Pages\HomepageIndex::class)->name('home');
```

```php
// What it is underneath (approximately)
Route::get('/', LivewirePageController::class)
    ->setComponent(Pages\HomepageIndex::class)
    ->name('home');
```

You can confirm it yourself:

```bash
php artisan route:list --name=home
# GET|HEAD  /  home › Livewire\Mechanisms\HandleRouting\LivewirePageController
```

When that `GET` arrives, the controller:
1. boots the component (`mount()`),
2. renders its view inside the current layout,
3. returns the **full HTML page** — crawler-friendly, no JS required to *see* it —
4. embeds the component's **snapshot** (serialized state) in the HTML, so the
   browser can continue the conversation over `/livewire/update`.

The route may also receive parameters — your resource manager is the perfect
example (`routes/admin.php:59`):

```php
Route::livewire("/{$resource}", 'admin::resource-manager.index')
    ->defaults('resource', $resource)
    ->name($resource);
```

The `{resource}` route param becomes the component's public `$resource`
property on mount. Same as a controller receiving `$resource` — just handed to
Livewire instead.

### `Route::get` / `Route::post` — the classic HTTP verbs

```php
Route::get('/export', [CsvExportController::class, 'download'])->name('export');
Route::post('/webhooks/neon', [WebhookController::class, 'handle']);
```

A controller method receives the `Request`, does work, and returns a `Response`.
No snapshot, no component state, no `/livewire/update`. The **URL is the
interface**: `GET /export?from=2026-08-01` is addressable, bookmarkable,
curl-able, cacheable.

### The hidden third surface: `/livewire/update`

The most important thing to internalize: **in a Livewire app, your "POST
routes" already exist** — there is one catch-all endpoint,
`POST /livewire/update`, that every `wire:click`, `wire:submit`, `wire:model`
debounce, and file upload flows through. The payload is the component
**snapshot + the action name + params**, and the server replies with the
diff'd HTML (or a redirect, download, toast, …).

So the real decision is not "GET vs POST" — it's:

> **Where does an action live: as a route (URL-addressable, JS-optional) or as
> a component action (stateful, Livewire-managed)?**

| | `Route::livewire` / component action | `Route::get` / `Route::post` |
|---|---|---|
| URL is the interface | ❌ actions aren't addressable | ✅ every route is a URL |
| Works without JavaScript | ✅ first render | ✅ always |
| State between interactions | ✅ component state round-trips | ❌ only what you send in the request |
| Where logic lives | in the component class | in a controller |
| Test surface | `Livewire::test(Component::class)` | `$this->get(...)` / `$this->post(...)` |

---

## 2. Request flow, side by side

### A page (both approaches return the same HTML to the browser)

```
Route::livewire('/doctors', DoctorsIndex::class)

  Browser ──GET /doctors──────────────────────────────► LivewirePageController
  Browser ◄──full HTML + embedded Livewire snapshot────  boots DoctorsIndex
                                                          mount() loads data
                                                          render() → Blade view

Route::get('/doctors', [DoctorsController::class, 'index'])

  Browser ──GET /doctors──────────────────────────────► DoctorsController@index
  Browser ◄──full HTML─────────────────────────────────  query → view('doctors.index')
```

First paint is identical. The difference appears on the **next interaction**:

```
Livewire:  click "Search" ──POST /livewire/update {snapshot, action:"search"}
           ──► server re-runs ONLY the component, returns new HTML for the region
           ──► browser patches the DOM. No reload, state (search text, page, filters) preserved.

Classic:   submit form ──POST /doctors/search {q:"heart"}
           ──► server returns a fresh page (redirect-after-post → GET)
           ──► full reload. Any state you didn't send is gone.
```

### An action (create / edit / delete / search / export)

```
Livewire:  wire:click="save"  ──► POST /livewire/update
           action: save       ──► component validates, persists, flashes, closes modal
           response: DOM patch. The modal, the form values, the error messages all
           round-trip inside the component — no route needed.

Classic:   POST /admin/doctors {name, ..., _token}
           ──► controller validates (FormRequest), persists,
           ──► redirect()->route('admin.doctors')->with('success', ...)
           The browser had to navigate to a URL, submit, and come back.
```

For a **CRUD admin with modals and inline search**, the Livewire shape removes
an entire class of routing boilerplate: you never write
`POST /admin/doctors`, `PUT /admin/doctors/{id}`, `DELETE /admin/doctors/{id}`…
for 24 resources that would be ~120 routes for zero added capability. The
component's action methods *are* your verbs.

---

## 3. The axes that actually matter

### 3.1 JavaScript dependency
- `Route::livewire` pages render without JS, but every **action** needs JS.
- Classic `GET`/`POST` works in a bare-bones client (curl, old browser, screen
  readers in some setups, automated scrapers).
- **Your call**: the admin is used by staff in modern browsers — Livewire is
  fine. The **public** pages must degrade gracefully — they already do,
  because first render is server-side HTML and the only JS-dependent parts are
  the forms (appointment, careers), which degrade to normal browser behavior.

### 3.2 URL-addressability
- Livewire actions have **no URL**. You can't link to "delete doctor #4", you
  can't deep-link a filtered state (search text, range) — unless you sync it to
  the query string (`$this->queryString` — worth knowing).
- Classic routes are addressable: `?from=&to=` filters, pagination, CSV export
  — all linkable, shareable, curl-able.
- **Your call**: your admin doesn't need addressable actions (it's a
  control panel, not a shareable surface). Your **CSV export** is the one place
  an addressable URL genuinely helps (see §5.2).

### 3.3 State management
- Livewire keeps the component's public properties alive across interactions
  (server-side truth, mirrored in the snapshot). Search-as-you-type,
  multi-step modals, pagination — zero extra code.
- Classic HTTP: every interaction is stateless; you re-derive state from the
  request + DB. The browser is the state container.
- **Your call**: your resource manager (search + sort + bulk-select + modal
  forms) would be painful as classic routes — it's exactly what Livewire is
  for.

### 3.4 SEO and first paint
- Both produce complete server-rendered HTML for the first GET. No SPA blank
  screen. Google sees your `/doctors`, `/services`, `/careers` content either
  way.
- `wire:navigate` (your sidebar links use it) makes subsequent navigations
  SPA-style fast — but the initial page is always full HTML.
- **Your call**: keep all public pages as `Route::livewire` — you get SEO-safe
  first render *and* instant in-app navigation.

### 3.5 Performance / payload
- Livewire round-trips the whole snapshot on every action (your resource-manager
  form arrays make those payloads chunky). It's *usually* fine, but a huge
  component state means fatter POST bodies and slower updates.
- Classic routes send only the form fields.
- **Your call**: you're already on the right side of this — `public/build`
  split keeps JS small, DB caching keeps renders cheap. If the resource-manager
  ever feels laggy, the fix is splitting the monolith (see architecture
  review), not converting to routes.

### 3.6 Testing
- `Livewire::test(Component::class)` — call actions, set properties, assert
  HTML/state/flash. Fast, no HTTP server.
- `$this->get('/doctors')` / `$this->post('/admin/doctors', [...])` — true
  end-to-end through the router, middleware, CSRF, and session.
- **Your call**: your project currently has **zero real tests** (only the
  framework Examples). The cheapest wins: `Livewire::test` for the admin
  components, HTTP tests for the `logout` POST and any future webhook/export
  route.

### 3.7 CSRF and method verbs
- Both are CSRF-protected (Livewire handles its own token; classic forms need
  `@csrf`).
- Livewire actions are all POSTs to one endpoint — no `GET` with side effects.
- Classic routes: `GET` must be read-only, `POST` = change. Your `logout` is
  correctly a `POST` (a `GET /logout` would be logged out by prefetchers).

### 3.8 Middleware, layouts, naming
- Identical for both: `Route::middleware(['auth:admin'])`, prefixes, `->name()`.
- Your `routes/admin.php` already demonstrates the clean shape: a `web`-
  middleware group, `admin` prefix, `admin.` name prefix, loaded from
  `bootstrap/app.php`.

### 3.9 When Livewire is wrong for the job
- Webhooks/callbacks (Neon, payment gateways) — must be plain `POST` routes,
  no snapshot, no session reliance.
- Huge download streams — a controller returning `response()->streamDownload()`
  (or `StreamedResponse`) is simpler than a Livewire action.
- Cron/admin-only maintenance endpoints.
- A page with no interactivity that you want to cache at the HTTP layer.

---

## 4. Decision table (for this project)

| Need | Use | Example |
|---|---|---|
| A public or admin page | `Route::livewire` | `/`, `/doctors`, `/admin/settings` |
| In-page action (save, delete, search, toggle) | Livewire action | `wire:click="save"` |
| Logout (no JS, must-always-work) | `Route::post` | `routes/admin.php:66` |
| File download / CSV export | `Route::get` + controller (see §5.2) | `GET /admin/analytics/export.csv` |
| External callback / webhook | `Route::post` | future Neon webhook |
| Health check | `Route::get` | `/up` (Laravel built-in) |
| Plain page w/o Livewire (rare) | `Route::get` + view | a static landing page |

**Anti-pattern for this project**: building REST-style `POST`/`PUT`/`DELETE`
route sets for admin CRUD *alongside* Livewire components. Pick one state
strategy per surface — the admin already picked Livewire. Adding routes would
double the interface for the same job.

---

## 5. Audit of your current routes

### What's already right
- **All 20 public pages** → `Route::livewire` with `wire:navigate` links. ✅
- **All 8 admin sections** (login, dashboard, analytics, menus, 24 resource
  routes) → `Route::livewire`. ✅
- **`Route::post('/logout')`** (admin.php:66) → correct: works without JS,
  POST-only (no prefetch logouts), CSRF-protected. ✅
- **`/up` health route** → plain GET, no Livewire overhead. ✅

### One place to consider breaking out: the CSV export

`exportCsv()` (analytics component) is a Livewire action that returns
`response()->streamDownload(...)`. It works. But:

- The **URL isn't addressable** — you can't link "download this month's CSV".
- It requires a Livewire round-trip before the download starts.
- Testing it means `Livewire::test` + asserting the download — awkward.

A classic GET route is the idiomatic fit:

```php
// routes/admin.php (inside the auth:admin group)
Route::get('/analytics/export.csv', App\Http\Controllers\Admin\AnalyticsExportController::class)
    ->name('analytics.export');
```

```php
// app/Http/Controllers/Admin/AnalyticsExportController.php
class AnalyticsExportController extends Controller
{
    public function __invoke(Request $request)
    {
        // validate ?from=&to=, build rows, return streamDownload()
    }
}
```

Then the button becomes a real link:
```html
<a href="{{ route('admin.analytics.export', ['from' => $from, 'to' => $to]) }}"
   class="...">Export CSV</a>
```

Same bytes, but now: curl-able, bookmarkable, testable with
`$this->get('/admin/analytics/export.csv?from=...')`, and no snapshot payload.

---

## 6. Pitfalls to avoid

1. **`wire:navigate` + a plain `Route::get` page**: navigate works (Livewire
   fetches the HTML and swaps it), but the plain page's own scripts must be
   idempotent — a page that expects a full reload (heavy inline `onload` JS)
   will misbehave. Keep all navigable pages as Livewire components or
   script-free views.
2. **`GET` with side effects**: never make a download/delete a `GET` link
   unless it's idempotent. `logout` is POST for exactly this reason.
3. **Double CSRF**: don't wrap a Livewire component inside a plain `<form
   method="POST">` — Livewire manages its own token; a wrapping form breaks the
   update requests. (Your forms use `wire:submit`, correct.)
4. **Route model binding + Livewire**: `Route::livewire('/doctors/{doctor}',
   ...)` binds the model and Livewire passes it to `mount()` — but the binding
   happens per request; if the model changes you must re-fetch. Your slug-based
   routes avoid this.
5. **Snapshot bloat**: keep component state minimal. Large `form` arrays (your
   resource manager) round-trip on every keystroke-action; prefer per-field
   `wire:model.lazy` where feedback latency matters.
6. **Redirect-after-POST** is a classic-routing pattern; in Livewire you
   `redirect()->route(...)` from within an action or flash a message — don't
   mix both idioms in one surface.

---

## 7. Recommendation, restated

| Surface | Strategy | Why |
|---|---|---|
| Public pages | `Route::livewire` + `wire:navigate` | SEO-safe first paint + instant nav |
| Admin pages & CRUD | `Route::livewire` + component actions | 24 resources without 120 routes; modals/search stay stateful |
| Logout | `Route::post` | works without JS, verb-correct |
| CSV export | **upgrade to `Route::get`** | addressable, curl-able, HTTP-testable |
| Future webhooks | `Route::post` | external callers need plain HTTP |

The mental model: **`Route::livewire` is your page-rendering layer;
`/livewire/update` is your action layer; `Route::get`/`Route::post` are for the
narrow set of things that need a real URL, a real verb, or no JavaScript.**

*Related: `routes/web.php` + `routes/admin.php` (the split is documented in
`uttam.md` §1).*
