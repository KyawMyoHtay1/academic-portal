# Chapter 4: System Requirements and Design

## 4.1 Introduction

This chapter defines the requirements and design of the University Academic Portal. Building on the findings from Chapters 1 to 3, it translates the identified problems, user needs, and feasibility decisions into a structured system specification. The chapter identifies the target users, presents the functional and non-functional requirements, applies MoSCoW prioritisation, describes the timebox plan, outlines the main design artefacts, and discusses project risk management.

The purpose of this chapter is not only to list features, but also to show how the system was planned in a controlled and justifiable way before and during implementation. Because the portal supports multiple user roles and sensitive academic workflows, the design needed to be structured, role-aware, and aligned with the timeboxed delivery approach selected in Chapter 3.

## 4.2 Target Users

The University Academic Portal is designed for four main user groups. Each group has different goals, different levels of technical confidence, and different access rights.

### 4.2.1 Students

Students are the main academic end users of the portal. Their typical age range is approximately 18 to 25, and their technical ability is expected to range from basic to intermediate. Students need to perform tasks such as viewing their profile, requesting course enrolment, checking grades, tracking attendance, viewing timetables, reviewing fees, submitting payments, submitting assignments, reading announcements, and receiving notifications.

The interface for students should therefore be clear, simple, and task-oriented. Students should not need advanced technical knowledge to complete routine academic tasks.

### 4.2.2 Teachers

Teachers are responsible for academic delivery and record-related tasks. Their technical ability is assumed to be intermediate to advanced, although they may still require straightforward interfaces for repetitive workflows. Teachers use the portal to view assigned courses and subjects, submit grades, manage assignments, record attendance, view timetables, publish announcements, and communicate with students.

Because teacher tasks can involve large data views and repeated form actions, the design must support efficient workflows, clear validation, and quick access to teaching-related records.

### 4.2.3 Administrative Staff

Administrative staff are the main management users of the portal. They are responsible for user management, student records, course and subject administration, enrolment approvals, grade approval workflows, fee administration, payment review, timetable management, attendance reporting, announcements, and communication inboxes.

This user group requires broader system access than students or teachers. The interface therefore needs to support filtering, management views, status tracking, and reporting functions while preserving role-based security.

### 4.2.4 Guests and Visitors

Guests are unauthenticated users who access the public-facing parts of the portal. Their role is limited compared with authenticated users, but they are still important because they interact with public information, such as general portal content, courses, announcements, contact forms, feedback forms, and support pages.

For this group, the main design concern is clarity, ease of access, and safe handling of public submissions.

## 4.3 Functional Requirements

The functional requirements were organised around the DSDM timebox plan used for the project. This keeps the requirements aligned with implementation and later testing.

## 4.3.1 Timebox 1: Student Registration and Course Registration

Timebox 1 focuses on the core identity, student, and enrolment foundations of the portal.

### A. User and Access Management

The system shall:

1. Allow users to create accounts and sign in securely.
2. Support password reset and e-mail verification.
3. Allow staff to create, update, search, and delete user records.
4. Support role-based user assignment for student, teacher, and staff accounts.
5. Provide user settings and notification preferences for authenticated users.
6. Support global search with role-based result visibility.

### B. Student Management

The system shall:

1. Allow staff to create, update, search, and delete student records.
2. Automatically generate unique student numbers.
3. Store student personal details, programme data, and supporting documents.
4. Allow students to view and partially update their own profile.
5. Restrict sensitive profile fields from unauthorised self-editing.

### C. Course and Subject Management

The system shall:

1. Allow staff to create, update, search, and delete courses.
2. Allow staff to create, update, search, and delete subjects.
3. Allow staff to assign teachers to courses and subjects.
4. Validate uniqueness of course codes and subject codes.

### D. Course Registration Workflow

The system shall:

1. Allow students to request enrolment in available courses.
2. Prevent duplicate enrolment and duplicate pending requests.
3. Detect timetable conflicts before enrolment approval.
4. Allow students to request course withdrawal.
5. Allow staff to approve or reject enrolment and withdrawal requests.
6. Allow students to view their active and pending course registrations.
7. Allow staff to manage enrolment requests through dedicated review screens.

