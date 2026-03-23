# University Academic Portal

University Academic Portal is a hybrid Laravel 12 application for managing academic operations across three authenticated roles: `student`, `teacher`, and `staff`.

The project mixes:

- Blade-rendered public pages for guest-facing content
- Inertia.js + Vue 3 for the authenticated application
- Database-backed workflows for enrollment, fees, grades, attendance, messaging, and timetables
- Optional realtime updates through Laravel Reverb

It is designed as a full academic operations portal rather than a single dashboard page. The codebase includes public marketing/support pages, internal back-office tools, role-based dashboards, seeded demo data, automated tests, and system-analysis artifacts such as class diagrams, sequence diagrams, use cases, and deployment diagrams.

## Contents

- [What The Portal Supports](#what-the-portal-supports)
- [Architecture And Stack](#architecture-and-stack)
- [Key Workflow Lifecycles](#key-workflow-lifecycles)
- [Repository Layout](#repository-layout)
- [Getting Started](#getting-started)
- [Demo Data](#demo-data)
- [Running The App In Development](#running-the-app-in-development)
- [Configuration Reference](#configuration-reference)
- [Reports And Exports](#reports-and-exports)
- [Testing And Quality](#testing-and-quality)
- [Project Documentation Assets](#project-documentation-assets)
- [Troubleshooting](#troubleshooting)

## What The Portal Supports

### Public and guest features

- Marketing-style public home page with live aggregate statistics
- Public course catalog and course detail pages
- Public news/announcements listing with publish and expiry logic
- About, vision, services, support, contact, feedback, policies, and user-manual pages
- Contact and feedback submission flows
- Optional Google reCAPTCHA v3 protection for public forms
- Global search endpoint available to guests and authenticated users

### Student features

- Registration, login, email verification, profile management, and password updates
- Student profile self-service, including photo updates
- Course browsing and enrollment requests
- Withdrawal requests for already approved enrollments
- "My Courses" view of the student's approved and pending academic load
- Read-only grade view
- Attendance report view with CSV/PDF export
- Assignment list, submission flow, and submission download
- Fee ledger, payment submission, Stripe checkout, payment success/cancel handling
- Timetable view with CSV/PDF export
- Notifications and internal messaging
- Personalized dashboard metrics and charts

### Teacher features

- View assigned courses and subjects
- Mark attendance by subject
- Record grades for assigned subjects
- Submit final grades for staff review
- Manage assignment lifecycle: create, edit, publish, close, delete
- Grade assignment submissions and download all submissions
- View and export timetable
- Create and manage teacher announcements
- Teacher dashboard with pending/approved/rejected grade trends

### Staff features

- Student CRUD, filters, document handling, and photo management
- User management and role assignment
- Course CRUD and teacher assignment
- Subject CRUD and teacher assignment
- Enrollment approval/rejection and withdrawal approval/rejection
- Fee creation, payment review, reminder sending, receipts, and ledger export
- Grade review and approval/rejection workflow
- Timetable management and export
- Announcement management and reminder sending
- Attendance report generation and export
- Manual trigger for low-attendance alerts
- Contact/feedback inbox management
- Failed job inspection, retry, retry-all, and flush operations
- Staff dashboard with queue health and operational counters

## Architecture and Stack

### Core stack

- Backend: Laravel 12, PHP 8.2+
- Auth/UI transport: Laravel Breeze + Inertia.js
- Frontend: Vue 3, Vite 7, Tailwind CSS
- Database: SQLite by default in `.env.example`; MySQL-compatible setup is also supported
- Queue: database driver by default
- Cache/session: database-backed in local defaults
- Payments: Stripe Checkout + webhook processing
- Realtime: Laravel Reverb + Laravel Echo + Pusher JS client
- PDF generation: `barryvdh/laravel-dompdf`
- Images: `intervention/image`
- Routing model: Blade for guest pages, Inertia/Vue for authenticated pages

### Application shape

- `User` is the primary auth model and must verify email
- `User.role` drives access control with three active roles: `student`, `teacher`, `staff`
- `Student` extends the authenticated user with academic and demographic profile data
- Courses contain subjects
- Teachers are assigned to courses and subjects
- Students enroll in courses through the `course_student` pivot, modeled as `Enrollment`
- Grades, attendance, assignments, timetables, fees, announcements, messages, and notifications are separate feature modules

### Security and access control

- Role middleware restricts student, teacher, and staff sections
- Policies are implemented for sensitive workflows such as grades, fees, and courses
- Search, messaging, and public forms are throttled
- Security headers middleware adds CSP/X-Frame-Options/X-Content-Type-Options and production HSTS behavior
- Stripe webhook endpoint is intentionally excluded from CSRF protection
- reCAPTCHA is conditional: if keys are blank, local development continues without it

### Realtime behavior

- Messaging supports broadcast events over private channels like `messages.{userId}`
- Frontend Echo bootstrap is enabled only when `VITE_REVERB_APP_KEY` is present
- Broadcasting falls back to the `log` driver if Reverb/Pusher dependencies or credentials are not configured
- In other words: messaging still works without websockets, but live in-browser updates require Reverb to be configured and running

## Key Workflow Lifecycles

### Enrollment lifecycle

- Initial request: `pending`
- Staff approval: `approved`
- Staff rejection: `rejected`
- Student withdrawal request from an approved enrollment: `withdrawal_pending`
- Staff approves withdrawal: enrollment row is removed
- Staff rejects withdrawal: enrollment returns to `approved`

Important implementation detail:

- Enrollment approval checks for timetable conflicts against already approved courses
- Re-submitting a previously rejected enrollment is supported
- Duplicate pending requests and duplicate approved enrollments are blocked

### Grade lifecycle

- Teacher working state: `draft`
- Teacher submission for review: `pending`
- Staff acceptance: `approved`
- Staff rejection: `rejected`

Additional behavior:

- Grade review actions create review logs
- Letter grades are computed from numeric scores using the built-in scale:
  - `A`: 80-100
  - `B`: 70-79
  - `C`: 60-69
  - `D`: 50-59
  - `E`: 40-49
  - `F`: 0-39

### Fee lifecycle

- Not yet acted on: `pending`
- Student started Stripe checkout or staff is reviewing payment: `payment_pending`
- Confirmed paid: `paid`
- Failed or cancelled payment: `failed`

Additional behavior:

- Stripe webhook deliveries are recorded in `stripe_webhook_events`
- Duplicate webhook deliveries are ignored safely
- Success and failure handlers are guarded against double-processing and payment-intent mismatches

### Assignment lifecycle

- Teacher draft: `draft`
- Visible and submittable by students: `published`
- Past due or manually closed: `closed`

Assignments support:

- Allowed file type restrictions
- Max file size restrictions
- Submission grading
- Download-all submissions flow for teachers

### Announcements and alerts

- Announcements support:
  - `priority`
  - `pinned`
  - `require_ack`
  - role-based `audience`
  - `publish_at`
  - `expires_at`
- Low-attendance alerts are queued notifications based on:
  - `ATTENDANCE_LOW_THRESHOLD`
  - `ATTENDANCE_ALERT_COOLDOWN_DAYS`

## Repository Layout

```text
app/                    Core Laravel application code
app/Http/Controllers/   Feature controllers for student, teacher, staff, and public flows
app/Jobs/               Queued jobs such as low-attendance alerts
app/Models/             Domain models
app/Notifications/      Notification classes for portal events
app/Services/           Workflow services (payments, enrollment, dashboard builders, reports)
bootstrap/              App bootstrap and scheduler configuration
config/                 Framework and feature configuration
database/migrations/    Schema evolution
database/seeders/       Demo and sample data seeders
docs/                   Written project chapters and supporting documentation
resources/js/           Vue pages, layouts, components, and composables
resources/views/        Blade views, especially public/guest pages and PDF templates
routes/                 Web, auth, console, and broadcast channel routes
tests/                  Feature and unit tests
public/                 Public assets, images, videos, docs, favicon
class_diagram/          PlantUML class diagrams
deployment/             Deployment diagram source
sequence/               PlantUML sequence diagrams
sitemap/                Site map diagrams
usecase/                Use case diagrams
out/                    Rendered diagram outputs
fox-master/             Static template/theme assets used by public pages
```

## Getting Started

### Prerequisites

- PHP 8.2 or newer
- Composer 2 or newer
- Node.js 20 or newer
- npm 10 or newer
- SQLite or MySQL

### Quick start with the default SQLite setup

The repository already includes `database/database.sqlite`, and `.env.example` is configured for SQLite by default. That makes SQLite the fastest first-run option.

1. Install backend dependencies:

```bash
composer install
```

2. Install frontend dependencies:

```bash
npm ci
```

3. Create your environment file:

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

macOS / Linux:

```bash
cp .env.example .env
```

4. Generate the Laravel app key:

```bash
php artisan key:generate
```

5. Create the public storage symlink for uploaded files:

```bash
php artisan storage:link
```

6. Run migrations and seed the demo dataset:

```bash
php artisan migrate --seed
```

7. Start the usual development stack:

```bash
composer run dev
```

What `composer run dev` starts:

- `php artisan serve`
- `php artisan queue:listen --tries=1`
- `php artisan pail --timeout=0`
- `npm run dev`

Open the app at `http://127.0.0.1:8000`.

### MySQL setup instead of SQLite

If you want MySQL locally, update these values in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=academic_portal
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then run:

```bash
php artisan migrate --seed
```

### First-run notes

- Queue-backed features depend on the `jobs` table migrations already included in this repo
- Uploaded images are served from the `public` disk, so `php artisan storage:link` matters
- Public/contact/auth reCAPTCHA checks are disabled automatically if you leave the reCAPTCHA keys blank in local development

## Demo Data

The default `DatabaseSeeder` runs `ComprehensiveDemoSeeder`, which prepares a broad dataset for end-to-end testing.

### Seeded accounts

All seeded users share the same password:

```text
Password123!
```

Staff:

- `alice.staff@example.com`
- `brian.staff@example.com`

Teachers:

- `amelia.teacher@example.com`
- `ben.teacher@example.com`
- `chloe.teacher@example.com`

Students:

- `student01@example.com` through `student18@example.com`

### Seeded dataset includes

- 5 courses
- 3 subjects per course
- teacher-course and teacher-subject assignments
- 18 student users and linked student profiles
- approved, pending, rejected, and withdrawal-pending enrollments
- timetables across weekdays and time slots
- attendance records with low-attendance examples
- assignments in draft, published, and closed states
- submissions and grading samples
- grades in multiple review states
- fees in pending, payment-pending, paid, and failed states
- announcements with pinned, required-ack, future, and expired examples
- internal messages
- public contact and feedback inbox samples

### Seeder note about images

The demo seeder tries to download seed photos from Pexels for courses, subjects, users, and students. If the image host is unreachable, the seeder skips those downloads and continues successfully. That behavior is intentional.

## Running the App in Development

### Usual development command

```bash
composer run dev
```

This is enough for most UI and backend work because it runs:

- Laravel dev server
- database queue listener
- Laravel Pail log tailing
- Vite dev server

### Scheduler

Attendance alerts are scheduled daily in `bootstrap/app.php`.

To exercise scheduled behavior locally, run one of these in a separate terminal:

```bash
php artisan schedule:work
```

or:

```bash
php artisan schedule:run
```

### Queue workers

The default environment uses `QUEUE_CONNECTION=database`.

If you are not using `composer run dev`, start a worker manually:

```bash
php artisan queue:work
```

### Realtime with Laravel Reverb

Realtime updates are optional. The repo ships with Reverb dependencies and frontend Echo bootstrap, but the default `.env.example` keeps broadcasting on the `log` driver.

To enable realtime messaging:

1. Set `BROADCAST_CONNECTION=reverb`
2. Ensure `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`, `REVERB_HOST`, `REVERB_PORT`, and `REVERB_SCHEME` are set
3. Ensure matching `VITE_REVERB_*` values are available to Vite
4. Start the Reverb server:

```bash
php artisan reverb:start
```

### Stripe webhook testing

To test payment webhooks locally, set Stripe credentials in `.env` and forward webhook events:

```bash
stripe listen --forward-to http://127.0.0.1:8000/stripe/webhook
```

You will need:

- `STRIPE_KEY`
- `STRIPE_SECRET`
- `STRIPE_WEBHOOK_SECRET`

### Useful development helpers

- Local-only email verification shortcut for the logged-in user:

```text
/dev/verify-email-now
```

- Manually dispatch low-attendance alerts:

```bash
php artisan attendance:send-low-attendance-alerts
```

- Generate missing small image thumbnails for table views:

```bash
php artisan images:backfill-table-thumbs
```

Force regeneration:

```bash
php artisan images:backfill-table-thumbs --force
```

Limit to selected folders:

```bash
php artisan images:backfill-table-thumbs --folder=users --folder=students
```

## Configuration Reference

### Core application

```env
APP_NAME=
APP_ENV=
APP_KEY=
APP_DEBUG=
APP_URL=
```

`APP_URL` matters for:

- storage asset URLs
- Stripe success/cancel redirects
- generated links and some notification URLs

### Database

Default local `.env.example`:

```env
DB_CONNECTION=sqlite
```

MySQL alternative:

```env
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

### Cache, queue, sessions, files

```env
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
FILESYSTEM_DISK=local
```

Notes:

- Jobs are persisted to the database
- Uploaded public media is stored under the `public` disk and exposed through `public/storage`
- Session and cache tables are already part of the migration set

### Attendance alerts

```env
ATTENDANCE_LOW_THRESHOLD=75
ATTENDANCE_ALERT_COOLDOWN_DAYS=7
```

Meaning:

- Students strictly below the threshold can receive an alert
- Re-alerting is rate-limited by the cooldown window

### Realtime / broadcasting

```env
BROADCAST_CONNECTION=log
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=
REVERB_PORT=
REVERB_SCHEME=
VITE_REVERB_APP_KEY=
VITE_REVERB_HOST=
VITE_REVERB_PORT=
VITE_REVERB_SCHEME=
```

Default behavior:

- `BROADCAST_CONNECTION=log` keeps local development simple
- switch to `reverb` only when you want live updates

### Stripe

```env
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
```

### Google reCAPTCHA v3

```env
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=
RECAPTCHA_SCORE_THRESHOLD=0.5
```

Behavior:

- If keys are set, registration, login, and guest public forms require tokens
- If keys are blank, those checks are skipped locally

### Mail

`.env.example` defaults to the `log` mailer, which is enough for local development:

```env
MAIL_MAILER=log
```

## Reports and Exports

The application supports both CSV and PDF exports in multiple modules.

### Student-facing exports

- Attendance report: CSV/PDF
- Timetable: CSV/PDF

### Staff-facing exports

- Enrollment list: CSV/PDF
- Attendance report: CSV/PDF
- Fee ledger: CSV/PDF
- Grade sheet by subject: CSV/PDF
- Timetables: CSV/PDF
- Fee receipt: PDF

Implementation detail:

- PDF output is rendered from Blade templates via DomPDF

## Testing and Quality

### Main commands

Run backend formatting and tests:

```bash
composer check
```

Run the full local gate on Windows:

```powershell
./check.ps1
```

Run the full local gate on macOS/Linux:

```bash
./check.sh
```

Run backend tests only:

```bash
php artisan test
```

Run frontend build validation:

```bash
npm run build
```

Run Pint only:

```bash
./vendor/bin/pint --test
```

### What the test suite covers

The repo includes feature and unit tests for:

- authentication and registration
- email verification and password flows
- terms and conditions access
- role-based route protection
- course, subject, and user management
- course teacher assignment
- enrollment approval and withdrawal workflows
- staff attendance reporting
- payment webhook processing
- grade review workflows
- teacher draft ownership protections
- failed job management
- search scoping
- low-attendance alert job behavior
- grade calculation and letter-grade mapping

### Test environment defaults

From `phpunit.xml`:

- app env: `testing`
- database: in-memory SQLite
- queue: `sync`
- cache/session/mail: array/null-style test-safe drivers
- broadcast connection: `null`

### Pre-commit hooks

Enable repository-managed Git hooks once per clone:

```bash
composer hooks:install
```

Check the active hooks path:

```bash
composer hooks:status
```

Current pre-commit checks:

- `composer check`
- `npm run check:frontend`

### CI

GitHub Actions workflow:

```text
.github/workflows/ci.yml
```

CI currently runs:

- dependency install
- environment preparation
- `./vendor/bin/pint --test`
- `php artisan test`
- `npm run build`

## Project Documentation Assets

This repository contains more than application code. It also includes project/report artifacts and diagrams.

### Written documents

- `docs/Chapter1.md`
- `docs/CHAPTER2_REWRITE.md`
- `docs/CHAPTER3_REWRITE.md`
- `docs/CHAPTER4_REWRITE.md`
- `docs/CHAPTER5_REWRITE.md`
- `docs/CHAPTER6_REWRITE.md`
- `docs/CHAPTER7_REWRITE.md`
- `docs/APPENDIX_REWRITE.md`

### Diagram sources

- `class_diagram/`
- `deployment/`
- `sequence/`
- `sitemap/`
- `usecase/`

### Rendered outputs

- `out/`

These folders are useful if you need project documentation, architecture diagrams, or supporting material for reports/presentations.

## Troubleshooting

### Uploaded images or documents are not visible

Run:

```bash
php artisan storage:link
```

### Messages save correctly but do not appear live

Check all of the following:

- `BROADCAST_CONNECTION` is set to `reverb`
- Reverb credentials are set
- `php artisan reverb:start` is running
- Vite was restarted after changing `VITE_REVERB_*` values

### Queue-backed features are not processing

Start a worker:

```bash
php artisan queue:work
```

This affects features such as:

- low-attendance alerts
- some notification flows
- failed job management visibility

### Stripe webhook signature verification fails

Make sure the local listener secret matches `.env`:

- `STRIPE_WEBHOOK_SECRET` must match the secret reported by `stripe listen`

### Public/auth forms are blocked by reCAPTCHA in local development

Either:

- leave `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` blank locally

or:

- configure both correctly and ensure the frontend is loading the right site key

### Seeded photos did not appear

That usually means the image host was unreachable during seeding. The seeder is designed to skip photo downloads rather than fail the whole setup. Re-run the seeder later if you want those assets.
