# Appendix

This appendix consolidates the supporting material for the University Academic Portal. It is intended to complement Chapters 3 to 7 by summarising the most important use cases, design artefacts, implementation modules, test evidence, user documentation, and requirement-gathering material. Detailed screenshots, exported diagrams, and Google Forms images can remain in the Word appendix, while this appendix provides the cleaner academic narrative and index.

## Appendix A: Key Use Case Descriptions

The full use case coverage of the system is represented in the UML artefacts under the `usecase` directory. This section presents the most important representative use cases from the three DSDM timeboxes so that the appendix supports the implementation and evaluation chapters without becoming repetitive.

### A.1 Create Account and Login

| Field | Description |
| --- | --- |
| Primary actors | Guest, Student, Teacher, Staff |
| Goal | To create an account where permitted and gain secure access to the portal according to role. |
| Preconditions | The portal is available through a web browser; the user has valid registration details if account creation is allowed. |
| Main flow | 1. The user opens the registration or login page. 2. The system validates the submitted credentials and any required terms acceptance. 3. If the credentials are valid, the system creates the account or starts the authenticated session. 4. The user is redirected to the role-based dashboard. |
| Alternative flow | Invalid or incomplete data causes validation errors and the account or session is not created. |
| Postcondition | The user account exists or the session is established successfully. |

### A.2 Manage Student Registration and Profile

| Field | Description |
| --- | --- |
| Primary actors | Staff, Student |
| Goal | To maintain accurate student records and allow controlled profile updates. |
| Preconditions | Staff is authenticated with the correct permissions; the student record exists or is being created. |
| Main flow | 1. Staff creates or updates a student record. 2. The portal validates identity, course, and related academic information. 3. The record is stored in the system and becomes available to authorised users. 4. Students can later view or update permitted profile fields. |
| Alternative flow | Duplicate or invalid academic details are rejected until corrected. |
| Postcondition | A valid student record is created or updated and can be used by other modules. |

### A.3 Manage Course Registration

| Field | Description |
| --- | --- |
| Primary actors | Student, Staff |
| Goal | To submit, review, approve, reject, or withdraw course enrolment requests. |
| Preconditions | The student is authenticated and linked to a course; the target course or subject is available for enrolment. |
| Main flow | 1. The student views available courses or current enrolments. 2. The student submits an enrolment or withdrawal request. 3. The system validates eligibility and records the request. 4. Staff reviews the request and approves or rejects it. 5. Status logs are updated for traceability. |
| Alternative flow | Requests that fail validation or conflict with business rules remain pending or are rejected with feedback. |
| Postcondition | The enrolment status is updated and visible to authorised users. |

### A.4 Manage Grades and Grade Review

| Field | Description |
| --- | --- |
| Primary actors | Teacher, Staff, Student |
| Goal | To record, review, approve, and publish student grades in a controlled workflow. |
| Preconditions | The student is enrolled in the relevant subject; authorised staff and teachers are authenticated. |
| Main flow | 1. The teacher enters assessment marks or draft grades. 2. The system calculates or validates grade values. 3. Staff reviews the submitted grades and approves or returns them for correction. 4. Approved grades are published for student viewing. 5. Review logs record workflow actions. |
| Alternative flow | Missing marks, invalid values, or returned submissions require correction before publication. |
| Postcondition | The grade is stored with the correct status and audit trail. |

### A.5 Manage Fees and Online Payment

| Field | Description |
| --- | --- |
| Primary actors | Student, Staff |
| Goal | To manage student fee records and allow secure online payment processing. |
| Preconditions | A fee record exists for the student; payment configuration is available. |
| Main flow | 1. Staff creates or updates a fee item. 2. The student views outstanding fees and chooses to pay. 3. The portal creates a checkout session and redirects the user to the payment provider. 4. The payment result is returned by webhook processing. 5. The system updates the fee status, stores payment evidence, and makes a receipt available. |
| Alternative flow | Failed or cancelled payments leave the fee unpaid and no completion receipt is issued. |
| Postcondition | The fee record reflects the latest verified payment status. |

### A.6 Manage Timetable and Attendance

| Field | Description |
| --- | --- |
| Primary actors | Staff, Teacher, Student |
| Goal | To publish timetable information and record attendance for academic monitoring. |
| Preconditions | Courses, subjects, and users already exist in the system. |
| Main flow | 1. Staff creates or updates timetable entries. 2. Teachers view their timetable and record student attendance for scheduled sessions. 3. Students view their timetable and attendance summary. 4. The system calculates attendance rates and can trigger low-attendance alerts. |
| Alternative flow | Invalid timetable clashes or attendance entries are rejected until corrected. |
| Postcondition | Timetable and attendance information is available for reporting and monitoring. |

