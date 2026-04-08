# University Academic Portal Program Demonstration Video Recording Script

This document provides a 15-20 minute program demonstration video recording script for the University Academic Portal. It is aligned with the implemented system, the three timeboxes, and the project report structure.

## Recording Goal

Explain:

- the problem with the current manual academic process
- the purpose of the proposed portal
- the main system features by timebox
- the role-based workflows for guest, student, teacher, and staff
- the project outcome and future improvements

## Suggested Recording Setup

Prepare these tabs before recording:

1. Guest/public site
2. Staff account
3. Teacher account
4. Student account

Use seeded demo accounts:

- Staff: `alice.staff@example.com`
- Teacher: `amelia.teacher@example.com`
- Student: `student01@example.com`
- Password: `Password123!`

If Stripe is not configured locally, do not try a live card payment. Show the fee workflow, payment statuses, and receipt page while explaining that online Stripe checkout is supported by the system.

## Recommended Demo Flow

1. Introduce the project and problem background
2. Show the guest/public pages
3. Mention authentication, verification, search, and settings briefly
4. Show Timebox 1: registration, student, course, subject, enrolment
5. Show Timebox 2: grades, fees, payment, assignments
6. Show Timebox 3: timetable, attendance, announcements, messages, notifications
7. End with evaluation and future improvements

## Coverage Summary

This script is designed to cover all major implemented feature groups in the project.

### Covered as live demo

- guest/public pages
- staff dashboard and management modules
- student dashboard and self-service modules
- teacher dashboard and academic workflows
- course enrolment workflow
- grade submission, review, and student result viewing
- fee management and payment workflow
- assignment workflow
- timetable workflow
- attendance workflow
- announcements, messaging, notifications
- contact and feedback inboxes

### Covered by narration or brief mention

- registration, login, forgot password, reset password
- email verification
- user settings and notification preferences
- global search
- timetable and attendance export
- reminder sending
- Stripe checkout and webhook processing
- queue-backed alerts and email delivery
- failed-jobs management

### Not practical to fully demo live unless your environment is configured

- real Stripe card payment
- real email delivery
- queue worker processing in real time
- webhook delivery from external services

## Important Note About "All Features"

This document now covers all major feature areas from your report and repository.

However, a 15-20 minute screencast should not try to click every single sub-feature one by one, because:

- it becomes too long
- it feels repetitive
- the examiner usually wants the main workflows, system scope, and evidence of integration

So the script uses two levels:

- `live demo`: the most important workflows you should actually show
- `spoken coverage`: features you should mention while showing a related screen

## Full Time-Coded Script

| Time | What to show | What to say |
| --- | --- | --- |
| `0:00-1:10` | Guest home page | "Hello, my name is Kyaw Myo Htay, and this is my final year project, a University Academic Portal developed using Laravel, Vue.js, Inertia, Tailwind CSS, and MySQL. The aim of this project is to replace manual and spreadsheet-based university processes with a single integrated web-based platform." |
| `1:10-2:00` | Home page sections, quick navigation | "The project focuses on the major academic processes that are commonly handled manually, such as student registration, course enrolment, grades, fee management, timetables, attendance, and communication. The problem with the manual system is that it causes delays, duplicate records, data inconsistency, and difficulty in communication and reporting." |
| `2:00-3:00` | Public pages: Courses, News, About, Contact, Feedback, User Manual | "The portal also supports guest users. Public visitors can browse courses, read announcements, use the contact and feedback forms, and access the user manual. This improves accessibility and creates a single entry point for the academic portal." |
| `3:00-4:00` | Login page or prepared role tabs | "Before entering the main modules, the system also supports account registration, secure login, password reset, email verification, user settings, and role-based access control. To save time in this screencast, I am using pre-seeded accounts for staff, teacher, and student roles." |
| `4:00-5:30` | Staff dashboard | "The system is role-based, with separate interfaces for staff, teachers, and students. I will begin with the staff side because it manages most core records and administrative workflows. The dashboard summarises key statistics and gives quick access to the major modules." |
| `5:30-7:20` | Staff: Users, Students, Courses, Subjects, Assign Teachers | "Timebox 1 focused on student registration and course registration. Staff can create and manage user accounts, register students, create courses and subjects, and assign teachers. These workflows replace manual forms with validated digital records. The system checks required fields, unique email addresses, unique course and subject codes, supports photo upload, and includes search, filtering, pagination, and management controls." |
| `7:20-8:50` | Student tab: Browse Courses and My Courses, then staff Enrolments | "Students can browse available courses and submit enrolment requests through the portal. The system prevents duplicate enrolments and can detect timetable conflicts. When a student submits a request, the status becomes pending. Staff can then review, approve, or reject the request from the enrolment management page. Students can also request withdrawal, and approved courses are visible in the My Courses page." |
| `8:50-10:20` | Teacher dashboard, Assignments, Grades overview | "Timebox 2 focused on grades and fee payment, and during iterative development I also added assignment management to improve the grading workflow. Teachers can create assignments, publish them, view submissions, grade the submissions, and then use those results to support final grade submission." |
| `10:20-12:00` | Teacher grade submission, then staff grade review page, then student grades page | "Teachers can save grade data first and then submit final grades for staff review. This creates a controlled approval workflow. Staff can approve or reject grades, store reviewer information, keep grade review logs for traceability, and publish the approved result to the student. On the student side, the portal displays approved grades, grade breakdown, computed results, and GPA." |
| `12:00-13:30` | Staff fee management, student fee page, receipt | "The same timebox also includes fee and payment management. Staff can create fee records, update statuses, review submitted payments, reject or approve them, track overdue fees, send reminders, and generate receipts. Students can view their own fee information clearly with due dates and payment status. The portal also supports Stripe checkout and webhook processing for online payment integration." |
| `13:30-15:00` | Staff timetable management, teacher timetable, student timetable | "Timebox 3 introduced timetable, attendance, and communication features. Staff can create and update timetable records with validation rules to prevent invalid times, duplicates, and scheduling conflicts. Teachers and students can then view their role-based timetable in a weekly layout, and export options are also available for timetable reporting." |
| `15:00-16:20` | Teacher attendance marking, student attendance page, staff attendance report | "Teachers can record attendance by subject and date, while students can monitor their attendance statistics. Staff can view attendance reports across the system, including course and subject breakdowns, export the reports, and identify students below the attendance threshold. This digital process improves reporting accuracy and removes the need for paper-based attendance tracking." |
| `16:20-17:00` | Staff attendance alerts | "The portal also supports low-attendance monitoring. Staff can identify students below the attendance threshold and trigger alert workflows. These alerts can also work with queue-based processing and email delivery when the environment is configured." |
| `17:00-18:20` | Announcements, messages, notifications, contact messages, feedback inbox | "Communication is centralised through announcements, direct messages, notifications, and guest contact and feedback forms. Staff and teachers can create announcements, control visibility, set priority, send reminders, and require acknowledgement. Users can send direct messages, read notifications, and manage unread items. Staff can also manage contact and feedback inboxes from the guest site." |
| `18:20-19:10` | Global search, settings, failed jobs page, or dashboard | "In addition to the main workflows, the portal also includes user settings, structured search, notification preferences, and supporting administrative tools such as failed-jobs monitoring. These features improve maintainability, monitoring, and overall usability of the system." |
| `19:10-20:00` | Dashboard or diagrams/documentation assets | "From the development perspective, the system was implemented using Agile DSDM and divided into three timeboxes. The design was supported by use case diagrams, class diagrams, sequence diagrams, ERD, sitemap, prototypes, testing evidence, deployment planning, and user manual documentation. In conclusion, the University Academic Portal successfully integrates the major academic administration processes into one platform. It improves efficiency, reduces manual errors, strengthens communication, and supports staff, teachers, students, and guests through role-based workflows. Future improvements could include advanced analytics, stronger accessibility support, MFA, mobile optimisation, and deeper integration with other university systems. Thank you for watching." |

