# University Academic Portal

University Academic Portal is a Laravel 12 application for managing academic operations across three primary authenticated roles: `student`, `teacher`, and `staff`.

The codebase combines:

- Blade-rendered public pages for guest-facing content
- Inertia.js + Vue 3 for the authenticated application
- Database-backed workflows for enrollment, grades, fees, attendance, assignments, announcements, messaging, and timetables
- Optional realtime updates through Laravel Reverb
- Seeded demo data, automated tests, and project-report artifacts such as diagrams and chapter drafts

This is not a minimal student portal. It is a full role-based academic operations system with public marketing pages, internal admin workflows, reporting/export features, queue-backed background work, and production-oriented integrations such as Stripe and reCAPTCHA.

## Contents

- [Product Overview](#product-overview)
- [Architecture and Stack](#architecture-and-stack)
- [System Components](#system-components)
- [Detailed Module Map](#detailed-module-map)
- [Domain Model Reference](#domain-model-reference)
- [Key Workflow Lifecycles](#key-workflow-lifecycles)
- [Dashboards, Search, and Notifications](#dashboards-search-and-notifications)
- [Repository Layout](#repository-layout)
- [Getting Started](#getting-started)
- [Demo Data](#demo-data)
- [Running the App in Development](#running-the-app-in-development)
- [Configuration Reference](#configuration-reference)
- [Data, Storage, and Files](#data-storage-and-files)
- [Reports and Exports](#reports-and-exports)
- [Testing and Quality](#testing-and-quality)
- [Production Deployment Guide](#production-deployment-guide)
- [Project Documentation Assets](#project-documentation-assets)
- [Troubleshooting](#troubleshooting)

## Product Overview

### Public and guest features

- Public home page with dynamic counts and highlight sections
- Public course catalog and course detail pages
- Public news/announcement pages with visibility and expiry rules
- About, vision, services, support, contact, feedback, policies, and user-manual pages
- Contact and feedback submission forms
- Optional Google reCAPTCHA v3 on guest forms
- Guest/global search across public pages, public courses, and public announcements
- Inline user manual preview and direct PDF download endpoints

### Student features

- Registration, login, password reset, and email verification
- Student profile self-service with photo updates
- Course browsing and enrollment request submission
- Withdrawal requests for approved enrollments
- My Courses view of approved and pending academic load
- Read-only grades view
- Attendance report view with CSV/PDF export
- Assignment list, assignment detail, submission upload, and submission download
- Fee ledger, payment confirmation submission, Stripe checkout, and payment status tracking
- Timetable view with CSV/PDF export
- Announcements, notifications, and internal messaging
- Student dashboard with GPA, fee, grade, attendance, and enrollment insights
- Per-user notification settings

### Teacher features

- View assigned courses and subjects
- Mark attendance for assigned subjects
- Record grades for assigned subjects
- Submit final grades for staff review
- Manage assignments for owned subjects
- Grade assignment submissions, including rubric-style feedback rows
- Download individual submissions or ZIP archives of all submissions
- View and export timetable
- Create and manage teacher announcements
- Teacher dashboard with grade trends, subject activity, and at-risk attendance insights

### Staff features

- Student CRUD, filtering, document handling, and photo management
- User CRUD and role assignment
- Course CRUD and course-teacher assignment
- Subject CRUD and subject-teacher assignment
- Enrollment approval/rejection and withdrawal approval/rejection
- Fee creation, manual review, reminders, receipts, and ledger export
- Grade review, approval, rejection, and bulk review
- Timetable CRUD and export
- Announcement management and reminder sending
- Attendance report generation and export
- Manual low-attendance alert dispatch
- Contact-message and feedback-message inbox management
- Failed-jobs review and retry controls
- Staff dashboard with queue-health, fee, attendance, and approval metrics
- Settings screen for attendance-alert default thresholds

## Architecture and Stack

### Core stack

- Backend: Laravel 12 on PHP 8.2+
- Auth/UI transport: Laravel Breeze + Inertia.js
- Frontend: Vue 3, Vite 7, Tailwind CSS
- Charts: Chart.js + `vue-chartjs`
- Database default: SQLite in `.env.example`
- Production-oriented relational option: MySQL or MariaDB
- Queues: database queue by default
- Sessions and cache: database-backed by default
- Payments: Stripe Checkout + webhook processing
- Realtime: Laravel Reverb + Laravel Echo + Pusher JS client
- PDF generation: `barryvdh/laravel-dompdf`
- Image processing: `intervention/image` using the GD driver
- Route rendering split:
  - public pages: Blade
  - authenticated app: Inertia/Vue

### Runtime characteristics

- Authenticated users are role-scoped by `User.role`
- `User` must verify email
- `Student` is a domain profile linked to `User`
- The portal uses queue-backed jobs, database notifications, and scheduled commands
- Dashboard payloads are built through dedicated service classes and cached for short intervals
- Search results are scoped differently for guests, students, teachers, and staff
- Exports are generated as CSV or PDF depending on module

### Infrastructure and extension expectations

At minimum, a practical local or production environment should provide:

- PHP 8.2+
- Composer 2+
- Node.js 20+
- npm 10+
- a relational database supported by Laravel
- `mbstring`, `fileinfo`, `curl`, `dom`, and database PDO extensions
- GD for image resizing
- `ZipArchive` if you want teacher ZIP download of all submissions

The Laravel `database.php` config also ships with MariaDB, PostgreSQL, SQL Server, and Redis settings, but the project defaults and documentation focus on SQLite and MySQL because those are what the repository config and deployment diagram are centered around.

### Security and access control

- Role middleware protects student, teacher, and staff route groups
- Policies explicitly protect fees, grades, and course enrollment actions
- Selected routes are throttled:
  - guest contact form: `10/min`
  - guest feedback form: `10/min`
  - global search: `90/min`
  - message send: `20/min`
  - message read: `120/min`
  - email verification resend: `6/min`
- Security headers middleware sets:
  - `Content-Security-Policy`
  - `X-Frame-Options`
  - `X-Content-Type-Options`
  - `Referrer-Policy`
  - `Permissions-Policy`
  - HSTS in secure production requests
- Stripe webhook is intentionally excluded from CSRF validation
- reCAPTCHA is conditional:
  - if keys are configured, auth/public flows require it
  - if keys are blank, local development continues without it

### Realtime behavior

- Messaging can broadcast over private channels like `messages.{userId}`
- Frontend Echo bootstrap is enabled only when `VITE_REVERB_APP_KEY` is present
- Broadcasting falls back to the `log` driver when Reverb or Pusher configuration is absent
- Messaging still works without websockets; only live in-browser updates depend on Reverb

## System Components

### Public presentation layer

The public surface is built mainly with Blade views under `resources/views/guest`. It handles:

- portal landing pages
- course marketing pages
- public announcement/news pages
- support and policy content
- contact and feedback intake
- user-manual preview and download

Public pages use dynamic counts from the database for course, student, faculty, enrollment, and attendance statistics rather than hard-coded brochure text.

### Authenticated application layer

The authenticated area uses Inertia + Vue pages from `resources/js/Pages`. It includes:

- role-aware navigation
- dashboard views
- CRUD and review interfaces
- chart components
- global search UI
- notifications
- messaging
- profile and settings management

### Background and scheduled work

The application uses the queue system for asynchronous features. Current built-in examples include:

- low-attendance alerts
- queue failure logging
- notifications that implement `ShouldQueue`

The scheduler dispatches `attendance:send-low-attendance-alerts` daily from `bootstrap/app.php`.

### External integrations

- Stripe:
  - checkout session creation
  - success/cancel redirect handling
  - signed webhook validation
  - idempotent webhook event recording
- Google reCAPTCHA v3:
  - public contact and feedback forms
  - registration and login when configured
- Email:
  - mailer infrastructure exists
  - only selected notifications currently use mail channels
- Laravel Reverb:
  - optional websocket transport for realtime messaging updates

## Detailed Module Map

### Public route surface

Primary public endpoints from `routes/web.php`:

- `/`
- `/guest/courses`
- `/guest/courses/{course}`
- `/guest/news`
- `/guest/news/{announcement}`
- `/guest/about`
- `/guest/vision`
- `/guest/services`
- `/guest/support`
- `/guest/contact`
- `/guest/feedback`
- `/guest/policies`
- `/guest/user-manuals`
- `/guest/user-manual`
- `/guest/user-manual/download`
- `/search`
- `/privacy-policy`
- `/terms-and-conditions`

Public forms:

- `POST /guest/contact`
- `POST /guest/feedback`

Manual PDF handling:

- `GET /guest/user-manual` serves the PDF inline
- `GET /guest/user-manual/download` forces a download

### Authentication and account surface

Auth routes are defined in `routes/auth.php` and include:

- registration
- login/logout
- forgot password
- password reset
- email verification prompt
- verification resend
- password confirmation/update

Profile/account routes include:

- `GET /profile`
- `PATCH /profile`
- `DELETE /profile`
- `GET /settings`
- `PATCH /settings`

Local-only convenience route:

- `/dev/verify-email-now`

This only exists in the `local` environment and provides a signed verification redirect for the currently logged-in user.

### Shared authenticated modules

All authenticated users can access:

- `/dashboard`
- `/announcements`
- `/notifications`
- `/messages`
- `/messages/create`
- `/settings`
- `/profile`

Shared behaviors:

- announcements can be marked as read or acknowledged
- notifications can be read individually or all at once
- messages can be created, viewed, and marked read
- dashboard payload is role-specific

### Student modules

Student route surface includes:

- profile
  - `/student/profile`
- course registration
  - `/courses`
  - `POST /courses/{course}/enroll`
  - `DELETE /courses/{course}/unenroll`
- enrolled course view
  - `/my-courses`
- grades
  - `/student/grades`
- attendance
  - `/student/attendance`
  - `/student/attendance/export/{format}`
- assignments
  - `/student/assignments`
  - `/student/assignments/{assignment}`
  - `POST /student/assignments/{assignment}/submit`
  - `/student/assignments/submissions/{submission}/download`
- fees
  - `/student/fees`
  - `POST /student/fees/{fee}/submit-payment`
- Stripe payment endpoints
  - `POST /payment/{fee}/checkout`
  - `/payment/{fee}/success`
  - `/payment/{fee}/cancel`
- timetable
  - `/student/timetable`
  - `/student/timetable/export/{format}`

### Teacher modules

Teacher route surface includes:

- teaching load
  - `/teacher/courses`
- attendance
  - `/teacher/attendance`
  - `/teacher/attendance/{subject}`
- grades
  - `/teacher/grades`
  - `/teacher/grades/{subject}`
  - `POST /teacher/grades/{subject}`
  - `POST /teacher/grades/{subject}/students/{student}/submit-final`
- assignments
  - `/teacher/assignments`
  - `/teacher/assignments/{subject}`
  - `/teacher/assignments/{subject}/create`
  - `POST /teacher/assignments/{subject}`
  - `/teacher/assignments/{assignment}/edit`
  - `PUT /teacher/assignments/{assignment}`
  - `POST /teacher/assignments/{assignment}/publish`
  - `DELETE /teacher/assignments/{assignment}`
  - `/teacher/assignments/{assignment}/submissions`
  - `/teacher/assignments/{assignment}/submissions/download-all`
  - `POST /teacher/assignments/submissions/{submission}/grade`
  - `/teacher/assignments/submissions/{submission}/download`
- timetable
  - `/teacher/timetable`
  - `/teacher/timetable/export/{format}`
- announcements
  - `/teacher/announcements/*`

Teacher ownership rules worth noting:

- teachers may only edit, publish, delete, grade, or download submissions for assignments they created
- teachers may only grade/view subjects assigned to them

### Staff/admin modules

Staff route surface includes:

- student management
  - `/students`
  - `/students/create`
  - `/students/{student}/edit`
  - bulk delete and photo removal
- course management
  - `/admin/courses/*`
  - `/admin/courses/{course}/assign-teachers`
- subject management
  - `/admin/subjects/*`
  - `/admin/subjects/{subject}/assign-teachers`
  - bulk teacher assignment
- user management
  - `/admin/users/*`
- enrollment review
  - `/admin/enrollments`
  - export, approve, reject, approve-withdrawal, reject-withdrawal
- fee management
  - `/admin/fees/*`
  - approve/reject payment
  - send reminders
  - export
  - receipt generation
- grade review
  - `/admin/grades`
  - subject-specific grade review
  - export
  - approve/reject
  - bulk review
- timetable management
  - `/admin/timetables/*`
  - export
- announcement management
  - `/admin/announcements/*`
  - reminder sending
- attendance reporting
  - `/admin/attendance/report`
  - export
- attendance alert dispatch
  - `POST /admin/attendance/alerts/run`
- public inboxes
  - `/admin/contact-messages`
  - `/admin/feedback-messages`
- failed jobs
  - `/admin/failed-jobs`
  - retry, retry-all, flush, delete

## Domain Model Reference

The following table summarizes the core domain objects and how they relate.

| Model / table | Purpose | Main relations | Important fields / behavior |
| --- | --- | --- | --- |
| `User` / `users` | Primary auth identity for every signed-in person | `hasOne Student`, `belongsToMany Course` through `course_teacher`, `belongsToMany Subject` through `subject_teacher`, sent/received messages | `role`, `photo`, `preferences`, `email_verified_at`; must verify email |
| `Student` / `students` | Academic profile linked to a student user | `belongsTo User`, `belongsToMany Course`, `hasMany Attendance`, `hasMany Grade`, `hasMany Fee` | `student_no`, demographics, emergency contact, programme, intake, docs, `status`, `enrollment_date`; GPA is computed from approved grades only |
| `Course` / `courses` | Programme/course container | `belongsToMany Student`, `belongsToMany User` teachers, `hasMany Subject`, `hasManyThrough` grades/attendance/timetables/assignments | `course_code`, `title`, `credits`, `semester`, `photo`, optional `attendance_threshold` |
| `Subject` / `subjects` | Teachable unit under a course | `belongsTo Course`, `belongsToMany User` teachers, `hasMany Grade`, `hasMany Attendance`, `hasMany Timetable`, `hasMany Assignment` | `subject_code`, `title`, `credits`, `description`, `photo`, optional `attendance_threshold` |
| `Enrollment` / `course_student` | Enrollment pivot between student and course | `belongsTo Student`, `belongsTo Course`, `hasMany EnrollmentStatusLog` | State machine includes `pending`, `approved`, `rejected`, `withdrawal_pending` |
| `EnrollmentStatusLog` | Audit trail for enrollment requests and review decisions | linked by enrollment, student, course, performer | stores `from_status`, `to_status`, `action`, `reason`, `meta` |
| `Timetable` / `timetables` | Subject schedule entry | `belongsTo Subject`, `belongsTo User` creator | `day_of_week`, `start_time`, `end_time`, `location`, `created_by`; course is derived through subject |
| `Attendance` / `attendances` | Presence/absence records | `belongsTo Student`, `belongsTo Subject` | `date`, `status`; course is derived through subject |
| `LowAttendanceAlertState` | State tracking for alert throttling | keyed by student | last rate, below-threshold flag, last alert timestamp |
| `Grade` / `grades` | Subject grade for a student | `belongsTo Subject`, `belongsTo Student`, `belongsTo User` grader/reviewer, `hasMany GradeReviewLog` | states: `draft`, `pending`, `approved`, `rejected`; `letter_grade` accessor converts numeric score |
| `GradeReviewLog` | Review audit trail | belongs to grade and staff reviewer | stores review decisions and reasons |
| `Fee` / `fees` | Student charge/payment record | `belongsTo Student`, `belongsTo User` processor, `hasMany FeeStatusLog` | states: `pending`, `payment_pending`, `failed`, `paid`; stores payment intent, processed metadata, dates |
| `FeeStatusLog` | Fee status history | belongs to fee | tracks fee state transitions, performer, notes, meta |
| `StripeWebhookEvent` | Idempotency and audit record for incoming Stripe webhooks | independent support table | unique `event_id`, payload snapshot, processed timestamp |
| `Assignment` / `assignments` | Teacher-created coursework item | `belongsTo Subject`, `belongsTo User` creator, `hasMany AssignmentSubmission` | states: `draft`, `published`, `closed`; stores allowed file types and max file size |
| `AssignmentSubmission` / `assignment_submissions` | Student submission for an assignment | `belongsTo Assignment`, `belongsTo Student`, `belongsTo User` grader | states: `submitted`, `graded`, `returned`; stores file path, original filename, score, feedback, grader |
| `Announcement` / `announcements` | Broadcast communication item | `belongsTo User` author, `hasMany AnnouncementRead` | `priority`, `pinned`, `require_ack`, `audience`, `publish_at`, `expires_at`; scopes filter by role and time |
| `AnnouncementRead` | Read/ack state for announcements | belongs to announcement and user | tracks `read_at` and `acknowledged_at` |
| `Message` / `messages` | Internal direct message | `belongsTo User` sender and receiver | stores sender/receiver roles and read flag |
| `ContactMessage` / `contact_messages` | Guest contact inbox item | staff-managed | stores sender details, subject, message, reply, read state |
| `FeedbackMessage` / `feedback_messages` | Guest feedback inbox item | staff-managed | stores type, message, read and replied state |
| `SystemSetting` / `system_settings` | Lightweight database-backed configuration | belongs to updater | currently used for attendance alert defaults with safe fallback when table is missing |

## Key Workflow Lifecycles

### Enrollment workflow

Request path:

1. Student requests enrollment in a course.
2. System checks for an existing enrollment row.
3. System blocks duplicate approved, pending, or withdrawal-pending states.
4. System checks timetable conflicts against already approved courses.
5. If valid, the request enters `pending`.
6. Staff approves or rejects the request.

Withdrawal path:

1. Student requests withdrawal from an approved course.
2. Enrollment becomes `withdrawal_pending`.
3. Staff either:
   - approves the withdrawal and deletes the enrollment row
   - rejects the withdrawal and restores `approved`

Important implementation details:

- reapplying after `rejected` is supported
- schedule conflicts are checked both at request time and at approval time
- enrollment changes can be logged to `enrollment_status_logs`
- students and staff receive database notifications for key decisions

### Grade workflow

Teacher grade lifecycle:

1. Teacher records or updates grade data.
2. Working state remains `draft`.
3. Teacher submits final grade for review.
4. Grade becomes `pending`.
5. Staff reviews the grade.
6. Staff either approves or rejects it.

Resulting states:

- `draft`
- `pending`
- `approved`
- `rejected`

Important implementation details:

- teachers can only act on assigned subjects
- tests specifically protect draft ownership between teachers
- review actions create grade review logs
- only approved grades count toward GPA and student-facing finalized reporting

### Fee workflow

Supported paths:

- manual confirmation path
- Stripe checkout path

Manual confirmation path:

1. Student opens fee list.
2. Student submits payment confirmation.
3. Fee becomes `payment_pending`.
4. Staff reviews and marks paid or rejected/failed.

Stripe path:

1. Student starts Stripe checkout.
2. Fee becomes `payment_pending`.
3. Success path can be confirmed from return session and/or webhook.
4. Webhook processing records the event and transitions the fee.
5. Duplicate webhook events are ignored safely.

Fee states:

- `pending`
- `payment_pending`
- `failed`
- `paid`

Important implementation details:

- webhook events are stored in `stripe_webhook_events`
- payment intent mismatches are guarded against
- already-paid fees are not reverted by later failure webhooks
- fee status changes can be logged to `fee_status_logs`

### Assignment workflow

Teacher side:

1. Teacher creates assignment.
2. Assignment starts as `draft`, `published`, or `closed`.
3. Published assignments notify enrolled students.
4. Teacher can update, publish, or delete their own assignments.

Student side:

1. Student sees only published assignments for approved or withdrawal-pending courses.
2. Student uploads a file and optional comments.
3. Resubmission is allowed only while assignment is open and submission has not been graded.
4. Teacher grades the submission and can attach rubric-derived feedback text.

Assignment states:

- `draft`
- `published`
- `closed`

Submission states:

- `submitted`
- `graded`
- `returned`

Important implementation details:

- teacher-configured allowed types: `pdf`, `doc`, `docx`, `txt`, `zip`, `rar`
- teacher-configured max size range: `1KB` to `10240KB`
- student controller enforces type and size before storing the file
- ZIP download of all submissions requires `ZipArchive`

### Announcement and attendance-alert workflow

Announcements:

- can be pinned
- can require acknowledgement
- can target specific roles through `audience`
- can be future-scheduled with `publish_at`
- can expire with `expires_at`

Attendance alerts:

1. Scheduler dispatches the low-attendance job daily.
2. The job computes per-student attendance rate.
3. Threshold comes from:
   - `system_settings` if staff changed it
   - otherwise `.env` / `config/attendance_alerts.php`
4. Alerts are sent when newly below threshold or when cooldown has elapsed.
5. `low_attendance_alert_states` prevents spam.

## Dashboards, Search, and Notifications

### Dashboards

Dashboard payloads are built through `DashboardStatsService` and role-specific builders:

- `StaffDashboardDataBuilder`
- `TeacherDashboardDataBuilder`
- `StudentDashboardDataBuilder`

Dashboard data is cached for roughly 2 minutes.

Staff dashboard includes:

- student and course totals
- fee totals
- attendance rate
- pending enrollments and withdrawals
- pending grades
- pending payments
- queue-health overview
- fee, grade, attendance, enrollment, and course charts

Teacher dashboard includes:

- teaching subject count
- students taught
- grades recorded
- attendance rate
- pending and approved grades
- submissions needing grading
- at-risk students by attendance
- subject, assignment, score-distribution, and attendance charts

Student dashboard includes:

- course count
- outstanding and paid fees
- grade count
- attendance rate
- GPA
- pending vs approved enrollments
- subject score trends and risk-subject detection

### Global search

Search behavior from `SearchController`:

- minimum query length: 2 characters
- result cap: 5 per entity type
- response format: lightweight JSON via `SearchResultResource`

Guest search covers:

- public courses
- public announcements/news
- static guest pages

Staff search covers:

- students
- users
- courses
- subjects
- announcements

Teacher search covers:

- assigned courses
- assigned subjects
- assignments in owned subjects
- visible announcements

Student search covers:

- courses
- published assignments in enrolled courses
- visible announcements

### Notifications and user preferences

User notification preferences are stored in `users.preferences`. Default keys include:

- `email_announcements`
- `email_messages`
- `email_notifications`
- `notify_timetable`
- `notify_attendance`
- `notify_grades`
- `notify_grade_review`
- `notify_fees`
- `notify_messages`
- `notify_assignments`
- `notify_announcements`
- `notify_enrollment_requests`
- `notify_management`

Current notification categories in `app/Notifications` include:

- assignments
- attendance alerts
- announcements
- contact and feedback inbox events
- enrollment requests and outcomes
- fee review and fee status updates
- grade review requests and outcomes
- grade publication
- internal messages
- timetable updates

Channel behavior:

- most notifications are database-only
- `LowAttendanceAlert` and `AnnouncementReminder` can also use the `mail` channel when `email_notifications` is enabled and mail is configured

### Logging and audit trails

The system keeps or emits operational history through several channels:

- `enrollment_status_logs`
- `grade_review_logs`
- `fee_status_logs`
- `stripe_webhook_events`
- `failed_jobs`
- structured application logs for:
  - queue failures
  - payment events
  - enrollment actions
  - reCAPTCHA failures
  - webhook processing problems

## Repository Layout

```text
app/                    Core Laravel application code
app/Http/Controllers/   Public, student, teacher, staff, auth, and shared controllers
app/Http/Requests/      Validation rules per module
app/Http/Resources/     API-style response transformers such as search/fees
app/Jobs/               Queued jobs
app/Models/             Domain models
app/Notifications/      Notification classes
app/Policies/           Authorization policies
app/Services/           Workflow and aggregation services
app/Support/            Small support helpers such as attendance alert settings
bootstrap/              App bootstrap, middleware, schedule
config/                 Laravel and feature configuration
database/migrations/    Schema history
database/seeders/       Demo/sample data seeders
docs/                   Written project chapter material
resources/js/           Vue pages, components, layouts, composables
resources/views/        Blade guest pages and PDF templates
routes/                 Web, auth, channels, console routes
tests/                  Feature and unit tests
public/                 Public assets, images, docs, videos
class_diagram/          PlantUML class diagrams
deployment/             Deployment diagram source
sequence/               Sequence diagrams
sitemap/                Site maps
usecase/                Use case diagrams
out/                    Rendered diagram outputs
fox-master/             Static theme/template assets for public pages
```

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer 2+
- Node.js 20+
- npm 10+
- SQLite or MySQL/MariaDB
- GD extension for image processing
- `ZipArchive` if you want ZIP export of teacher assignment submissions

### Quick start with the default SQLite setup

The repository already includes `database/database.sqlite`, and `.env.example` is configured for SQLite. That makes SQLite the fastest first-run option.

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

5. Create the storage symlink:

```bash
php artisan storage:link
```

6. Run migrations and seed the demo dataset:

```bash
php artisan migrate --seed
```

7. Start the normal development stack:

```bash
composer run dev
```

8. Open the app:

```text
http://127.0.0.1:8000
```

### MySQL setup instead of SQLite

Update `.env`:

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

- Queue-backed features expect the queue tables from the migration set
- Public files depend on `php artisan storage:link`
- Login, registration, and guest-form reCAPTCHA checks are automatically skipped if keys are blank
- Mail defaults to `log`, so email-capable notifications will not leave the app unless you configure a real mailer

### Resetting local data

To rebuild the database and reseed demo data:

```bash
php artisan migrate:fresh --seed
```

To rerun only seeders:

```bash
php artisan db:seed
```

## Demo Data

`DatabaseSeeder` runs `ComprehensiveDemoSeeder`, which prepares a broad demo dataset for end-to-end testing.

### Seeded accounts

All seeded users share this password:

```text
Password123!
```

Staff accounts:

- `alice.staff@example.com`
- `brian.staff@example.com`

Teacher accounts:

- `amelia.teacher@example.com`
- `ben.teacher@example.com`
- `chloe.teacher@example.com`

Student accounts:

- `student01@example.com` through `student18@example.com`

### Seeded dataset includes

- 5 courses
- 3 subjects per course
- course-teacher and subject-teacher assignments
- 18 student users with linked student profiles
- approved, pending, rejected, and withdrawal-pending enrollments
- timetable entries across weekdays and time slots
- attendance records including below-threshold cases
- assignments in draft, published, and closed states
- assignment submissions and grading samples
- grades in multiple review states
- fees in pending, payment-pending, failed, and paid states
- announcements with pinned, required-ack, future, visible, and expired examples
- internal messages
- guest contact and feedback samples

### Demo seeder image behavior

The seeder attempts to download demo photos from Pexels for courses, subjects, users, and students. If the image host is unreachable, the seeder skips those downloads and continues. That is intentional.

## Running the App in Development

### Standard development command

```bash
composer run dev
```

This starts:

- `php artisan serve`
- `php artisan queue:listen --tries=1`
- `php artisan pail --timeout=0`
- `npm run dev`

### Development processes explained

- `artisan serve` hosts the Laravel app
- `queue:listen` processes database jobs in development
- `pail` tails application logs
- Vite serves Vue and CSS assets with HMR

### Alternative manual process start

If you do not want to use the Composer wrapper, you can run services separately:

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
php artisan queue:work
```

Terminal 3:

```bash
npm run dev
```

Optional Terminal 4:

```bash
php artisan pail --timeout=0
```

### Scheduler

The scheduler dispatches low-attendance alerts daily from `bootstrap/app.php`.

To exercise scheduled behavior locally:

```bash
php artisan schedule:work
```

or:

```bash
php artisan schedule:run
```

### Queue workers

The default local environment uses:

```env
QUEUE_CONNECTION=database
```

If queue-backed features are not processing, start a worker:

```bash
php artisan queue:work
```

### Realtime with Laravel Reverb

Realtime is optional. The repo ships with Reverb dependencies and Echo bootstrap, but `.env.example` defaults to `BROADCAST_CONNECTION=log`.

To enable realtime messaging:

1. Set `BROADCAST_CONNECTION=reverb`
2. Set `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`, `REVERB_HOST`, `REVERB_PORT`, `REVERB_SCHEME`
3. Set matching `VITE_REVERB_*` values
4. Start the Reverb server:

```bash
php artisan reverb:start
```

### Stripe webhook testing

Set Stripe credentials in `.env`, then forward webhook events:

```bash
stripe listen --forward-to http://127.0.0.1:8000/stripe/webhook
```

Required Stripe-related keys:

- `STRIPE_KEY`
- `STRIPE_SECRET`
- `STRIPE_WEBHOOK_SECRET`

### Useful helper commands

Local-only one-click verification for the current signed-in user:

```text
/dev/verify-email-now
```

Dispatch low-attendance alerts manually:

```bash
php artisan attendance:send-low-attendance-alerts
```

Generate missing table thumbnails for uploaded images:

```bash
php artisan images:backfill-table-thumbs
```

Force regeneration:

```bash
php artisan images:backfill-table-thumbs --force
```

Limit thumbnail generation to selected folders:

```bash
php artisan images:backfill-table-thumbs --folder=users --folder=students
```