### A.7 Manage Communication and Support

| Field | Description |
| --- | --- |
| Primary actors | Guest, Student, Teacher, Staff |
| Goal | To support academic communication through announcements, messages, notifications, contact forms, and feedback forms. |
| Preconditions | The relevant user has appropriate access, or a guest accesses a public contact route. |
| Main flow | 1. Staff or teachers publish announcements or send messages. 2. Students and staff receive notifications within the portal. 3. Guests or users submit enquiries or feedback using the contact and feedback forms. 4. Staff reviews and responds to submitted messages where required. |
| Alternative flow | Invalid messages or incomplete submissions are rejected through validation. |
| Postcondition | Communication records are stored and visible to authorised users. |

## Appendix B: Design Artefacts and Key Class Definitions

### B.1 Design Artefact Index

| Artefact | Purpose | Supporting file |
| --- | --- | --- |
| Whole-system use case diagram | Shows the main actors and use cases across the complete portal scope. | `usecase/WholeSystem_UseCaseDiagram.plantuml` |
| Timebox 1 use case diagram | Represents registration, student, course, subject, and enrolment workflows. | `usecase/Timebox1_UseCaseDiagram.plantuml` |
| Timebox 2 use case diagram | Represents grades, fees, payments, and assignment workflows. | `usecase/Timebox2_UseCaseDiagram.plantuml` |
| Timebox 3 use case diagram | Represents timetable, attendance, and communication workflows. | `usecase/Timebox3_UseCaseDiagram.plantuml` |
| Entity relationship diagram | Defines the main data structure for the academic portal. | `class_diagram/erd/AcademicPortal_ERD.plantuml` |
| Timebox 1 class diagram | Shows the domain classes and relationships for the first implementation stage. | `class_diagram/Timebox1_Class.plantuml` |
| Timebox 2 class diagram | Shows the domain classes and relationships for the second implementation stage. | `class_diagram/Timebox2_Class.plantuml` |
| Timebox 3 class diagram | Shows the domain classes and relationships for the third implementation stage. | `class_diagram/Timebox3_Class.plantuml` |
| Registration sequence diagram | Demonstrates the interaction flow for student registration. | `sequence/Timebox1_StudentRegistrationProcess.plantuml` |
| Grade workflow sequence diagram | Demonstrates the interaction flow for grade processing. | `sequence/Timebox2_GradeSequence.plantuml` |
| Fee workflow sequence diagram | Demonstrates the interaction flow for fee processing. | `sequence/Timebox2_FeeSequence.plantuml` |
| Assignment workflow sequence diagram | Demonstrates the interaction flow for assignment handling. | `sequence/Timebox2_AssignmentSequence.plantuml` |
| Attendance workflow sequence diagram | Demonstrates the interaction flow for attendance recording and monitoring. | `sequence/Timebox3_AttendanceSequence.plantuml` |
| Communication workflow sequence diagram | Demonstrates the interaction flow for messaging and communication. | `sequence/Timebox3_CommunicationSequence.plantuml` |
| Timetable workflow sequence diagram | Demonstrates the interaction flow for timetable management. | `sequence/Timebox3_TimetableSequence.plantuml` |

### B.2 Key Class and Model Definitions

The project includes a larger set of models than can be discussed individually in the main dissertation chapter. Table B.2 summarises the most important classes and data entities that support the portal.

