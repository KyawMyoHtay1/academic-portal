# Chapter 5: System Implementation

## 5.1 Introduction

This chapter explains how the University Academic Portal was implemented across the planned development timeboxes. It describes the implementation environment, the main modules delivered in each timebox, the key technical decisions taken during development, and the main testing and refinement outcomes that shaped the final system.

The purpose of the chapter is not to reproduce every low-level validation rule or every interface screenshot in the main text. Instead, it explains how the design from Chapter 4 was translated into a working Laravel and Vue-based portal. Detailed diagrams, prototypes, test scripts, screenshots, and extended evidence can be placed in appendices or supporting documents.

## 5.2 Implementation Environment

The University Academic Portal was implemented as a full-stack web application using the following core technologies:

1. Laravel 12 and PHP 8.2 for the backend.
2. Vue 3 with Inertia.js for the frontend.
3. Tailwind CSS for responsive user interface styling.
4. MySQL as the main database.
5. Stripe Checkout and webhooks for online payment processing.

The implementation followed the MVC pattern justified in Chapter 3. Laravel was used to manage routes, controllers, middleware, models, notifications, jobs, migrations, and business logic. Vue pages handled interactive user interfaces for the different user roles, while Inertia.js linked backend data and frontend rendering without requiring a separate single-page API architecture.

The backend structure is visible across the controller layer in [app/Http/Controllers](d:/university-portal/academic-portal/app/Http/Controllers), the service layer in [app/Services](d:/university-portal/academic-portal/app/Services), and the database schema in [database/migrations](d:/university-portal/academic-portal/database/migrations). Frontend implementation is organised by role and module under [resources/js/Pages](d:/university-portal/academic-portal/resources/js/Pages).

## 5.3 Implementation Strategy

Implementation followed the DSDM timebox plan defined in Chapter 4. Each timebox delivered a coherent group of related features rather than isolated pages. This helped keep development controlled and made it easier to connect design, coding, testing, and evaluation.

The project also adopted a layered implementation approach:

1. Database schema and migrations defined the data structure.
2. Eloquent models defined relationships and reusable data behaviour.
3. Controllers handled requests, validation, and routing.
4. Service classes were used for complex business logic such as enrolment decisions, payment processing, and computed grade calculation.
5. Vue pages implemented the user interface for each role.

This approach improved maintainability and made it easier to test and refine individual workflows.

## 5.4 Timebox 1: Student Registration and Course Registration

Timebox 1 established the core identity and academic registration foundations of the system.

### 5.4.1 User and Authentication Features

The implementation included guest registration, login, password reset, email verification, profile handling, settings, and global search support. Laravel Breeze and related authentication features provided the base authentication flow, while role-based access was enforced through middleware and route grouping.

In practice, the portal supports:

1. public registration and authentication pages
2. email verification support
3. profile update and password management
4. settings and notification preference management
5. global search with role-aware behaviour

These features are reflected in the public and authenticated routes in [routes/web.php](d:/university-portal/academic-portal/routes/web.php) and the authentication and profile-related pages in [resources/js/Pages/Auth](d:/university-portal/academic-portal/resources/js/Pages/Auth), [resources/js/Pages/Profile](d:/university-portal/academic-portal/resources/js/Pages/Profile), and [resources/js/Pages/Settings](d:/university-portal/academic-portal/resources/js/Pages/Settings).

### 5.4.2 Student, Course, and Subject Management

The administrative implementation for Timebox 1 includes:

1. user management
2. student record management
3. course management
4. subject management
5. teacher assignment to courses and subjects

The system stores student personal details, programme information, emergency contacts, and supporting documents such as ID cards and transcripts. Student numbers are generated automatically, and administrators can search, update, and manage records through dedicated interfaces. Course and subject administration also support search, filtering, editing, and deletion workflows with validation.

These functions are implemented mainly through:

