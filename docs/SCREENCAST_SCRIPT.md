# University Academic Portal Screencast Script

This document provides a 15-20 minute screencast script for the University Academic Portal. It is aligned with the implemented system, the three timeboxes, and the project report structure.

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
3. Show Timebox 1: registration, student, course, subject, enrolment
4. Show Timebox 2: grades, fees, payment, assignments
5. Show Timebox 3: timetable, attendance, announcements, messages, notifications
6. End with evaluation and future improvements

## Full Time-Coded Script

| Time | What to show | What to say |
| --- | --- | --- |
| `0:00-1:10` | Guest home page | "Hello, my name is Kyaw Myo Htay, and this is my final year project, a University Academic Portal developed using Laravel, Vue.js, Inertia, Tailwind CSS, and MySQL. The aim of this project is to replace manual and spreadsheet-based university processes with a single integrated web-based platform." |
| `1:10-2:00` | Home page sections, quick navigation | "The project focuses on the major academic processes that are commonly handled manually, such as student registration, course enrolment, grades, fee management, timetables, attendance, and communication. The problem with the manual system is that it causes delays, duplicate records, data inconsistency, and difficulty in communication and reporting." |
| `2:00-3:00` | Public pages: Courses, News, About, Contact, Feedback, User Manual | "The portal also supports guest users. Public visitors can browse courses, read announcements, use the contact and feedback forms, and access the user manual. This improves accessibility and creates a single entry point for the academic portal." |
| `3:00-4:30` | Staff dashboard | "The system is role-based, with separate interfaces for staff, teachers, and students. I will begin with the staff side because it manages most core records and administrative workflows. The dashboard summarises key statistics and gives quick access to the major modules." |
| `4:30-6:10` | Staff: Users, Students, Courses, Subjects, Assign Teachers | "Timebox 1 focused on student registration and course registration. Staff can create and manage user accounts, register students, create courses and subjects, and assign teachers. These workflows replace manual forms with validated digital records. The system checks required fields, unique email addresses, unique course and subject codes, and supports search, filtering, and pagination." |
| `6:10-7:40` | Student tab: Browse Courses and My Courses, then staff Enrolments | "Students can browse available courses and submit enrolment requests through the portal. The system prevents duplicate enrolments and can detect timetable conflicts. When a student submits a request, the status becomes pending. Staff can then review, approve, or reject the request from the enrolment management page. Approved courses are then visible in the student's My Courses page." |
| `7:40-9:30` | Teacher dashboard, Assignments, Grades overview | "Timebox 2 focused on grades and fee payment, and during iterative development I also added assignment management to improve the grading workflow. Teachers can create assignments, publish them, view submissions, grade the submissions, and then use those results to support final grade submission." |
| `9:30-11:10` | Teacher grade submission, then staff grade review page, then student grades page | "Teachers can save grade data first and then submit final grades for staff review. This creates a controlled approval workflow. Staff can approve or reject grades, store reviewer information, and keep grade review logs for traceability. Once approved, the student can view the grade and GPA through the student portal. This improves both transparency and academic control compared with manual grade entry." |
| `11:10-12:40` | Staff fee management, student fee page, receipt | "The same timebox also includes fee and payment management. Staff can create fee records, update statuses, review submitted payments, track overdue fees, and generate receipts. Students can view their fee information clearly with due dates and payment status. The portal also supports Stripe checkout and webhook processing for online payment integration." |
| `12:40-14:20` | Staff timetable management, teacher timetable, student timetable | "Timebox 3 introduced timetable, attendance, and communication features. Staff can create and update timetable records with validation rules to prevent invalid times and scheduling conflicts. Teachers and students can then view their role-based timetable in a clearer weekly layout." |
| `14:20-15:45` | Teacher attendance marking, student attendance page, staff attendance report | "Teachers can record attendance by subject and date, while students can monitor their attendance statistics. Staff can view attendance reports across the system, including course and subject breakdowns. This digital process improves reporting accuracy and removes the need for paper-based attendance tracking." |
| `15:45-16:40` | Staff attendance alerts | "The portal also supports low-attendance monitoring. Staff can identify students below the attendance threshold and trigger alert workflows. This makes intervention faster and more proactive than a manual review process." |
| `16:40-18:10` | Announcements, messages, notifications, contact messages, feedback inbox | "Communication is centralised through announcements, direct messages, notifications, and guest contact and feedback forms. Staff and teachers can create announcements, and users see announcements based on their role and visibility rules. Messages and notifications improve internal communication, while contact and feedback forms give the public a structured communication channel with the university." |
| `18:10-19:10` | Dashboard or diagrams/documentation assets | "From the development perspective, the system was implemented using Agile DSDM and divided into three timeboxes. The design was supported by use case diagrams, class diagrams, sequence diagrams, ERD, sitemap, prototypes, testing evidence, deployment planning, and user manual documentation. The architecture follows the MVC structure, which helps keep the system maintainable and modular." |
| `19:10-20:00` | Final home page or dashboard | "In conclusion, the University Academic Portal successfully integrates the major academic administration processes into one platform. It improves efficiency, reduces manual errors, strengthens communication, and supports staff, teachers, students, and guests through role-based workflows. Future improvements could include advanced analytics, stronger accessibility support, MFA, mobile optimisation, and deeper integration with other university systems. Thank you for watching." |

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
