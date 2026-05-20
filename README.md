# Mero Expense Tracker

A multi-auth income and expense tracking web application built with **Laravel 13**, **Filament 5** (admin panel), **Laravel Breeze** (user auth), **Bootstrap 5**, and **Tailwind CSS** (Tailwind UI–style components). Includes **light/dark mode** and a fully **mobile-responsive** layout.

## Features

- **User panel** (`/dashboard`): dashboard with stats, transactions CRUD, custom categories, profile
- **Admin panel** (`/admin`): approve users (membership fee + expiry), contact form inbox, review moderation
- **Multi-auth**: role-based access (`admin` vs `user`) on a single `users` table
- **User approval & membership**: new users wait for admin approval; active membership required for the dashboard
- **Public site privacy**: no user financial data on marketing pages; only admin-approved reviews are shown
- **Light / dark theme**: persisted in `localStorage`, works on landing, auth, and user pages
- **Filament dark mode** enabled on the admin panel

## Requirements

- PHP 8.4+
- Composer
- Node.js 18+
- SQLite (default) or MySQL/PostgreSQL

## Installation

```bash
composer install
cp .env.example .env   # if needed
php artisan key:generate
npm install
npm run build
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Visit `http://127.0.0.1:8000`.

## Production deployment

See **[DEPLOYMENT.md](DEPLOYMENT.md)** for essential steps: production `.env`, migrate, build assets, HTTPS, permissions, and security checklist.

## Public pages

| Page | URL |
|------|-----|
| Home | `/` |
| Features | `/features` |
| Pricing | `/pricing` |
| About | `/about` |
| FAQ | `/faq` |
| Contact | `/contact` |
| Privacy | `/privacy` |
| Terms | `/terms` |

Login and register use the same site header and footer.

## Demo accounts

| Role  | Email                      | Password  |
|-------|----------------------------|-----------|
| Admin | admin@expensetracker.test  | password  |
| User  | user@expensetracker.test   | password  |

- **Admin** → log in at `/login`, redirected to `/admin` (Filament)
- **User** → approved demo account logs in at `/login`, redirected to `/dashboard`

New registrations receive the `user` role and stay on **Account pending** until an admin approves them and sets membership fee/expiry.

## Admin workflows

| Task | Where |
|------|--------|
| Approve user + set fee & expiry | Admin → **Users** → **Approve** |
| Revoke approval | Admin → **Users** → **Revoke** |
| Contact form messages | Admin → **Contact messages** |
| Approve reviews for homepage | Admin → **Review approvals** → **Approve** (submitted from homepage) |
| Reply to contact messages | Admin → **Contact inbox** → **Reply** (emails the visitor) |

Admin login: `/admin/login` (link in site footer).

## Tech stack

- Laravel 13 + Breeze (Blade)
- Filament 5 admin panel
- Bootstrap 5 (grid, navbar, tables, alerts)
- Tailwind CSS 3 + `@tailwindcss/forms` (utility styling, cards, dark mode)
- Alpine.js
- SQLite database (default)

## Project structure

```
app/
  Enums/           UserRole, TransactionType, CategoryType
  Filament/        Admin resources & widgets
  Http/Controllers User-facing CRUD
  Models/          User, Category, Transaction
resources/views/
  dashboard/       User dashboard
  transactions/    User transaction views
  categories/      User category views
  components/      Theme toggle, user layout
```

## Development

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

## License

MIT
