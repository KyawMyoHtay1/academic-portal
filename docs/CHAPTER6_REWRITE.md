# Chapter 6: System Deployment, Data Migration, and Training

## 6.1 Introduction

This chapter explains how the University Academic Portal can be prepared for operational use after implementation. It covers the deployment architecture, integration requirements, data migration approach, user training strategy, and operational support considerations needed to move the system from a development environment to a usable institutional platform.

An important distinction should be made at the outset. The project demonstrates strong deployment readiness, but it does not claim that the portal has already been rolled out as a live university production system. The dissertation therefore presents a justified deployment and migration strategy based on the implemented Laravel application, rather than making unsupported claims about a completed institutional launch.

## 6.2 Deployment and Integration

### 6.2.1 Deployment Objectives

The deployment strategy for the University Academic Portal has four main objectives:

1. make the portal securely accessible through standard web browsers
2. support the multi-role workflows required by students, teachers, staff, and guest users
3. ensure that background processes such as low-attendance alerts and payment handling can run reliably
4. preserve data integrity, availability, and maintainability during ongoing operation

These objectives are consistent with the project scope defined in earlier chapters, especially because the portal manages enrolment, grades, fees, timetables, attendance, announcements, messaging, and support interactions.

### 6.2.2 Runtime Architecture

The implemented system follows a standard web application deployment model. End users access the portal through a browser on a laptop, tablet, or mobile device. Requests are sent over HTTP or HTTPS to the web application, which runs Laravel on PHP and serves Vue 3 interfaces through Inertia.js. Application data is stored in a relational database, while asynchronous tasks are handled through Laravel's queue system.

At runtime, the main deployment components are:

1. a client layer, consisting of student, teacher, staff, and guest browsers
2. a web and application server running Laravel, PHP, and frontend assets built by Vite
3. a MySQL database for academic and administrative records
4. a queue worker for background processing
5. a scheduler for automated periodic tasks
6. supporting external services such as Stripe, email delivery, and reCAPTCHA

This architecture is directly supported by the project configuration in [README.md](d:/university-portal/academic-portal/README.md), [bootstrap/app.php](d:/university-portal/academic-portal/bootstrap/app.php), [services.php](d:/university-portal/academic-portal/config/services.php), [queue.php](d:/university-portal/academic-portal/config/queue.php), and [web.php](d:/university-portal/academic-portal/routes/web.php).

### 6.2.3 Application Server and Background Services

The application server is responsible for routing, validation, middleware enforcement, business logic execution, file handling, and view delivery. The codebase is already structured to support this separation through controllers, services, middleware, models, and Vue pages.

The deployment also requires background service support. This is particularly important because the portal includes workflows that should not rely only on direct page requests. For example:

1. Stripe payment events are received through a webhook endpoint.
2. notification and email delivery may happen after user actions.
3. low-attendance alerts are scheduled as recurring background work.
4. failed jobs can be monitored through a staff-facing administrative view.

The scheduling of low-attendance alerts is defined in [app.php](d:/university-portal/academic-portal/bootstrap/app.php), and the production queue and scheduler requirements are described in [README.md](d:/university-portal/academic-portal/README.md).

### 6.2.4 Development and Target Deployment Environments

During development, the system runs in a local environment using Laravel, Vite, and the database configuration provided through the environment file. The repository documents a standard local setup process based on dependency installation, environment configuration, migration, seeding, and service startup.

For operational deployment, the target environment should include:

1. PHP 8.2 or later
2. Composer and Node.js build support
3. a web server such as Apache or Nginx
4. MySQL or a compatible production database
5. queue worker execution
6. scheduled command execution
7. configured mail delivery
8. configured Stripe keys and webhook secret where online payments are enabled

The environment variables needed for deployment are shown in [.env.example](d:/university-portal/academic-portal/.env.example). These include database settings, queue settings, mail settings, Stripe keys, and reCAPTCHA configuration.

