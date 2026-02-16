# University Academic Portal

University Academic Portal is a Laravel + Inertia (Vue) application for managing academic operations across student, teacher, and staff roles.

## Features

- Role-based dashboards (student, teacher, staff)
- Course enrollment and withdrawal request workflow
- Fee management and Stripe payment processing
- Grade submission and staff review/approval workflow
- Attendance tracking and low-attendance alerts
- Announcements, notifications, and internal messaging
- Public guest pages (courses, news, contact, support)

## Tech Stack

- Backend: Laravel 12, PHP 8.2+
- Frontend: Vue 3, Inertia.js, Vite, Tailwind CSS
- Database: MySQL (primary), SQLite (tests)
- Payments: Stripe Checkout + Webhooks

## Prerequisites

- PHP 8.2+
- Composer 2+
- Node.js 20+
- npm 10+
- MySQL 8+ (or compatible)

## Local Setup

1. Install dependencies:

```bash
composer install
npm ci
```

2. Configure environment:

```bash
cp .env.example .env
php artisan key:generate
```

3. Update `.env` database and service keys.

4. Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
```

5. Start development services:

```bash
composer run dev
```

This runs Laravel server, queue worker, logs, and Vite dev server concurrently.

## Environment Variables

Core:

- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `QUEUE_CONNECTION`, `SESSION_DRIVER`, `CACHE_STORE`

Stripe:

- `STRIPE_KEY`
- `STRIPE_SECRET`
- `STRIPE_WEBHOOK_SECRET`

reCAPTCHA:

- `RECAPTCHA_SITE_KEY`
- `RECAPTCHA_SECRET_KEY`
- `RECAPTCHA_SCORE_THRESHOLD`

Attendance alerts:

- `ATTENDANCE_LOW_THRESHOLD`
- `ATTENDANCE_ALERT_COOLDOWN_DAYS`

## Testing and Quality

Run backend tests:

```bash
php artisan test
```

Run code style checks:

```bash
./vendor/bin/pint --test
```

Build frontend assets:

```bash
npm run build
```

## CI

GitHub Actions workflow: `.github/workflows/ci.yml`

On every push and pull request, CI runs:

- `./vendor/bin/pint --test`
- `php artisan test`
- `npm run build`

## Payment Webhooks (Stripe)

Local testing example:

```bash
stripe listen --forward-to http://127.0.0.1:8000/stripe/webhook
```

Webhook handler is idempotent and ignores duplicate event deliveries.

## Queue and Scheduler

Low-attendance alerts are scheduled daily in `bootstrap/app.php`.

Production requirements:

- Queue worker: `php artisan queue:work`
- Scheduler trigger (every minute): `php artisan schedule:run`

## Demo Workflow

1. Staff creates courses, subjects, and fees.
2. Student registers, enrolls in courses, and views fees/grades.
3. Teacher records grades for assigned subjects.
4. Staff reviews and approves/rejects grades.
5. Student pays fees via Stripe checkout and receives status updates.

## Security and Hardening

- Role middleware and resource policies for fees/grades/enrollment actions
- Throttling on search, messaging, and public form endpoints
- Security headers middleware (CSP, X-Frame-Options, X-Content-Type-Options, HSTS in production)
- Structured logging for payments, enrollment actions, grade review decisions, and queue job failures

## Useful Commands

```bash
php artisan migrate:fresh --seed
php artisan queue:work
php artisan schedule:work
npm run dev
```
