# shadcn/ui — Mero Expense Tracker

User-facing frontend uses [shadcn/ui](https://ui.shadcn.com) on **Tailwind CSS v4**.

## Theme (light / dark)

- **Toggle:** sun/moon button in the marketing header, user panel header, and report toolbar.
- **Persistence:** `localStorage.theme` = `light` or `dark`.
- **No flash:** `resources/views/layouts/partials/theme-init.blade.php` runs before paint.
- **Logic:** `resources/js/theme.js` (syncs across tabs, respects system preference when unset).

## Mobile responsive

- `resources/css/responsive.css` — safe areas, touch targets (44px), stacked toolbars, full-width buttons on small screens.
- `app.css` — table card stacks (`table-mobile-stack`), sidebar drawer, settings nav scroll.
- Test at 320px–768px widths; use real devices or DevTools device mode.

## Blade components

```blade
<x-ui.button>Save</x-ui.button>
<x-ui.button variant="outline" size="sm" href="/path">Cancel</x-ui.button>
<x-ui.input name="email" type="email" class="mt-1" />
<x-ui.label for="email">Email</x-ui.label>
<x-ui.select name="currency">...</x-ui.select>
<x-ui.textarea name="notes" rows="4" />
<x-ui.card title="Title">...</x-ui.card>
<x-ui.alert variant="success">Saved.</x-ui.alert>
<x-ui.badge variant="secondary">Draft</x-ui.badge>
<x-ui.checkbox name="remember" />
<x-ui.theme-toggle />
```

Breeze: `<x-primary-button>`, `<x-text-input>`, `<x-input-label>` → shadcn wrappers.

## Build & deploy

```bash
npm ci
npm run build
```

Upload `public/build/`. Delete `public/hot` on production.

## Maintenance scripts

```bash
npm run migrate:ui       # Bootstrap utilities → Tailwind
npm run migrate:buttons  # btn classes → x-ui.button
```

PDF reports and Filament `/admin` are excluded.