### 6.2.5 Deployment Procedure

A practical deployment sequence for the portal is as follows:

1. provision the target server and database environment
2. deploy the application source code
3. install PHP and JavaScript dependencies
4. configure the `.env` file for the target environment
5. run database migrations
6. optionally seed baseline or demonstration data where appropriate
7. build frontend assets
8. configure queue and scheduler execution
9. configure the Stripe webhook endpoint if online fee payment is enabled
10. verify mail delivery, role access, and core workflows

The repository already documents the essential setup commands:

```bash
composer install
npm ci
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
php artisan queue:work
php artisan schedule:run
```

These commands and their role in the setup process are described in [README.md](d:/university-portal/academic-portal/README.md).

### 6.2.6 Integration Points

The portal includes several important integration points that must be considered during deployment.

First, payment processing depends on Stripe Checkout and Stripe webhooks. The payment logic is implemented in [PaymentService.php](d:/university-portal/academic-portal/app/Services/PaymentService.php), while the webhook endpoint is exposed in [web.php](d:/university-portal/academic-portal/routes/web.php). Stripe configuration is also documented in [STRIPE_SETUP_GUIDE.md](d:/university-portal/academic-portal/STRIPE_SETUP_GUIDE.md).

Second, email support is required for account verification, password reset, some notifications, and contact-related processes. The mail configuration options are defined in [.env.example](d:/university-portal/academic-portal/.env.example) and [mail.php](d:/university-portal/academic-portal/config/mail.php).

Third, the portal uses public support, contact, feedback, and user-manual pages to assist non-technical users and guest users. These are exposed through guest routes in [web.php](d:/university-portal/academic-portal/routes/web.php), supported by views such as [support.blade.php](d:/university-portal/academic-portal/resources/views/guest/support.blade.php), [contact.blade.php](d:/university-portal/academic-portal/resources/views/guest/contact.blade.php), [feedback.blade.php](d:/university-portal/academic-portal/resources/views/guest/feedback.blade.php), and [user-manual.blade.php](d:/university-portal/academic-portal/resources/views/guest/user-manual.blade.php).

### 6.2.7 Deployment Risks and Controls

Deployment introduces operational risks that should be managed before live use.

| Risk | Likely Impact | Control |
| --- | --- | --- |
| Incorrect environment configuration | Application startup failure or broken services | Validate `.env` values before release and test in staging |
| Queue worker not running | delayed alerts or notifications | configure persistent queue worker supervision |
| Scheduler not running | automated attendance alerts not triggered | add scheduled task monitoring |
| Unreachable Stripe webhook | payment state mismatch | verify public webhook endpoint and log webhook events |
| Mail misconfiguration | verification and reset flows fail | test mail delivery before release |
| Migration failure | inconsistent schema or blocked deployment | back up database and test migrations in non-production first |
| File permission problems | uploads or document retrieval fail | validate storage permissions during deployment |

This type of risk control is especially important because the system contains workflows with state transitions and audit significance, not just simple data entry pages.

## 6.3 Data Migration

### 6.3.1 Purpose of Data Migration

If the portal were introduced into a real university environment, historical and reference data would need to be transferred from existing records into the new database. The aim of data migration is to populate the portal with reliable information before operational use, while preserving correctness, referential integrity, and usability.

The data to be migrated can be divided into two broad categories:

1. master data, such as users, students, courses, subjects, and settings
2. transaction data, such as enrolments, grades, fees, assignments, attendance, announcements, and messages

### 6.3.2 Migration Scope by Module

The migration scope closely follows the timebox structure used throughout the project.

| Timebox | Main Data Areas |
| --- | --- |
| Timebox 1 | users, student records, courses, subjects, enrolment records |
| Timebox 2 | grades, fee records, payment states, assignments, submissions |
| Timebox 3 | timetables, attendance records, announcements, messages, notifications |

This grouping is useful because it allows migration planning to follow the same modular structure as requirements, implementation, and testing.

