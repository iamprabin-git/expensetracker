# Deployment guide — Mero Expense Tracker

Essential steps to deploy safely to production.

## 1. Server requirements

- PHP **8.3+** (8.4 recommended) with extensions: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd` or `imagick` (images)
- Composer 2.x
- Node.js **18+** (build assets once; not required on server if you deploy `public/build`)
- MySQL 8+ / PostgreSQL 15+ / MariaDB (recommended for production) — or SQLite for small single-server setups
- Web server: Nginx or Apache with **document root = `public/`**
- HTTPS certificate (Let's Encrypt or your host)

## 2. Production `.env`

Copy `.env.example` to `.env` on the server and set:

```env
APP_NAME="Mero Expense Tracker"
APP_ENV=production
APP_DEBUG=false
APP_KEY=                    # run: php artisan key:generate
APP_URL=https://your-domain.com

LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_strong_password

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true
SESSION_SAME_SITE=lax

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# AI Scan (optional)
GEMINI_API_KEY=
OPENAI_API_KEY=
AI_SCAN_PROVIDER=auto
AI_SCAN_MODEL=gemini-2.5-flash
```

**Never** set `APP_DEBUG=true` on a public server.

## 3. Deploy commands

Run from the project root on the server (or in your CI/CD pipeline):

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build

php artisan key:generate          # first deploy only
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

After code updates:

```bash
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 4. File permissions

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

Replace `www-data` with your PHP-FPM user if different.

## 5. Web server

- Point the site **only** at the `public/` folder.
- Force HTTPS (redirect HTTP → HTTPS).
- Block direct access to `.env`, `vendor/`, `storage/` (except `public/storage` via symlink).

**Nginx** (minimal):

```nginx
root /var/www/expensetracker/public;
index index.php;
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
location ~ \.php$ {
    fastcgi_pass unix:/run/php/php8.4-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## 6. Security checklist

- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] Unique `APP_KEY` on this server
- [ ] Strong database password; dedicated DB user (not root)
- [ ] HTTPS + `SESSION_SECURE_COOKIE=true`
- [ ] `composer audit` shows no vulnerabilities
- [ ] `.env` is **not** in git and not web-accessible
- [ ] Change default admin password after first login
- [ ] AI API keys set with usage/billing limits in provider console
- [ ] Queue/cron if you add scheduled jobs later: `* * * * * php /path/artisan schedule:run`

## 7. First login

1. Open `https://your-domain.com/admin` and log in as admin.
2. Change the admin password (Filament profile).
3. In **User management**, approve users and set membership as needed.
4. In **Company & branding** / **Frontend pages**, update site content.
5. Optional: set **Currency** tab under Company settings.

## 8. Verify after deploy

| Check | URL / action |
|-------|----------------|
| Home page loads | `/` |
| Login works | `/login` |
| User dashboard (approved user) | `/dashboard` |
| Admin panel | `/admin` |
| Upload receipt (if AI configured) | `/ai-scan` |
| `php artisan about` shows `Environment: production` | SSH |

## 9. Backups

- Database: daily automated dump
- Files: `storage/app` (avatars, receipts, company uploads)

## 10. cPanel (shared hosting)

This app uses **Vite** for CSS/JS. Production pages load files from `public/build/` (not from `resources/css/`).

### Recommended layout

1. Upload the **whole project** to e.g. `/home/username/expensetracker` (outside `public_html`).
2. In cPanel → **Domains** → your domain → set **Document Root** to:
   `expensetracker/public`
3. Do **not** point the domain at the project root unless you also upload the root `.htaccess` (included in this repo) that forwards requests to `public/`.

### Build assets before upload

On your PC (or in CI), from the project root:

```bash
npm ci
npm run build
```

Then upload **`public/build/`** to the server (entire folder, including `manifest.json` and `assets/`).

This repo tracks `public/build` in git so a normal `git pull` deploy includes CSS/JS. After you change styles or JS, run `npm run build` again and redeploy `public/build`.

### cPanel checklist (CSS not loading)

| Step | What to do |
|------|------------|
| 1 | Confirm `public/build/manifest.json` exists on the server |
| 2 | Confirm `public/build/assets/*.css` exists (same hashes as in `manifest.json`) |
| 3 | Delete **`public/hot`** if it exists (leftover from `npm run dev` — breaks production) |
| 4 | Document root must be **`public/`**, not the Laravel root |
| 5 | Set `APP_URL=https://your-actual-domain.com` (HTTPS, no trailing slash) |
| 6 | Subfolder install: also set `ASSET_URL="${APP_URL}"` in `.env`, then `php artisan config:cache` |
| 7 | Run `php artisan config:cache` after changing `.env` |

### Test asset URLs

In the browser, open DevTools → Network. Reload the home page. You should see requests like:

`https://your-domain.com/build/assets/app-….css` → status **200**

If you see **404** on `/build/...`, the folder was not uploaded or the web server is not serving `public/` correctly.

### If you cannot run Node on the server

Build locally, then upload only:

- `public/build/manifest.json`
- `public/build/assets/` (all files inside)

No `npm` is required on cPanel if you deploy those files.

### Alternative: Laravel app above `public_html`

If your host only allows `public_html` as the web root:

1. Put the Laravel project in `/home/username/expensetracker`.
2. Copy **contents** of `expensetracker/public/` into `public_html/` (including `build/`, `.htaccess`, `index.php`).
3. Edit `public_html/index.php` so paths point to the app folder:

```php
require __DIR__.'/../expensetracker/vendor/autoload.php';
$app = require_once __DIR__.'/../expensetracker/bootstrap/app.php';
```

(Adjust `../expensetracker` to match your folder name.)

## 11. Troubleshooting

| Issue | Fix |
|-------|-----|
| 500 error | `storage/logs/laravel.log`, check permissions, run `php artisan config:clear` |
| CSS/JS missing | See **§10 cPanel**; run `npm run build`, upload `public/build`, delete `public/hot` |
| CSS/JS load `127.0.0.1:5173` or `[::1]:5173` | Delete **`public/hot`** on server; run `php artisan assets:check --fix` |
| CSS 404 but HTML works | Wrong `APP_URL` / `ASSET_URL`; wrong document root; missing `public/build` |
| Desktop OK, mobile layout broken | Rebuild with `npm run build` and redeploy `public/build` (fixes legacy `@media` for older phones) |
| Images 404 | `php artisan storage:link` |
| Session issues behind proxy | Set trusted proxies; `APP_URL` must match HTTPS domain |
| AI Scan disabled | Set `GEMINI_API_KEY` or `OPENAI_API_KEY`, `php artisan config:clear` |

---

See also: [README.md](README.md) for local development and demo accounts.