## 4.3.2 Timebox 2: Grades, Fees, Payments, and Assignment Support

Timebox 2 extends the portal into academic performance and finance workflows.

### A. Grade Management

The system shall:

1. Allow teachers to record and submit grades for their assigned subjects.
2. Allow staff to review, approve, or reject grades.
3. Maintain a review log for grade actions.
4. Allow students to view approved grades only.
5. Calculate and display GPA using approved grades.
6. Support grade filtering and search by relevant academic fields.
7. Display grade-related summaries for student, teacher, and staff views.

### B. Fee and Payment Management

The system shall:

1. Allow staff to create, update, search, and delete fee records.
2. Allow students to view their fee records and payment status.
3. Allow students to submit payment confirmation manually when required.
4. Allow staff to approve or reject payment confirmations.
5. Support Stripe checkout and webhook-based payment updates.
6. Generate receipts for paid fees.
7. Track overdue fees and late-payment status.
8. Notify users when fee status changes.

### C. Assignment Workflow

Although assignment management was not part of the original proposal's core list, it was included as a supporting academic workflow because it strengthens the grade management process.

The system shall:

1. Allow teachers to create, update, publish, and delete assignments.
2. Allow students to view assignments for enrolled subjects.
3. Allow students to submit assignment files.
4. Allow teachers to review and grade submissions.
5. Support computed-grade logic based on assignment performance where appropriate.

## 4.3.3 Timebox 3: Timetable, Attendance, and Communication

Timebox 3 completes the main academic coordination and communication features.

### A. Timetable Management

The system shall:

1. Allow staff to create, update, and delete timetable entries.
2. Prevent timetable conflicts for overlapping subject schedules.
3. Allow students to view their timetable based on approved enrolments.
4. Allow teachers to view timetables for their assigned subjects.
5. Support timetable export and timetable update notifications where relevant.

### B. Attendance Management

The system shall:

1. Allow teachers to record attendance for students in assigned subjects.
2. Prevent duplicate attendance records for the same subject, student, and date.
3. Allow students to view attendance summaries and recent attendance history.
4. Provide attendance reporting for staff.
5. Identify low-attendance cases and support alert generation.

### C. Communication and Notification Management

The system shall:

1. Allow staff and teachers to create and manage announcements.
2. Allow users to read and acknowledge announcements where required.
3. Allow authenticated users to send and receive internal messages.
4. Provide notification lists and read/unread management.
5. Allow guests to submit contact and feedback forms.
6. Allow staff to manage contact and feedback inboxes.

## 4.4 MoSCoW Prioritisation

MoSCoW prioritisation was used to classify requirements according to delivery importance within the timeboxed development model.

### 4.4.1 Must Have

These were essential to meeting the proposal scope and core project aim.

1. Authentication and role-based access.
2. Student record management.
3. Course and subject management.
4. Course enrolment and withdrawal workflow.
5. Grade submission and staff approval.
6. Student grade viewing and GPA display.
7. Fee record management and payment status handling.
8. Timetable management and timetable access.
9. Attendance recording and attendance reporting.
10. Announcements, messaging, and notifications.

### 4.4.2 Should Have

These requirements significantly improve the system and support realistic academic workflows.

1. Global search.
2. User settings and notification preferences.
3. PDF receipt generation.
4. Late fee tracking.
5. Attendance export and timetable export.
6. Public contact and feedback handling.
7. Assignment workflow as a supporting academic module.
8. Audit logs for grades, enrolments, and payments.

### 4.4.3 Could Have

These features improve usability or traceability but are lower priority than the core functions.

1. Automated low-attendance reminder scheduling.
2. Public statistics and richer guest-facing pages.
3. Additional management dashboards and convenience filters.
4. More advanced notification reminder actions.
5. Queue management views and operational support tooling.

### 4.4.4 Won't Have for the Current Project

These features were outside the scope of the current implementation and are treated as future possibilities.

1. Full library management integration.
2. Native mobile application.
3. Advanced analytics and forecasting dashboards.
4. Course prerequisite engine.
5. Official transcript generation as a full institutional record service.
6. Multi-provider payment ecosystem beyond the implemented payment workflow.