### 6.3.3 Implemented Migration Readiness in the Prototype

The project already demonstrates migration readiness in two important ways.

First, the database schema is fully version-controlled through Laravel migrations located in [database/migrations](d:/university-portal/academic-portal/database/migrations). These migrations define the table structure, relationships, status fields, indexes, and later workflow enhancements needed by the portal.

Second, the system includes seeders that populate the database with representative operational data. These include [DatabaseSeeder.php](d:/university-portal/academic-portal/database/seeders/DatabaseSeeder.php), [ComprehensiveDemoSeeder.php](d:/university-portal/academic-portal/database/seeders/ComprehensiveDemoSeeder.php), and [SampleDataSeeder.php](d:/university-portal/academic-portal/database/seeders/SampleDataSeeder.php). While seeders are not the same as a live institutional migration, they do provide evidence that the schema can be populated in a structured and repeatable way.

This distinction matters for academic honesty. The project can credibly claim that schema migration and seeded data preparation were implemented, but it should not claim that real university legacy data was imported unless such a process was actually executed and evidenced.

### 6.3.4 Proposed Data Migration Process

A realistic migration process for institutional use would follow these stages:

1. identify source datasets and responsible owners
2. map source fields to the portal schema
3. clean and normalise inconsistent values
4. validate required fields, unique values, and relationships
5. import master data before transaction data
6. verify record counts and spot-check samples after import
7. retain rollback and backup capability until migration is confirmed

The recommended import order is:

1. users and role information
2. student records
3. courses and subjects
4. enrolments
5. grades and fee records
6. timetable and attendance records
7. communication records and other secondary data

This order reduces referential problems because later records depend on the existence of earlier master data.

### 6.3.5 Data Quality Controls

Migration quality depends on validation and reconciliation rather than only loading data into tables. The portal already contains many validation concepts that can support migration quality, including:

1. unique email and student number handling
2. role-based user distinctions
3. approval and status values for enrolment, grades, and fees
4. subject and course relationships
5. timetable and attendance dependencies

In a real migration exercise, data quality checks should include:

1. field completeness checks
2. duplicate detection
3. format validation
4. foreign-key and relationship verification
5. status consistency checks
6. sample-based manual review by domain staff

### 6.3.6 Backup and Recovery Considerations

Before any production migration, the existing data source and the target portal database should both be backed up. Recovery planning is necessary because academic data is operationally sensitive and errors in migrated grades, fees, or enrolment records could affect real users.

For this dissertation project, the strongest evidence of controlled change is the use of database migrations, repeatable seeding, structured logging, and state-based workflows. In a live deployment, these controls should be complemented by formal backup routines and rollback procedures.

## 6.4 Training and User Adoption

### 6.4.1 Training Objectives

Training is necessary because the portal serves multiple user groups with different technical responsibilities. The aim of training is not only to show users where buttons are located, but to ensure that they understand the workflow logic of the system and can use it correctly within their role.

The training objectives are:

1. enable students to access and use self-service academic functions
2. enable teachers to manage grades, assignments, attendance, timetables, and announcements correctly
3. enable staff users to administer records, review workflow transitions, and monitor operational issues
4. reduce user errors and increase confidence in system adoption

### 6.4.2 Training Groups

The training can be divided into the following user groups:

1. students
2. teachers
3. staff and administrators
4. guest-facing support or front-desk users where relevant

Each group requires different emphasis.

Students mainly need guidance on registration, login, profile management, course requests, grades, fees, timetable viewing, assignment submission, announcements, and messages.

Teachers mainly need guidance on grade entry, final grade submission, attendance recording, assignment management, timetable access, and announcement management.

Staff users require broader training because they manage user accounts, student records, courses, subjects, enrolment approvals, fee administration, grade reviews, timetable maintenance, support inboxes, failed jobs, and reporting.

### 6.4.3 Training Delivery Strategy