1. [StudentController.php](d:/university-portal/academic-portal/app/Http/Controllers/StudentController.php)
2. [StaffUserController.php](d:/university-portal/academic-portal/app/Http/Controllers/StaffUserController.php)
3. [StaffCourseController.php](d:/university-portal/academic-portal/app/Http/Controllers/StaffCourseController.php)
4. [StaffSubjectController.php](d:/university-portal/academic-portal/app/Http/Controllers/StaffSubjectController.php)
5. [Students](d:/university-portal/academic-portal/resources/js/Pages/Students)
6. [Admin/Courses](d:/university-portal/academic-portal/resources/js/Pages/Admin/Courses)
7. [Admin/Subjects](d:/university-portal/academic-portal/resources/js/Pages/Admin/Subjects)

### 5.4.3 Enrolment Workflow Implementation

The course enrolment workflow is one of the most important implementations in Timebox 1. Students can browse courses, request enrolment, request withdrawal, and view their active course records. Staff can then review and approve or reject enrolment-related requests.

The main logic is implemented in [EnrollmentService.php](d:/university-portal/academic-portal/app/Services/EnrollmentService.php), which handles:

1. duplicate request prevention
2. timetable conflict detection
3. transaction-based updates
4. rejection and resubmission handling
5. enrolment status logging
6. staff notification of new requests

This service-based design is important because enrolment is more than a simple CRUD operation. The workflow includes status transitions, conflict checking, and approval control, so separating business logic from controller code improved maintainability and correctness.

### 5.4.4 Timebox 1 Testing and Refinement

Timebox 1 was tested through functional validation of registration, login, user management, student management, course administration, subject management, and enrolment actions. The main concerns were:

1. input validation
2. duplicate prevention
3. search and filtering correctness
4. role-based access
5. enrolment status flow

Usability refinement in this timebox focused on improving login clarity, course browsing, pagination, breadcrumb navigation, and required-field visibility. These improvements supported the usability goals identified in earlier chapters.

### 5.4.5 Timebox 1 Outcome

Timebox 1 successfully established the core structure of the portal. By the end of this phase, the system already supported authenticated multi-role use, student record administration, course and subject data management, and an approval-based enrolment workflow. This provided the stable foundation required for the later academic and financial modules.

## 5.5 Timebox 2: Grades, Fees, Payments, and Assignment Support

Timebox 2 added the core academic performance and payment workflows.

### 5.5.1 Grade Submission and Review Workflow

The grade module was implemented with a teacher submission and staff approval model rather than immediate direct publication. This design improves traceability and makes the portal more suitable for academic record management.

The implemented workflow includes:

1. teacher entry of grades for assigned subjects
2. submission of grades for review
3. staff approval or rejection of pending grades
4. review logging and status history
5. student visibility of approved grades

The relevant implementation is handled mainly in:

1. [TeacherGradesController.php](d:/university-portal/academic-portal/app/Http/Controllers/TeacherGradesController.php)
2. [StaffGradesController.php](d:/university-portal/academic-portal/app/Http/Controllers/StaffGradesController.php)
3. [StudentGradesController.php](d:/university-portal/academic-portal/app/Http/Controllers/StudentGradesController.php)

The use of review logs and status transitions makes the grade process more defensible than a simple overwrite-based system.

### 5.5.2 GPA and Computed Grade Logic

The system also implements GPA display and computed grade support. The student's GPA is calculated from approved grades only, which is implemented in [Student.php](d:/university-portal/academic-portal/app/Models/Student.php). In addition, assignment-based computed grade logic is handled by [SubjectGradeCalculator.php](d:/university-portal/academic-portal/app/Services/SubjectGradeCalculator.php), which aggregates graded assignment performance into a calculated subject-level result suggestion.

This design adds value because it links the assignment workflow and the final grade workflow in a coherent way.

### 5.5.3 Fee and Payment Workflow

Timebox 2 also implemented fee administration and payment handling. Students can view fees, submit payment confirmation, or use the online payment path. Staff can create fee records, review payment states, approve or reject submitted payments, generate receipts, and monitor overdue fees.

The payment architecture includes both manual and Stripe-based processing. Stripe integration is handled in [PaymentService.php](d:/university-portal/academic-portal/app/Services/PaymentService.php), which supports:

1. checkout session creation
2. payment-pending state handling
3. webhook event processing
4. successful payment confirmation
5. payment failure rollback
6. duplicate webhook protection

This implementation is stronger than a basic mock payment flow because it includes idempotent webhook handling and status-based transitions.

### 5.5.4 Assignment Workflow

Assignment management was implemented as a supporting academic workflow within Timebox 2. Teachers can create, publish, edit, and delete assignments, while students can view available assignments and submit files. Teachers can then view submissions, grade them, and provide feedback.

This feature is implemented mainly in:

1. [TeacherAssignmentController.php](d:/university-portal/academic-portal/app/Http/Controllers/TeacherAssignmentController.php)
2. [StudentAssignmentController.php](d:/university-portal/academic-portal/app/Http/Controllers/StudentAssignmentController.php)

The module supports submission constraints, grading logic, and assignment-related notifications, making it a useful extension to the grade management process.

### 5.5.5 Timebox 2 Testing and Refinement

Testing in Timebox 2 focused on:

1. teacher authorisation and subject ownership
2. correctness of grade status transitions
3. GPA calculation and assignment-based grade calculation
4. fee filtering and status updates
5. Stripe checkout and payment status handling
6. receipt generation and late-payment tracking

Usability improvements in this phase focused on grade visibility, fee status clarity, assignment handling, and the presentation of financial and academic data in a more readable form.

### 5.5.6 Timebox 2 Outcome

By the end of Timebox 2, the portal supported complete academic result and fee workflows. Students could view approved grades and financial status, teachers could manage grades and assignments, and staff could supervise academic and financial record transitions through controlled review interfaces.

## 5.6 Timebox 3: Timetable, Attendance, and Communication

Timebox 3 completed the portal by adding coordination, monitoring, and communication features.

### 5.6.1 Timetable Management

The timetable module allows staff to create and maintain schedules while preventing conflicts. Students and teachers can then view their schedules through role-specific interfaces. The timetable implementation is supported by:

1. [StaffTimetableController.php](d:/university-portal/academic-portal/app/Http/Controllers/StaffTimetableController.php)
2. [StudentTimetableController.php](d:/university-portal/academic-portal/app/Http/Controllers/StudentTimetableController.php)
3. [TeacherTimetableController.php](d:/university-portal/academic-portal/app/Http/Controllers/TeacherTimetableController.php)

One of the major improvements in this timebox was the move toward a clearer weekly timetable layout and stronger conflict feedback.

### 5.6.2 Attendance and Low-Attendance Monitoring

The attendance implementation allows teachers to record attendance by subject and date, while students can review attendance summaries and staff can generate attendance reports. The attendance module also supports low-attendance alert logic through:

1. [TeacherAttendanceController.php](d:/university-portal/academic-portal/app/Http/Controllers/TeacherAttendanceController.php)
2. [StudentAttendanceController.php](d:/university-portal/academic-portal/app/Http/Controllers/StudentAttendanceController.php)
3. [StaffAttendanceReportController.php](d:/university-portal/academic-portal/app/Http/Controllers/StaffAttendanceReportController.php)
4. [SendLowAttendanceAlertsJob.php](d:/university-portal/academic-portal/app/Jobs/SendLowAttendanceAlertsJob.php)

The job-based low-attendance alert implementation is particularly important because it extends the system beyond static reporting. It enables threshold-based and cooldown-based alerting, which makes the feature more proactive and more realistic.

### 5.6.3 Announcements, Messaging, and Notifications

The communication layer of the portal includes:

1. announcements with visibility and acknowledgement support
2. internal messages between authenticated users
3. notification delivery and read management
4. public contact and feedback forms for guest users

These features are implemented through controllers such as:

1. [AnnouncementController.php](d:/university-portal/academic-portal/app/Http/Controllers/AnnouncementController.php)
2. [MessageController.php](d:/university-portal/academic-portal/app/Http/Controllers/MessageController.php)
3. [NotificationController.php](d:/university-portal/academic-portal/app/Http/Controllers/NotificationController.php)
4. [ContactController.php](d:/university-portal/academic-portal/app/Http/Controllers/ContactController.php)
5. [FeedbackController.php](d:/university-portal/academic-portal/app/Http/Controllers/FeedbackController.php)