## 4.5 Non-Functional Requirements

The portal also requires a clear set of non-functional requirements because academic systems must be secure, reliable, and usable in addition to being functionally correct.

### 4.5.1 Usability

The system shall provide an interface that is clear and easy to use for students, teachers, and staff. It shall use familiar terminology, consistent navigation, and understandable feedback messages. Forms shall provide validation and action status messages so that users can recover easily from mistakes.

### 4.5.2 Interface and Accessibility

The portal shall support responsive use across common desktop and mobile browser environments. Layouts, labels, spacing, and controls shall remain consistent across pages. The interface should also support reasonable accessibility practices, including readable text, clear contrast, keyboard-friendly interaction, and logically structured pages.

### 4.5.3 Security

The system shall enforce role-based access control and protect user credentials through secure hashing and authentication controls. It shall validate user inputs, restrict unauthorised access, and protect sensitive workflows such as grade review, payment processing, and record management. Public forms and authentication workflows should also use rate limiting and anti-abuse controls where necessary.

### 4.5.4 Maintainability

The system shall use a modular and layered structure so that future changes can be made without excessive rework. Clear naming, version control, framework conventions, and separated business logic should support easier maintenance and future extension.

### 4.5.5 Reliability and Recovery

The portal shall store academic data in a controlled database structure and support recovery planning through backup and operational safeguards. Important workflows, such as enrolment, grade updates, and payment status changes, should maintain data consistency and be recoverable where possible.

### 4.5.6 Performance

The system shall provide acceptable response times for normal academic use, including data listing, search, filtering, form submission, and role-based dashboards. While the project is not designed as a high-scale enterprise deployment, the implementation should remain responsive for realistic student, teacher, and staff workloads.

## 4.6 System Design Overview

### 4.6.1 Architectural Design

The portal follows a Laravel MVC architecture with an Inertia.js and Vue frontend. This combines:

1. Model layer for database interaction and business data.
2. Controller layer for request handling, validation, and workflow control.
3. Vue-based presentation layer for interactive user interfaces.

This architecture is appropriate because it supports separation of concerns, maintainability, and efficient web application development. It also matches the project justification made in Chapter 3.

### 4.6.2 Whole-System Use Case Design

The whole-system use case design models the interactions of the four main actors: Guest, Student, Teacher, and Administrator/Staff. The use case model shows that students interact mainly with enrolment, grades, fees, timetable, attendance, announcements, messages, and notifications. Teachers interact mainly with grades, assignments, attendance, timetables, and announcements. Staff interact with management, approvals, and reporting workflows across the system.

This whole-system use case is represented in [WholeSystem_UseCaseDiagram.plantuml](d:/university-portal/academic-portal/usecase/WholeSystem_UseCaseDiagram.plantuml).

### 4.6.3 Database Design

The database design is relational and centred on the core academic entities:

1. `users`
2. `students`
3. `courses`
4. `subjects`
5. `course_student` (enrolments)
6. `grades`
7. `fees`
8. `assignments`
9. `assignment_submissions`
10. `timetables`
11. `attendances`
12. `announcements`
13. `messages`
14. `notifications`
15. supporting logs and state tables

This structure supports role-based academic workflows while maintaining referential integrity and traceability. The overall ERD is represented in [AcademicPortal_ERD.plantuml](d:/university-portal/academic-portal/class_diagram/erd/AcademicPortal_ERD.plantuml).

### 4.6.4 Class Design

The class design was separated by timebox to keep each functional area manageable.

1. Timebox 1 class design focuses on users, students, courses, subjects, teacher assignment, and enrolment relationships.
2. Timebox 2 class design focuses on grades, grade review logs, fees, payments, assignments, and assignment submissions.
3. Timebox 3 class design focuses on timetables, attendances, low-attendance alert state, announcements, messages, notifications, contact messages, and feedback messages.

The class artefacts are available in:

1. [Timebox1_Class.plantuml](d:/university-portal/academic-portal/class_diagram/Timebox1_Class.plantuml)
2. [Timebox2_Class.plantuml](d:/university-portal/academic-portal/class_diagram/Timebox2_Class.plantuml)
3. [Timebox3_Class.plantuml](d:/university-portal/academic-portal/class_diagram/Timebox3_Class.plantuml)