| Class or model | Role in the system | Supporting file |
| --- | --- | --- |
| `User` | Core authenticated identity for student, teacher, and staff access. | `app/Models/User.php` |
| `Student` | Stores student-specific academic and profile information. | `app/Models/Student.php` |
| `Course` | Represents academic courses and their administrative data. | `app/Models/Course.php` |
| `Subject` | Represents subjects linked to courses and teaching allocation. | `app/Models/Subject.php` |
| `Grade` | Stores grade information and publication status. | `app/Models/Grade.php` |
| `GradeReviewLog` | Records review actions for grade workflow traceability. | `app/Models/GradeReviewLog.php` |
| `Fee` | Stores student fee records, balances, and payment state. | `app/Models/Fee.php` |
| `FeeStatusLog` | Records changes to fee status for audit purposes. | `app/Models/FeeStatusLog.php` |
| `Assignment` | Represents published coursework tasks and related metadata. | `app/Models/Assignment.php` |
| `AssignmentSubmission` | Stores student assignment submissions and submission state. | `app/Models/AssignmentSubmission.php` |
| `Timetable` | Stores scheduled teaching sessions and timetable entries. | `app/Models/Timetable.php` |
| `Attendance` | Stores attendance records for students in scheduled sessions. | `app/Models/Attendance.php` |
| `LowAttendanceAlertState` | Stores alert state to avoid duplicate attendance warnings. | `app/Models/LowAttendanceAlertState.php` |
| `Announcement` | Stores public or role-based academic announcements. | `app/Models/Announcement.php` |
| `AnnouncementRead` | Tracks whether announcements have been read by users. | `app/Models/AnnouncementRead.php` |
| `Message` | Supports internal user-to-user messaging. | `app/Models/Message.php` |
| `ContactMessage` | Stores enquiries submitted through the contact form. | `app/Models/ContactMessage.php` |
| `FeedbackMessage` | Stores user or guest feedback submitted to the portal. | `app/Models/FeedbackMessage.php` |
| `StripeWebhookEvent` | Stores payment webhook records related to Stripe integration. | `app/Models/StripeWebhookEvent.php` |
| `EnrollmentStatusLog` | Records changes to course enrolment request status. | `app/Models/EnrollmentStatusLog.php` |

## Appendix C: Implementation Module Summary

The original appendix listed generic function names such as `manageStudents()` and `manageTimetable()`. For dissertation quality, it is stronger to map the implemented modules to the actual Laravel controllers and service-layer classes used in the system.

| Module | Main implementation files | Purpose |
| --- | --- | --- |
| Authentication and profile | `app/Http/Controllers/Auth`, `app/Http/Controllers/ProfileController.php`, `app/Http/Controllers/StudentProfileController.php`, `app/Http/Controllers/SettingsController.php` | Handles user registration, login, password/session management, terms acceptance, and profile maintenance. |
| Student, user, course, and subject administration | `app/Http/Controllers/StudentController.php`, `app/Http/Controllers/StaffUserController.php`, `app/Http/Controllers/StaffCourseController.php`, `app/Http/Controllers/StaffSubjectController.php`, `app/Http/Controllers/CourseController.php` | Supports administrative management of users, students, courses, and subjects. |
| Course enrolment workflow | `app/Http/Controllers/CourseRegistrationController.php`, `app/Http/Controllers/MyCoursesController.php`, `app/Http/Controllers/StaffEnrollmentController.php`, `app/Services/EnrollmentService.php` | Supports student enrolment requests, review by staff, approval or rejection, withdrawal, and status logging. |
| Grade management | `app/Http/Controllers/TeacherGradesController.php`, `app/Http/Controllers/StaffGradesController.php`, `app/Http/Controllers/StudentGradesController.php`, `app/Services/SubjectGradeCalculator.php` | Supports draft grades, review workflow, calculation logic, approval, and student result viewing. |
| Fees and payment | `app/Http/Controllers/StudentFeeController.php`, `app/Http/Controllers/StaffFeeController.php`, `app/Http/Controllers/PaymentController.php`, `app/Services/PaymentService.php` | Supports fee creation, payment checkout, webhook verification, receipt generation, and status tracking. |
| Assignment management | `app/Http/Controllers/TeacherAssignmentController.php`, `app/Http/Controllers/StudentAssignmentController.php` | Supports assignment publishing, student submission, and assignment-related workflow handling. |
| Timetable management | `app/Http/Controllers/StaffTimetableController.php`, `app/Http/Controllers/TeacherTimetableController.php`, `app/Http/Controllers/StudentTimetableController.php` | Supports creation, update, and role-based viewing of timetable entries. |
| Attendance and alerts | `app/Http/Controllers/TeacherAttendanceController.php`, `app/Http/Controllers/StudentAttendanceController.php`, `app/Http/Controllers/StaffAttendanceReportController.php`, `app/Http/Controllers/StaffAttendanceAlertsController.php`, `app/Jobs/SendLowAttendanceAlertsJob.php` | Supports attendance recording, monitoring, reporting, and low-attendance notifications. |
| Communication and support | `app/Http/Controllers/TeacherAnnouncementController.php`, `app/Http/Controllers/StaffAnnouncementController.php`, `app/Http/Controllers/MessageController.php`, `app/Http/Controllers/NotificationController.php`, `app/Http/Controllers/ContactController.php`, `app/Http/Controllers/FeedbackController.php`, `app/Http/Controllers/StaffContactMessageController.php`, `app/Http/Controllers/StaffFeedbackMessageController.php` | Supports announcements, internal messaging, notifications, public contact forms, and feedback management. |
| Dashboard, search, and reporting support | `app/Http/Controllers/DashboardController.php`, `app/Http/Controllers/SearchController.php`, `app/Services/DashboardStatsService.php` | Supports dashboard statistics, search features, and reporting-oriented summaries for portal users. |

