# Deployment Guide

For the quickest live portfolio demo with a real HTTPS URL, use the Railway-specific guide in [RAILWAY_DEPLOYMENT.md](RAILWAY_DEPLOYMENT.md).

This repository now includes a Docker-based production setup for the Laravel + Vue/Inertia app.

It is built around the way this project already works in code:

- one web process for the app
- one queue worker for notifications and background jobs
- one scheduler process for daily tasks like low-attendance alerts
- MySQL for the application database, sessions, cache, and queued jobs

## Why MySQL Instead of SQLite

SQLite is fine for local development, but this app stores sessions, cache, and queue jobs in the database by default. In production that means concurrent reads and writes from the web app, queue worker, and scheduler. MySQL or MariaDB is the safer production choice.

## Files Added

- `Dockerfile`
- `docker-compose.production.yml`
- `.env.production.example`
- `docker/entrypoint.sh`
- `docker/php/production.ini`

## Prerequisites

- Docker Engine with the Docker Compose plugin
- A server or VM that can expose HTTP traffic
- A real HTTPS domain before enabling Stripe or reCAPTCHA in production

## 1. Prepare Environment Variables

Copy the production example file:

```bash
cp .env.production.example .env
```

Update these values before you start the stack:

- `APP_URL` to your real public URL
- `DB_PASSWORD`
- `DB_ROOT_PASSWORD`
- `MAIL_*` if you want real email delivery
- `STRIPE_*` if you want online payments
- `RECAPTCHA_*` before opening public forms to real users

Generate an application key from the Docker image:

```bash
docker compose -f docker-compose.production.yml build
docker compose -f docker-compose.production.yml run --rm --no-deps app php artisan key:generate --show
```

Paste the printed value into `APP_KEY=` in your `.env`.

## 2. Start the Stack

```bash
docker compose -f docker-compose.production.yml up -d
```

This starts:

- `app` on port `80`
- `queue` for queued jobs
- `scheduler` for scheduled commands
- `db` for MySQL

## 3. Run Migrations

Run database migrations after the containers are up:

```bash
docker compose -f docker-compose.production.yml exec app php artisan migrate --force
```

If you want demo/sample data in a staging environment, use:

```bash
docker compose -f docker-compose.production.yml exec app php artisan db:seed
```

For a public production deployment, seed only if you intentionally want the demo accounts and sample content.

## 4. Verify the Deployment

Check service status:

```bash
docker compose -f docker-compose.production.yml ps
```

Stream logs:

```bash
docker compose -f docker-compose.production.yml logs -f app
docker compose -f docker-compose.production.yml logs -f queue
```

Laravel exposes a health endpoint at:

```text
/up
```

So once the server is reachable, test:

```text
https://your-domain.example/up
```

## 5. Deploy Updates

For later releases:

```bash
git pull
docker compose -f docker-compose.production.yml up -d --build
docker compose -f docker-compose.production.yml exec app php artisan migrate --force
```

## 6. Important Production Notes

- `APP_URL` matters for Stripe success/cancel URLs and generated file URLs.
- `public/storage` is created automatically by the container entrypoint.
- Uploaded files persist inside the shared `laravel-storage` Docker volume.
- Reverb is disabled by default in this deployment setup. The app will still work because `BROADCAST_CONNECTION=log`.
- If you want live WebSocket messaging, add a dedicated Reverb or Pusher setup later.
- If you use an external managed MySQL database, remove the `db` service from `docker-compose.production.yml` and point the `DB_*` variables at your managed instance.

## 7. HTTPS, Stripe, and reCAPTCHA

Use HTTPS before enabling these features publicly:

- Stripe checkout/webhooks expect your public URLs to be correct
- reCAPTCHA keys should match the real domain
- secure session cookies are enabled in the production example

Stripe webhook endpoint:

```text
https://your-domain.example/stripe/webhook
```

## 8. First Commands I Recommend On The Server

```bash
cp .env.production.example .env
docker compose -f docker-compose.production.yml build
docker compose -f docker-compose.production.yml run --rm --no-deps app php artisan key:generate --show
# paste the key into .env
docker compose -f docker-compose.production.yml up -d
docker compose -f docker-compose.production.yml exec app php artisan migrate --force
```