### 4.6.5 Sequence Design

Sequence diagrams were prepared for major workflows in order to show how requests move across interface, controller, service, model, and persistence layers. Key sequence diagrams include:

1. student registration
2. grades and computed-grade flow
3. fee and payment flow
4. attendance recording
5. timetable management
6. communication workflow

These artefacts help explain process-level behaviour and are stored in the [sequence](d:/university-portal/academic-portal/sequence) directory.

### 4.6.6 Sitemap and Navigation Design

The portal uses role-based navigation and separates public pages from authenticated academic pages. Public users can access informational pages and form-based contact/feedback features. Authenticated users are directed to dashboards and role-specific workflows. This structure supports usability and security by limiting access according to role while keeping common features such as profile, settings, notifications, and announcements easy to reach.

## 4.7 Timebox Plan

The project was planned using three development timeboxes aligned with the DSDM approach.

| Timebox | Focus | Planned Dates | Main Outputs |
| --- | --- | --- | --- |
| Timebox 1 | Student registration and course registration | 16 December 2025 to 11 January 2026 | user management, student management, course and subject management, enrolment workflow, related design artefacts and testing |
| Timebox 2 | Grades, fees, payments, and assignment support | 11 January 2026 to 9 February 2026 | grade workflow, fee and payment workflow, assignment support, related design artefacts and testing |
| Timebox 3 | Timetable, attendance, and communication | 9 February 2026 to 13 March 2026 | timetable, attendance, low-attendance support, announcements, messages, notifications, guest contact and feedback, related design artefacts and testing |

This timebox structure supports incremental implementation, manageable review cycles, and a clear mapping between requirements, design, implementation, and testing.

## 4.8 Risk Management

Risk management was included in the design stage to reduce disruption to schedule, quality, and system stability.

### 4.8.1 Key Risks

The most relevant project risks were:

1. application defects and integration issues
2. security weaknesses and unauthorised access
3. data loss or inconsistent data
4. schedule overrun and underestimation
5. changing requirements
6. limited experience with parts of the stack
7. low user adoption or training gaps

### 4.8.2 Risk Matrix

| Risk | Probability | Impact | Proactive action | Reactive action |
| --- | --- | --- | --- | --- |
| Application bugs and integration errors | High | High | coding standards, incremental testing, sandbox payment testing | debug, hotfix, retest affected modules |
| Security weakness or data breach | Low | High | RBAC, validation, hashing, rate limiting, secure coding | revoke access, patch issue, review logs, restore trusted state |
| Data loss or corruption | Low | High | backups, transactions, cautious schema changes | restore backup and reconcile affected records |
| Changing requirements | High | Medium | timeboxing, prioritisation, documented scope control | re-prioritise and defer lower-value items |
| Schedule overrun | Medium | Medium | break work into smaller tasks and track progress | re-plan and reduce low-priority scope |
| Limited experience with technology | Medium | Medium | documentation review, phased learning, smaller increments | refactor weak areas and seek targeted guidance |
| Low user adoption or training gaps | Medium | Medium | clear UI, guides, demonstrations, user manuals | gather feedback and improve workflow clarity |

### 4.8.3 Critical Success Factors

The critical success factors for the current project are:

1. a controlled scope and realistic timebox plan
2. consistent alignment between requirements, design, implementation, and testing
3. secure and accurate handling of academic data
4. usable interfaces for the three main authenticated roles
5. maintainable code and clear documentation
6. effective testing and issue resolution

## 4.9 Chapter Summary

This chapter has defined the requirements and design of the University Academic Portal in a structured way. It identified the target users, grouped the functional requirements by timebox, applied MoSCoW prioritisation, and defined the key non-functional requirements that support usability, security, maintainability, and reliability.

The chapter also described the main design artefacts, including the architectural model, use case design, ERD, class diagrams, sequence diagrams, and navigation structure. Finally, it outlined the timebox plan and the main project risks that needed to be managed during development.

These requirements and design decisions provide the direct foundation for Chapter 5, which explains how the portal was implemented and tested across the planned development stages.