This implementation is significant because it ensures the portal is not limited to static record management. It also acts as a communication hub, which aligns strongly with the project aim described in Chapter 1.

### 5.6.4 Timebox 3 Testing and Refinement

Timebox 3 testing focused on:

1. timetable conflict prevention
2. attendance data accuracy
3. report correctness
4. low-attendance alert logic
5. announcement visibility and acknowledgement
6. message delivery and notification management
7. guest contact and feedback submission handling

Usability refinement in this phase focused especially on timetable readability, faster attendance entry, stronger announcement visibility, better inbox management, and easier notification control.

### 5.6.5 Timebox 3 Outcome

Timebox 3 completed the portal as a full academic workflow system rather than only a registration or record-management tool. The system now supported timetable access, attendance monitoring, communication, and alerting in addition to the earlier administrative and academic features.

## 5.7 Database Implementation

The database schema was implemented incrementally through Laravel migrations. The migration history shows how the portal evolved from the initial core entities to the later workflow and traceability features. The schema includes foundational tables such as users, students, courses, subjects, enrolments, grades, fees, timetables, attendances, announcements, messages, and notifications, as well as later additions such as:

1. grade review logs
2. assignment tables
3. low attendance alert state
4. Stripe webhook event records
5. fee status logs
6. enrolment status logs
7. system settings

This migration-based implementation approach improved traceability and helped keep schema changes controlled over time. Later refinements also removed redundant direct `course_id` storage from subject-based grade and assignment records, and tightened enrolment logging by linking status logs back to enrolment rows where available.

## 5.8 Overall Testing and Quality Support

Testing and quality support were integrated across the implementation rather than left until the end. The project uses:

1. backend testing through Laravel test tooling
2. formatting and style checks through Pint
3. frontend build verification
4. module-specific functional testing
5. usability-driven refinements

The repository also includes project quality commands described in [README.md](d:/university-portal/academic-portal/README.md), including `composer check`, `php artisan test`, and `npm run build`.

This approach is important because the portal contains multiple interdependent workflows. Without testing and iterative refinement, issues in enrolment logic, payment state, grade review, timetable conflicts, or attendance reporting could undermine the reliability of the entire system.

## 5.9 Implementation Challenges and Solutions

Several implementation challenges were encountered during development.

### 5.9.1 Workflow Complexity

Many modules were more complex than simple CRUD pages because they involved status transitions and role-based decisions. Examples include enrolment approval, grade review, payment approval, and announcement acknowledgement. This was addressed by separating business logic into services and using explicit statuses and logs.

### 5.9.2 Payment Integration Complexity

Stripe integration required careful handling of pending, succeeded, failed, and duplicate webhook events. This was addressed using dedicated payment service logic, database status handling, and webhook event recording.

### 5.9.3 Usability at Scale

As more data-heavy modules were added, interface readability became more difficult. This was addressed through pagination, filters, search, badges, summary cards, weekly timetable layouts, and clearer user feedback.

### 5.9.4 Cross-Module Consistency

Because the portal spans many modules, consistency in role access, navigation, notifications, and status indicators was important. This was addressed by keeping shared behaviour consistent across routes, controllers, layouts, and page patterns.

## 5.10 Chapter Summary

This chapter has explained how the University Academic Portal was implemented across the planned DSDM timeboxes. Timebox 1 delivered the core identity, student, course, and enrolment foundations. Timebox 2 delivered grade, fee, payment, and assignment workflows. Timebox 3 completed the system with timetable, attendance, communication, and alerting features.

The implementation combined Laravel, Vue, Inertia, Tailwind CSS, MySQL, and Stripe into a coherent multi-role academic portal. It also used controllers, services, migrations, notifications, and queued jobs to support more complex workflows such as enrolment management, computed grades, payment processing, and low-attendance alerting.

These implementation outcomes provide the basis for Chapter 6 and Chapter 7, where testing, deployment readiness, and evaluation of the final system are discussed in more detail.
