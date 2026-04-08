# University Academic Portal Program Demonstration Video Recording Script

This document provides a 15-20 minute program demonstration video recording script for the University Academic Portal. It is aligned with the implemented system, the three timeboxes, and the project report structure, and it includes what to click, what to show, and what to say.

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

## Exact Click-By-Click Recording Order

Use this section while recording if you want a more guided walkthrough. The labels below match the current sidebar and page actions in the app.

Important:

- For actions that change seeded data, either perform them live or point at the button and explain the workflow.
- If a table is long, use the first visible record instead of scrolling too much.
- Keep all four tabs logged in before you start: guest, staff, teacher, student.

| Time | Tab / role | Exact clicks | What to highlight while speaking |
| --- | --- | --- | --- |
| `0:00-1:10` | Guest | Open the home page. Stay on the landing page and scroll slightly if needed. | Project title, portal purpose, and that this is one integrated academic platform. |
| `1:10-3:00` | Guest | Use the top navigation to open `Courses`, `News`, `About`, `Contact`, `Feedback`, and `User Manual`. | Public access, course discovery, announcements, and guest communication forms. |
| `3:00-4:00` | Login / tabs | Briefly show the login page if needed, then switch across the prepared `Staff`, `Teacher`, and `Student` tabs. | Role-based access, registration, login, password reset, and email verification by narration. |
| `4:00-5:30` | Staff | In the staff tab, click `Dashboard`. | Summary cards, quick links, and that staff manages the main academic records. |
| `5:30-6:00` | Staff | Click `People > Manage Users`. Point at `Add User`, the role tabs `All`, `Students`, `Teachers`, `Staff`, and the search box. | User creation, role assignment, search, filtering, sorting, and administration controls. |
| `6:00-6:35` | Staff | Click `People > Student Records`. Point at `Add student`, then on the first row click `Quick view`, then `Done`. | Student registration, academic records, quick profile access, and filter options. |
| `6:35-7:00` | Staff | Click `Academics > Manage Courses`. Point at `Create Course`, then on the first visible row click `Quick view`, then `Done`. | Course catalog management, credits, semesters, enrollment status, and course details. |
| `7:00-7:20` | Staff | Click `Academics > Manage Subjects`. Point at `Create Subject`, then on the first visible row point at `Assign Teachers` or click it and return. | Subject setup, assigned teachers, and the teacher assignment workflow. |
| `7:20-8:00` | Student | Switch to the student tab. Click `Academics > Courses`. Optionally type in the course search, click `Quick view` on one course card, then click `Enroll to course` or `Enroll`. | Students browse available courses and submit enrollment requests through the portal. |
| `8:00-8:25` | Student | Click `Academics > My Courses`. | Pending or approved enrollment status and the student self-service view. |
| `8:25-8:50` | Staff | Switch back to staff. Click `Academics > Enrollment Requests`. If needed, click the `Pending` tab, then on a request click `Details` and point at `Approve Enrollment` or `Reject Enrollment`. | Staff approval and rejection workflow, pending status, and controlled enrollment management. |
| `8:50-9:25` | Teacher | Switch to the teacher tab. Click `Teaching > Assignments`. Open the first subject card. | Assignment management as part of the grading workflow. |
| `9:25-10:20` | Teacher | Click `Teaching > Grades`. Open the first subject card. On the grade entry page, point at `Save Draft Grades`. If you want to show the full workflow, also point at the final submission action for grade review. | Teachers record grades, save draft data, and prepare final submission for staff review. |
| `10:20-10:55` | Staff | Switch to staff. Click `Academics > Grade Reviews`. On the subject list click `Review`. On the review page point at `Approve`, `Reject`, and the `Rejection reason (required)` field. | Grade review, approval, rejection, and auditability. |
| `10:55-11:20` | Student | Switch to student. Click `Academics > Grades`. | Approved grades, grade breakdowns, and GPA visibility for students. |
| `11:20-12:10` | Staff | Switch to staff. Click `Finance > Manage Fees`. Point at `Create Fee`, `Remind Filtered Overdue`, `Export CSV`, `Export PDF`, and on a row click `Details`. If a row is `payment_pending`, point at `Approve Payment` and `Reject`. If a row is `paid`, point at `Receipt`. | Fee records, payment review, overdue reminders, exports, and receipt generation. |
| `12:10-12:40` | Student | Switch to student. Click `Finance > Fees`. Point at the `Cards` / `Table` view toggle and the actions `Pay Now`, `Submit Proof`, or `Payment Pending Approval` if visible. | Student fee visibility and payment status tracking. |
| `12:40-13:30` | Staff | Switch to staff. Click `Academics > Manage Timetable`. Point at `Create Entry`, filters, `Export PDF`, `Export CSV`, `Week view`, and click `Details` on one row. | Timetable creation, validation, filtering, and reporting options. |
| `13:30-14:00` | Teacher | Switch to teacher. Click `Teaching > Timetable`. | Teacher-specific weekly timetable view. |
| `14:00-14:20` | Student | Switch to student. Click `Academics > Timetable`. | Student timetable access and weekly layout. |
| `14:20-15:05` | Teacher | Switch to teacher. Click `Teaching > Mark Attendance`. Open the first subject card. On the attendance page, show the student list and attendance actions. | Digital attendance capture by teacher and subject. |
| `15:05-15:35` | Student | Switch to student. Click `Academics > Attendance`. | Personal attendance statistics and breakdowns. |
| `15:35-16:20` | Staff | Switch to staff. Click `Academics > Attendance Report`. | Course and subject summaries, low-attendance identification, and reporting. |
| `16:20-17:00` | Staff | Stay on attendance reporting or related alert controls. If low-attendance alert controls are visible, point at them and explain the queue-backed alert workflow. | Attendance alerts, threshold monitoring, and background processing support. |
| `17:00-17:25` | Staff | Click `Communication > Announcements`. Point at `Create Announcement`, filters, and on one row point at `Remind`, `Edit`, and `Delete`. | Staff announcement management, priority, audience, and reminders. |
| `17:25-17:45` | Student or teacher | Switch to student or teacher. Click `Communication > Announcements`. Open one announcement, then point at or click `Acknowledge` if available. | Role-based announcement visibility and acknowledgement. |
| `17:45-18:00` | Any logged-in role | Click `Communication > Messages`. Point at the message tabs, search, `Unread only`, `Mark as read`, `Reply`, and `Send Reply`. | Internal messaging and read-state workflow. |
| `18:00-18:20` | Any logged-in role | Click `Communication > Notifications`. Point at `Mark all as read`, the notification tabs, `Open`, and `Mark as read`. | Centralised notification center across modules. |
| `18:20-18:40` | Staff | Click `Communication > Contact Messages`. Point at filters, `Add note` / `Edit note`, and `Mark read`. | Guest contact inbox and internal follow-up notes. |
| `18:40-19:00` | Staff | Click `Communication > Feedback Messages`. Point at filters, `Mark read`, and `Mark as handled`. | Guest feedback inbox and handling status. |
| `19:00-19:20` | Staff | In the header, use the global search field. Then click `Settings` in the sidebar or from the user menu. | Search, preferences, and notification settings. |
| `19:20-19:35` | Staff | Click `Academics > Failed Jobs`. | Supporting admin monitoring and maintainability features. |
| `19:35-20:00` | Dashboard / docs | Return to `Dashboard`, or briefly show diagrams or report assets if you want a closing technical summary. | DSDM timeboxes, design artefacts, testing, deployment planning, and conclusion. |

## Exact Actions Worth Naming Out Loud

These are useful button names to mention exactly during the demo because they match the current UI:

- `Add User`
- `Add student`
- `Create Course`
- `Create Subject`
- `Assign Teachers`
- `Quick view`
- `Enroll to course`
- `Details`
- `Approve Enrollment`
- `Reject Enrollment`
- `Save Draft Grades`
- `Review`
- `Approve`
- `Reject`
- `Create Fee`
- `Approve Payment`
- `Receipt`
- `Create Entry`
- `Week view`
- `Create Announcement`
- `Remind`
- `Acknowledge`
- `Mark all as read`
- `Add note`
- `Mark as handled`

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
