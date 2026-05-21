# Run on localhost (Windows)

Use **one URL only**: **http://localhost:8000** (matches `APP_URL` in `.env`).

Do not mix `127.0.0.1` and `localhost` in the browser — they are treated as different sites.

## First-time setup (run once)

Open PowerShell in the project folder (`g:\expensetracker`):

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
```

## Every day (normal use)

**Terminal 1** — Laravel:

```powershell
php artisan serve
```

**Terminal 2** — only if you change CSS/JS and want live reload:

```powershell
# In .env set: VITE_USE_DEV=true
npm run dev
```

If you are **not** running `npm run dev`, keep in `.env`:

```env
VITE_USE_DEV=false
```

Then after CSS/JS changes run: `npm run build`

## Quick health check

```powershell
php scripts/local-check.php
```

You want: `site_pages: 8`, `build/manifest: OK`, `public/hot: (none)`.

## Login (after seed)

| Role  | Email                      | Password  |
|-------|----------------------------|-----------|
| User  | user@expensetracker.test   | password  |
| Admin | admin@expensetracker.test  | password  |

## If you see 404 on `(index):1`

1. **Page 404** — database empty. Run: `php artisan db:seed`
2. **CSS/JS 404** — run: `npm run build`, delete `public\hot` if it exists, hard refresh (`Ctrl+F5`)

## MySQL must be running

`.env` uses database `personal` on `127.0.0.1:3306`. Start XAMPP/WAMP/MySQL before `php artisan serve`.