A suitable training strategy for the portal combines short demonstrations, guided practice, and self-service reference material.

The recommended approach is:

1. introductory demonstration for each user group
2. hands-on task-based training using realistic scenarios
3. role-specific quick-reference guides
4. a user manual for self-service review
5. access to support and feedback channels after training

This approach is preferable to a single large lecture because the portal is workflow-driven. Users learn more effectively when they perform realistic tasks such as submitting an enrolment request, approving grades, recording attendance, or responding to support messages.

### 6.4.4 User Manual and Self-Service Support

The project already includes user-manual support in the deployed interface. The manual can be viewed or downloaded through guest routes defined in [web.php](d:/university-portal/academic-portal/routes/web.php), and the PDF is present at [University_Academic_Portal_User_Manual.pdf](d:/university-portal/academic-portal/public/docs/University_Academic_Portal_User_Manual.pdf). The public manual page is implemented in [user-manual.blade.php](d:/university-portal/academic-portal/resources/views/guest/user-manual.blade.php).

This is important because it strengthens operational readiness. A system becomes easier to adopt when formal training is supported by on-demand reference material.

However, for dissertation quality, the main chapter should not reproduce the full screen-by-screen user manual. A better structure is:

1. summarise the training and manual strategy in the chapter
2. place the detailed manual in an appendix or attached PDF
3. reference the manual as supporting evidence rather than using it as the main narrative

This keeps the dissertation academically focused while still showing that user support material exists.

### 6.4.5 Post-Training Support

After initial training, users should still have access to operational support. The portal already supports this through:

1. a guest support page
2. contact and feedback forms
3. internal notifications and messages
4. staff-facing contact and feedback inbox management
5. failed-job monitoring for operational follow-up

These functions are visible in [web.php](d:/university-portal/academic-portal/routes/web.php), [support.blade.php](d:/university-portal/academic-portal/resources/views/guest/support.blade.php), [ContactController.php](d:/university-portal/academic-portal/app/Http/Controllers/ContactController.php), [FeedbackController.php](d:/university-portal/academic-portal/app/Http/Controllers/FeedbackController.php), and [StaffFailedJobController.php](d:/university-portal/academic-portal/app/Http/Controllers/StaffFailedJobController.php).

## 6.5 Operational Readiness and Support Controls

Beyond deployment and training, the portal includes several controls that support stable operation.

First, security-related HTTP headers are applied through [SecurityHeaders.php](d:/university-portal/academic-portal/app/Http/Middleware/SecurityHeaders.php). These include content security policy rules, frame restrictions, referrer policy, permissions policy, and HSTS in secure production contexts.

Second, role-based access is enforced through middleware and route grouping, helping ensure that students, teachers, staff, and guests only access relevant system functions.

Third, throttling is applied to public and message-related routes to reduce abuse risk. This is particularly important for contact forms, feedback forms, authentication flows, and messaging.

Fourth, the project includes queue-related operational support through failed-job management. This is useful because some portal functions depend on background processing rather than immediate synchronous execution.

These controls do not replace full enterprise operations management, but they do show that the portal was developed with practical deployment and support considerations in mind.

## 6.6 Chapter Summary

This chapter has explained how the University Academic Portal can be prepared for operational use through a structured deployment, data migration, and training approach. The deployment strategy is based on a Laravel and Vue application running with a relational database, queue worker, scheduler, and supporting integrations such as Stripe, mail, and reCAPTCHA.

The chapter also clarified an important academic point: the project demonstrates deployment readiness, schema migration support, seeded data preparation, and user-support material, but it should not over-claim a completed live university rollout where such evidence does not exist. This distinction strengthens the credibility of the dissertation.

Finally, the chapter showed that successful use of the portal depends not only on technical installation, but also on controlled data migration, role-based training, accessible user guidance, and practical operational support. These deployment and adoption considerations provide the foundation for the final evaluation in Chapter 7.
