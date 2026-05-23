# Railway Deployment Guide

This is the recommended path if you want a real HTTPS portfolio demo quickly.

Why Railway is the best fit for this repository:

- this app needs a web service, queue worker, scheduler, and SQL database
- Railway supports all of those in one project
- Railway provides a public `*.up.railway.app` domain with automatic SSL
- Railway offers a managed MySQL service, which fits this codebase better than PostgreSQL

## What You Will Deploy

Create four Railway services in one project:

1. MySQL database
2. App service
3. Worker service
4. Cron service

<<<<<<< HEAD
=======
For the best performance, add a fifth optional service:

5. Redis

>>>>>>> 1e9f232bbdeac9084abc1815f7f1e7cc8a564a74
The app service is the only one that gets a public HTTPS domain.

## Files Added For Railway

- `railway/bootstrap-runtime.sh`
- `railway/init-app.sh`
- `railway/run-web.sh`
- `railway/run-worker.sh`
- `railway/run-cron.sh`
- `railway/seed-demo.sh`

## Before You Start

Make sure the branch that contains these Railway files is pushed to GitHub.

Your repo remote is:

```text
https://github.com/KyawMyoHtay1/academic-portal.git
```

If you use the current local `deployment` branch, push it first:

```bash
git push -u origin deployment
```

## 1. Create A Railway Project

1. Sign in to Railway with GitHub.
2. Create a new empty project.
3. Add a MySQL database service.

Railway exposes these MySQL variables from that service:

- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLDATABASE`
- `MYSQL_URL`

## 2. Create The App Service

1. Add a new service from your GitHub repo.
2. Choose the branch that contains these Railway files.
3. Open the app service settings.
4. Set the custom start command to:

```bash
/bin/sh -lc "chmod +x ./railway/*.sh && ./railway/run-web.sh"
```

5. Set the pre-deploy command to:

```bash
/bin/sh -lc "chmod +x ./railway/*.sh && ./railway/init-app.sh"
```

6. Set the healthcheck path to:

```text
/up
```

## 3. Create The Worker Service

1. Add another service from the same repo and branch.
2. Set the custom start command to:

```bash
/bin/sh -lc "chmod +x ./railway/*.sh && ./railway/run-worker.sh"
```

This service should not have a public domain.

## 4. Create The Cron Service

1. Add another service from the same repo and branch.
2. Configure it as a cron job.
3. Set the schedule to:

```text
* * * * *
```

4. Set the custom start command to:

```bash
/bin/sh -lc "chmod +x ./railway/*.sh && ./railway/run-cron.sh"
```

This runs Laravel's scheduler every minute, which is the normal production setup.

## 5. Add A Persistent Volume For Uploads

Your app stores uploaded files in Laravel's `storage` directory, so attach a Railway Volume to the app service.

Use this mount path:

```text
/var/www/html/storage
```

Without a volume, uploaded files will be lost on redeploy.

## 6. Set App Variables

In the app service, add these environment variables.

Use Railway reference variables for the database values:

```env
APP_NAME=University Academic Portal
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-domain.up.railway.app

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_PATH=/
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
BROADCAST_CONNECTION=log

LOG_CHANNEL=stderr
LOG_LEVEL=info

MAIL_MAILER=log
ATTENDANCE_LOW_THRESHOLD=75
ATTENDANCE_ALERT_COOLDOWN_DAYS=7
VITE_APP_NAME="${APP_NAME}"
VITE_DISABLE_HTML5_VALIDATION=false
```

Generate `APP_KEY` with Node.js:

```bash
node -e "console.log('base64:'+require('crypto').randomBytes(32).toString('base64'))"
```

Then add the output as:

```env
APP_KEY=base64:...
```

Copy the same non-domain variables to the worker and cron services.

The worker and cron services need the same database and Laravel configuration as the app service.

<<<<<<< HEAD
=======
## 6A. Best Performance With Redis

If you want the fastest production setup, add a Railway Redis service and then set these variables on the app, worker, and cron services:

```env
REDIS_URL=${{Redis.REDIS_URL}}
SESSION_DRIVER=redis
SESSION_CONNECTION=default
SESSION_STORE=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_QUEUE_CONNECTION=default
REDIS_QUEUE=default
REDIS_CACHE_CONNECTION=cache
REDIS_CACHE_LOCK_CONNECTION=default
```

This reduces database load from sessions, cache, and queued jobs.

>>>>>>> 1e9f232bbdeac9084abc1815f7f1e7cc8a564a74
## 7. Generate The Public HTTPS Link

In the app service:

1. Open `Settings`
2. Go to `Networking`
3. Click `Generate Domain`

Railway will create a public URL like:

```text
https://your-app-name-production-xxxx.up.railway.app
```

That is your live portfolio link.

After the domain is generated, update:

```env
APP_URL=https://your-app-name-production-xxxx.up.railway.app
```

and redeploy the app service.

## 8. Seed Demo Data Once

If you want the portfolio demo to include sample users and records, run this one time in the app service shell:

```bash
/bin/sh -lc "chmod +x ./railway/*.sh && ./railway/seed-demo.sh"
```

Only do this for a demo environment where sample data is wanted.

## 9. Recommended Portfolio Settings

For a public demo, I recommend:

- keep `MAIL_MAILER=log` unless you want real email
- keep `BROADCAST_CONNECTION=log` unless you need live websockets
- leave Stripe disabled unless you want to demonstrate real checkout
- seed demo data once so reviewers can explore the system

## 10. What Your Final Link Will Look Like

The app link will not exist until Railway creates it, but once deployed it will be:

```text
https://<generated-name>.up.railway.app
```

If you later connect your own custom domain, Railway also supports automatic SSL for that domain.
<<<<<<< HEAD

=======
>>>>>>> 1e9f232bbdeac9084abc1815f7f1e7cc8a564a74