## Feature Coverage Matrix

| Feature Group | Coverage in script | How to present |
| --- | --- | --- |
| Public pages | Full | Live demo |
| Registration and login | Covered | Brief spoken mention |
| Email verification and password reset | Covered | Brief spoken mention |
| User settings and preferences | Covered | Brief spoken mention or quick show |
| Global search | Covered | Quick show near the end |
| Staff user management | Full | Live demo |
| Student registration and profile | Full | Live demo |
| Course and subject management | Full | Live demo |
| Teacher assignment | Full | Live demo |
| Enrolment and withdrawal workflow | Full | Live demo |
| Student My Courses | Full | Live demo |
| Teacher grades | Full | Live demo |
| Grade review logs and approval | Full | Live demo |
| Student grades and GPA | Full | Live demo |
| Assignment management | Full | Live demo |
| Fee management | Full | Live demo |
| Payment approval and rejection | Full | Live demo |
| Stripe and webhook support | Covered | Spoken mention |
| Receipt generation | Full | Quick live show |
| Timetable management | Full | Live demo |
| Timetable export | Covered | Spoken mention |
| Attendance recording | Full | Live demo |
| Attendance report and low-attendance alerts | Full | Live demo |
| Attendance export | Covered | Spoken mention |
| Announcements | Full | Live demo |
| Announcement acknowledgement and reminders | Full | Live demo or mention while showing page |
| Messaging | Full | Live demo |
| Notifications | Full | Live demo |
| Contact and feedback management | Full | Live demo |
| Failed jobs | Covered | Brief spoken mention |

## If You Want Maximum Detail In The Video

If your supervisor expects a very feature-heavy demonstration, add these short extra lines while showing the related page:

- "This module also supports search, filtering, sorting, and pagination."
- "Validation rules are enforced both on the form and the server side."
- "This workflow is protected by role-based access control."
- "Important changes are traceable through status logs or review logs."
- "Some modules also support export, reminders, and queue-backed notifications."

## Short Backup Lines During Recording

Use these if a feature is not configured or you do not want to risk a live failure during the recording.

- Stripe payment: "The portal supports Stripe checkout and webhook processing. In this recording, I am focusing on the fee workflow and payment status management rather than completing a live payment."
- Email notifications: "Some notifications can also be sent by email when the mail and queue configuration are enabled."
- Background jobs: "Low-attendance alerts and some notifications also support queued processing in the full deployment setup."

## Quick Checklist Before Recording

- Log in once with each role before recording
- Make sure seeded data is visible in dashboards and lists
- Keep the route flow ready in four browser tabs
- Avoid unnecessary scrolling in long management tables
- If something fails, continue the narration and explain the intended workflow calmly

## Suggested Closing Sentence

"This project demonstrates how a university can move from disconnected manual processes to an integrated academic portal that is more accurate, efficient, and easier to manage."
