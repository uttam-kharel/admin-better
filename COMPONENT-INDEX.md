# Component Index

Reusable Blade components for this project. Every component here is showcased live on
**/design-guide** — when you add or change a component, update that page too.

## UI primitives (`resources/views/components/ui/`)

| Component | Props | Usage |
|---|---|---|
| `<x-ui.badge>` | `variant` (neutral, primary, success, warning, info, danger, cyan, orange, indigo, violet), `dot` (bool) | Colored status pills. Variant vocabulary maps to domain statuses: success (active/completed/confirmed/approved/hired), warning (pending/idle), info (responded/reviewed), danger (failed/rejected/cancelled/blocked), cyan (running), orange (paused), indigo (in_progress), violet (in_review/interviewed), neutral (archived/backlog/cancelled/inactive) |
| `<x-ui.button>` | `variant` (primary, secondary, outline, ghost, destructive, emergency), `size` (sm, md, lg), `type` | Buttons and link-style buttons (`href` renders an `<a>`) |
| `<x-ui.card>` | `padding` ("default" or "none"), `class` | Surface container with border + radius |
| `<x-ui.stat-card>` | `icon`, `label`, `value`, `description` | Dashboard metric tile |
| `<x-ui.pill>` | `color`, `size`, `outline` | Small label chips |
| `<x-ui.logo>` | — | Site logo markup |
| `<x-ui.modal>` | `open` (bool), `title`, `footer` (slot) | Accessible modal shell (Esc to close, click-outside) |

## Form controls (`resources/views/components/form/`)

| Component | Props | Usage |
|---|---|---|
| `<x-form.input>` | `name`, `label`, `type`, `model`, `required` | Text/number/date inputs |
| `<x-form.textarea>` | `name`, `label`, `model`, `rows` | Multi-line text |
| `<x-form.select>` | `name`, `label`, `model`, `options` | Native `<select>` |
| `<x-form.select-menu>` | `name`, `label`, `model`, `options`, `required` | Alpine searchable dropdown — live search appears when > 6 options; Esc/click-outside to close; syncs via `$wire.set` |
| `<x-form.search-input>` | `model`, `placeholder` | Live search box |
| `<x-form.label>` | `for`, `required` | Field labels |

## Public sections (`resources/views/components/sections/`)

| Component | Usage |
|---|---|
| `<x-sections.faq-accordion>` | Alpine accordion; also available to CMS editors as the `faq` content block |
| `<x-sections.cta-panel>` | Contact CTA band (homepage + reusable) |
| `<x-sections.content-block>` | CMS block renderer — handles `text`, `faq`, `stats` block types |

## Layout partials (`resources/views/components/partials/`)

| Component | Usage |
|---|---|
| `<x-partials.base>` | Shared `<head>` + assets + theme-colors injection (public & admin) |
| `<x-partials.header>` / `<x-partials.top-bar>` | Public navigation |
| `<x-partials.footer>` | Public footer |
| `<x-partials.theme-colors>` | Emits `--color-*` CSS variables from Site Settings theme — the actual theming engine |

## Conventions

- Status colors always go through `<x-ui.badge>` — never hand-roll status colors inline.
- Semantic tokens (`bg-primary-soft`, `text-muted-foreground`, `border-border`, …) only — no raw hex.
- New reusable component? Add it here **and** to `/design-guide`.
