# University Academic Portal

University Academic Portal is a Laravel 12 application for managing academic operations across three main roles: `student`, `teacher`, and `staff`.

It combines:

- public guest-facing pages
- an authenticated Inertia.js + Vue 3 application
- academic workflows for enrollment, grades, fees, attendance, assignments, announcements, messaging, and timetables
- queue-backed notifications and scheduled background tasks
- seeded demo data and project documentation assets such as UML diagrams and report chapters

## Features

### Public

- home page and public information pages
- public course and announcement/news pages
- contact and feedback forms
- guest search
- user manual preview and download

### Student

- registration, login, email verification, and profile management
- course browsing and enrollment requests
- timetable, attendance, grades, and fee views
- assignment submission
- announcements, notifications, and internal messaging
- personal dashboard and settings

### Teacher

- assigned course and subject views
- attendance recording
- grade entry and final-grade submission
- assignment management and grading
- timetable access
- teacher announcements
- teacher dashboard

### Staff

- management of students, users, courses, subjects, and timetables
- enrollment review and withdrawal approval
- fee management and payment review
- grade review and approval
- attendance reporting and export
- manual low-attendance alert dispatch
- announcement management and reminder sending
- contact/feedback inbox management
- failed-jobs review
- staff dashboard and attendance-alert settings

## Stack

- Backend: Laravel 12, PHP 8.2+
- Frontend: Vue 3, Inertia.js, Vite, Tailwind CSS
- Database: SQLite by default in `.env.example`, MySQL/MariaDB also supported
- Queue/session/cache: database-backed by default
- Charts: Chart.js + `vue-chartjs`
- PDF export: `barryvdh/laravel-dompdf`
- Image handling: `intervention/image`
- Payments: Stripe Checkout + webhook handling
- Realtime: Laravel Reverb / Echo when configured

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer 2+
- Node.js 20+
- npm 10+
- SQLite or MySQL

### Quick Start

1. Install dependencies:

```bash
composer install
npm install
```

2. Create environment file:

```bash
cp .env.example .env
```

PowerShell:

```powershell
Copy-Item .env.example .env
```

3. Generate the application key:

```bash
php artisan key:generate
```

4. If you are using SQLite, create the database file:

```bash
touch database/database.sqlite
```

PowerShell:

```powershell
New-Item -ItemType File database/database.sqlite -Force
```

5. Run migrations and seed demo data:

```bash
php artisan migrate --seed
```

6. Create the storage symlink:

```bash
php artisan storage:link
```

7. Start the frontend dev server:

```bash
npm run dev
```

8. Start Laravel:

```bash
php artisan serve
```

9. Start the queue worker in another terminal:

```bash
php artisan queue:work
```

10. Start the scheduler in another terminal if you want scheduled jobs to run locally:

```bash
php artisan schedule:work
```

## Demo Accounts

Password for seeded demo users:

```text
Password123!
```

Examples:

- Staff: `alice.staff@example.com`
- Staff: `brian.staff@example.com`
- Teacher: `amelia.teacher@example.com`
- Teacher: `ben.teacher@example.com`
- Teacher: `chloe.teacher@example.com`
- Students: `student01@example.com` to `student18@example.com`

## Queue and Mail Notes

This project uses queue-backed behavior for background work and for selected notifications.

Important points:

- keep `php artisan queue:work` running when testing queued notifications
- low-attendance alerts can run automatically from the scheduler or manually from the staff attendance report
- configure a real mailer if you want actual email delivery
- if mail is not configured correctly, database notifications may still work while email does not

### SMTP example

If you want real email delivery, set mail variables in `.env`.

Minimal SMTP example:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_app_password
MAIL_FROM_ADDRESS=your_email@example.com
MAIL_FROM_NAME="${APP_NAME}"
MAIL_TIMEOUT=10
```

Notes:

- Laravel defaults to the `log` mailer if `MAIL_MAILER` is not set
- Gmail requires an app password, not your normal account password
- Gmail SMTP is acceptable for local testing, but not ideal for bulk reminder delivery
- after changing mail settings, clear config and cache:

```bash
php artisan config:clear
php artisan cache:clear
```

Manual low-attendance console dispatch:

```bash
php artisan attendance:send-low-attendance-alerts
```

## Useful Commands

Run tests:

```bash
php artisan test
```

Reset and reseed the database:

```bash
php artisan migrate:fresh --seed
```

Check failed jobs:

```bash
php artisan queue:failed
```

Retry failed jobs:

```bash
php artisan queue:retry all
```

Clear config and cache:

```bash
php artisan config:clear
php artisan cache:clear
```

## Project Structure

```text
app/                Application logic
config/             Laravel configuration
database/           Migrations, factories, seeders
docs/               Report/documentation chapters
class_diagram/      PlantUML class diagrams and ERD
sequence/           PlantUML sequence diagrams
usecase/            PlantUML use case diagrams
deployment/         Deployment diagram
resources/          Blade, Vue, CSS, frontend assets
routes/             Web and console routes
tests/              Feature and unit tests
```

## Documentation Assets

This repository also contains project-report assets, including:

- UML diagrams under `usecase/`, `class_diagram/`, `sequence/`, `deployment/`, and `sitemap/`
- report/dissertation chapter drafts under `docs/`
- a program demonstration video recording script at `docs/PROGRAM_DEMONSTRATION_VIDEO_RECORDING_SCRIPT.md`

## Troubleshooting

### Emails are not sending

- check your mail settings in `.env`
- make sure `php artisan queue:work` is running
- clear config after changing mail settings
- inspect `storage/logs/laravel.log`

### Low-attendance alerts are not processing

- make sure attendance data exists
- make sure the queue worker is running
- make sure the selected students are below the configured threshold

### Config changes are not taking effect

```bash
php artisan config:clear
php artisan cache:clear
```