## Appendix D: Test Evidence

Testing evidence was used in Chapter 7 to support the claim that the portal was functionally verified within project scope. The appendix should present representative test scripts and quality evidence rather than long raw output dumps.

| Timebox | Representative test evidence | Purpose |
| --- | --- | --- |
| Timebox 1: registration, students, courses, subjects, and enrolment | `tests/Feature/CourseEnrollmentTest.php`, `tests/Feature/EnrollmentReviewWorkflowTest.php`, `tests/Feature/AdminCourseManagementTest.php`, `tests/Feature/SubjectManagementTest.php`, `tests/Feature/StudentRouteAccessTest.php`, `tests/Feature/RoleAccessPolicyTest.php` | Verifies access control, course administration, subject administration, and the enrolment request workflow. |
| Timebox 2: grades, fees, payments, and assignments | `tests/Feature/GradeReviewWorkflowTest.php`, `tests/Feature/TeacherFinalGradeSubmissionTest.php`, `tests/Feature/TeacherGradeDraftOwnershipTest.php`, `tests/Feature/PaymentWebhookTest.php`, `tests/Feature/StaffEnrollmentAndFeesRegressionTest.php`, `tests/Unit/SubjectGradeCalculatorTest.php`, `tests/Unit/GradeLetterGradeTest.php` | Verifies the grade review lifecycle, fee workflows, payment handling, and grade calculation logic. |
| Timebox 3: timetable, attendance, and communication | `tests/Feature/AttendanceAccessTest.php`, `tests/Feature/StaffAttendanceReportTest.php`, `tests/Unit/SendLowAttendanceAlertsJobTest.php`, `tests/Feature/TeacherCoursesViewTest.php` | Verifies attendance access, reporting, alert generation, and timetable-related academic views. |
| Continuous integration and quality checks | `.github/workflows/ci.yml`, `README.md`, `check.ps1`, `check.sh` | Shows that style checks, backend tests, and frontend build verification were integrated into the project workflow. |

## Appendix E: User Manual

The full user manual for the University Academic Portal is provided as a separate PDF and should remain attached to the final submission package. It is also exposed through the guest manual routes implemented in the portal.

| Evidence item | Purpose | Supporting file |
| --- | --- | --- |
| User manual PDF | Provides role-based instructions for using the main portal features. | `public/docs/University_Academic_Portal_User_Manual.pdf` |
| Manual view route | Allows the manual to be viewed in the browser. | `routes/web.php` |
| Manual download route | Allows the manual to be downloaded as a PDF. | `routes/web.php` |

The manual should cover the following:

1. portal overview and login guidance
2. student actions such as course registration, fee payment, assignment submission, timetable viewing, attendance viewing, and results access
3. teacher actions such as grade submission, assignment management, timetable use, and attendance recording
4. staff actions such as course administration, enrolment review, fee management, timetable setup, attendance reporting, and communication support
5. troubleshooting and support guidance

## Appendix F: User Questionnaire

Requirement gathering for the project used a Google Forms questionnaire to collect user views before the detailed design and implementation stages. The screenshots of the form and its response views can remain in the Word appendix as figures, while this section explains their academic purpose more clearly.

The questionnaire was used to gather evidence about:

1. the respondent role or stakeholder type
2. problems experienced with the current academic process
3. perceived importance of registration, grades, timetable, fees, attendance, and communication features
4. expectations relating to usability, accessibility, and security
5. preferences for notifications and portal support features
6. open comments that informed the final requirement set

The figures labelled `Figure F.1` to `Figure F.16` should therefore be retained as visual evidence of the questionnaire instrument and response summaries, but they should be accompanied by a short explanatory paragraph in the final Word document linking them back to the feasibility and requirement analysis discussed in Chapter 3.
