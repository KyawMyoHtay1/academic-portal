# 5.1.6 Functional Testing
## Timebox 1 - Manage Student Registration and Course Registration Process

### 5.1.6.1 Scope
Functional testing was executed to verify authentication, user management, student management, course and subject management, and enrolment workflow behavior. The tests cover validation rules, role-based permissions, CRUD operations, business constraints, and end-to-end process transitions.

### 5.1.6.2 Coverage Summary Table
| Module ID | Module Name | TC Range | Total Test Cases |
|---|---|---|---|
| M1 | User Registration (Public) | TC1.1-TC1.6 | 6 |
| M2 | User Registration (Admin) | TC2.1-TC2.7 | 7 |
| M3 | User Login | TC3.1-TC3.6 | 6 |
| M4 | User Management | TC4.1-TC4.5 | 5 |
| M5 | Password Management | TC5.1-TC5.3 | 3 |
| M6 | User Settings / Preferences | TC6.1-TC6.2 | 2 |
| M7 | Global Search | TC7.1-TC7.2 | 2 |
| M8 | Student Registration | TC8.1-TC8.5 | 5 |
| M9 | Student Management | TC9.1-TC9.5 | 5 |
| M10 | Student Self Profile | TC10.1-TC10.3 | 3 |
| M11 | Course Management | TC11.1-TC11.6 | 6 |
| M12 | Subject Management | TC12.1-TC12.5 | 5 |
| M13 | Course Enrolment | TC13.1-TC13.9 | 9 |

**Total Functional Test Cases: 64**

### 5.1.6.3 Test Design Technique
Test cases were designed using equivalence partitioning, boundary value analysis, negative testing, role-based access testing, and workflow testing to cover both normal and exceptional flows.

### 5.1.6.4 Detailed Test Plan
| TC ID | Scenario | Preconditions | Input Data | Test Steps | Expected Result |
|---|---|---|---|---|---|
| TC1.1 | Required fields validation | Public user opens the registration page. | Submit Register with name, email, or password blank | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation message for empty required field(s) |
| TC1.2 | Email format validation | Public user opens the registration page. | Submit with email missing '@' or invalid format | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Invalid email format message |
| TC1.3 | Password confirmation | Public user opens the registration page. | Submit with password and confirmation mismatch | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Passwords must match message |
| TC1.4 | Password strength | Public user opens the registration page. | Submit with weak password (e.g. too short) | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Password strength requirement message |
| TC1.5 | Duplicate email | Public user opens the registration page. | Submit with existing email | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Email already taken message |
| TC1.6 | Successful registration | Public user opens the registration page. | Fill valid data, accept terms, complete reCAPTCHA (if enabled), click Register | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | User created, logged in, redirected to dashboard |
| TC2.1 | Required fields and role | Staff user is logged in and opens user management. | Submit with name, email, or role blank | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation message |
| TC2.2 | Email format and uniqueness | Staff user is logged in and opens user management. | Submit with invalid or duplicate email | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation/duplicate message |
| TC2.3 | Photo upload | Staff user is logged in and opens user management. | Upload file other than jpeg/jpg/png or > 2MB | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Invalid file type/size message |
| TC2.4 | Successful create | Staff user is logged in and opens user management. | Fill valid data, select role, click Add | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | User created, success message |
| TC2.5 | Update user | Staff user is logged in and opens user management. | Edit user, change name/email (unique), click Update | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | User updated, success message |
| TC2.6 | Delete confirmation | Staff user is logged in and opens user management. | Click Delete, confirm dialog | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | User deleted, related records cascade |
| TC2.7 | Search and filter | Staff user is logged in and opens user management. | Search by name/email/role, use role tabs | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Filtered, paginated results (10 per page) |
| TC3.1 | Required fields | Login page is available; valid and invalid credential sets are prepared. | Submit with email or password blank | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation message |
| TC3.2 | Invalid credentials | Login page is available; valid and invalid credential sets are prepared. | Submit with wrong email/password | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Incorrect credentials message |
| TC3.3 | Rate limiting | Login page is available; valid and invalid credential sets are prepared. | Submit wrong credentials 5+ times (same email+IP) | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Throttle/too many attempts message |
| TC3.4 | Remember Me | Login page is available; valid and invalid credential sets are prepared. | Login with Remember Me checked | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Session persists after browser close |
| TC3.5 | Successful login | Login page is available; valid and invalid credential sets are prepared. | Submit valid credentials | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Redirect to role-based dashboard |
| TC3.6 | reCAPTCHA (if enabled) | Login page is available; valid and invalid credential sets are prepared. | Submit without completing reCAPTCHA | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | reCAPTCHA verification required message |
| TC4.1 | Update validation | Staff user with management permission opens the user list page. | Update user with invalid/duplicate email | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation message |
| TC4.2 | Photo validation on update | Staff user with management permission opens the user list page. | Upload invalid photo type/size | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation message |
| TC4.3 | Delete with confirmation | Staff user with management permission opens the user list page. | Click Delete, confirm | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | User deleted, cascade to related records |
| TC4.4 | Search by name, email, role | Staff user with management permission opens the user list page. | Enter keyword, apply filters | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Matching results displayed |
| TC4.5 | Role filter tabs | Staff user with management permission opens the user list page. | Select All / Students / Teachers / Staff | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Filtered list, paginated |
| TC5.1 | Forgot password | User account exists; reset mail service and token routes are enabled. | Enter email, request reset | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Reset email sent (or success message) |
| TC5.2 | Reset with token | User account exists; reset mail service and token routes are enabled. | Open reset link, enter new password with confirmation | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Password updated |
| TC5.3 | Update password (authenticated) | User account exists; reset mail service and token routes are enabled. | Logged-in user changes password with confirmation | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Password updated, success message |
| TC6.1 | Display defaults | Authenticated user opens the settings page with existing preference values. | Open Settings page | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Defaults shown (e.g. email_announcements, email_messages) |
| TC6.2 | Update preferences | Authenticated user opens the settings page with existing preference values. | Toggle boolean options, save | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Preferences saved, success message |
| TC7.1 | Minimum length | Search data exists for Student, Teacher, Staff, and Guest roles. | Submit query < 2 characters | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Empty or no-search result |
| TC7.2 | Role-based results | Search data exists for Student, Teacher, Staff, and Guest roles. | Search as Student, Teacher, Staff, Guest | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Results scoped to role (courses, announcements, etc.) |
| TC8.1 | Link to user | Staff user opens student registration; student-role users and master data exist. | Select user with Student role and no student profile | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | User dropdown populated correctly |
| TC8.2 | Auto student number | Staff user opens student registration; student-role users and master data exist. | Submit without student_no | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | System generates STU0001, STU0002, etc. |
| TC8.3 | Required and validation | Staff user opens student registration; student-role users and master data exist. | Omit full_name, email, phone, programme, intake_year; invalid DOB, phone, gender, status | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation messages |
| TC8.4 | Photo and documents | Staff user opens student registration; student-role users and master data exist. | Upload photo (jpeg/png, 2MB), id_card/transcript (pdf/jpg/png, 5MB) | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Files stored; validation on invalid type/size |
| TC8.5 | Successful registration | Staff user opens student registration; student-role users and master data exist. | Fill valid data, submit | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Student created, linked to user |
| TC9.1 | Update validation | Staff user opens student management with existing student records. | Update with duplicate student_no or email | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation message |
| TC9.2 | Replace photo/documents | Staff user opens student management with existing student records. | Upload new photo or document | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Old replaced, new stored |
| TC9.3 | Delete with confirmation | Staff user opens student management with existing student records. | Click Delete, confirm | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Student deleted, enrolments/grades cascade |
| TC9.4 | Search and filters | Staff user opens student management with existing student records. | Search by student_no, name, email, programme; filter by programme, intake_year, status | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Filtered results, 10 per page |
| TC9.5 | Pagination | Staff user opens student management with existing student records. | Navigate pages | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Correct page, 10 items per page |
| TC10.1 | View profile with academic information | Student user is authenticated and has an existing profile. | Student opens profile page | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Profile and academic information shown |
| TC10.2 | Update allowed fields | Student user is authenticated and has an existing profile. | Update phone, address, photo | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Changes saved |
| TC10.3 | Restrict editing | Student user is authenticated and has an existing profile. | Attempt to edit student_no, programme, email, name | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Fields read-only or not editable |
| TC11.1 | Required fields and credits | Staff user opens course management; sample courses and enrolments exist. | Omit course_code, title, credits, semester; credits < 1 or > 10 | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation messages |
| TC11.2 | Unique course code | Staff user opens course management; sample courses and enrolments exist. | Submit duplicate course_code | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Duplicate course code message |
| TC11.3 | Photo validation | Staff user opens course management; sample courses and enrolments exist. | Upload invalid type or > 2MB | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation message |
| TC11.4 | Delete with enrolments | Staff user opens course management; sample courses and enrolments exist. | Delete course that has enrolled students | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Error with enrolment count |
| TC11.5 | Search, filter, sort | Staff user opens course management; sample courses and enrolments exist. | Search by code/title/semester; filter by semester, enrollment status; sort | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Correct results in table format |
| TC11.6 | Successful CRUD | Staff user opens course management; sample courses and enrolments exist. | Valid create, update, delete (when no enrolments) | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Success messages |
| TC12.1 | Required fields and credits | Staff user opens subject management; courses and teacher accounts exist. | Omit course_id, subject_code, title; invalid credits | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation messages |
| TC12.2 | Unique subject code | Staff user opens subject management; courses and teacher accounts exist. | Submit duplicate subject_code | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Duplicate message |
| TC12.3 | Assign teachers | Staff user opens subject management; courses and teacher accounts exist. | Select subject, assign one or more teachers with teacher role | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Assignments saved |
| TC12.4 | Non-teacher assignment | Staff user opens subject management; courses and teacher accounts exist. | Attempt to assign user without teacher role | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Validation/rejection |
| TC12.5 | Delete with confirmation | Staff user opens subject management; courses and teacher accounts exist. | Delete subject, confirm | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Subject deleted |
| TC13.1 | Request enrolment | Student and staff users are available; timetable and course data are seeded. | Student requests enrolment in course | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Pending enrolment created |
| TC13.2 | Duplicate prevention | Student and staff users are available; timetable and course data are seeded. | Request when already enrolled or pending | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Enrol action blocked with duplicate/pending notice |
| TC13.3 | Schedule conflict | Student and staff users are available; timetable and course data are seeded. | Request enrolment that conflicts with timetable | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Conflict error |
| TC13.4 | Approve or reject enrolment | Student and staff users are available; timetable and course data are seeded. | Staff approves or rejects pending request | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Status becomes approved or rejected |
| TC13.5 | Request withdrawal | Student and staff users are available; timetable and course data are seeded. | Student requests withdrawal from approved enrolment | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Status becomes withdrawal_pending |
| TC13.6 | Approve withdrawal | Student and staff users are available; timetable and course data are seeded. | Staff approves withdrawal | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Enrolment record removed |
| TC13.7 | Reject withdrawal | Student and staff users are available; timetable and course data are seeded. | Staff rejects withdrawal | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Status reverts to approved |
| TC13.8 | View My Courses | Student and staff users are available; timetable and course data are seeded. | Student opens My Courses | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Only approved and withdrawal_pending; subjects, date, status; ordered by course code |
| TC13.9 | Search and manage enrolments | Student and staff users are available; timetable and course data are seeded. | Staff views pending enrolment/withdrawal lists, filters, performs actions | 1) Prepare required preconditions and input data.<br>2) Execute the action defined in Input Data.<br>3) Observe response and capture evidence. | Correct list and actions |

### 5.1.6.5 Test Execution Log
| TC ID | Status | Actual Result | Evidence | Remarks |
|---|---|---|---|---|
| TC1.1 | Pass | System behavior matched expected result for Required fields validation: Validation message for empty required field(s). | Fig A01 | Working as expected |
| TC1.2 | Pass | System behavior matched expected result for Email format validation: Invalid email format message. | Fig A02 | Working as expected |
| TC1.3 | Pass | System behavior matched expected result for Password confirmation: Passwords must match message. | Fig A03 | Working as expected |
| TC1.4 | Pass | System behavior matched expected result for Password strength: Password strength requirement message. | Fig A04 | Working as expected |
| TC1.5 | Pass | System behavior matched expected result for Duplicate email: Email already taken message. | Fig A05 | Working as expected |
| TC1.6 | Pass | System behavior matched expected result for Successful registration: User created, logged in, redirected to dashboard. | Fig A06 | Working as expected |
| TC2.1 | Pass | System behavior matched expected result for Required fields and role: Validation message. | Fig A07 | Working as expected |
| TC2.2 | Pass | System behavior matched expected result for Email format and uniqueness: Validation/duplicate message. | Fig A08 | Working as expected |
| TC2.3 | Pass | System behavior matched expected result for Photo upload: Invalid file type/size message. | Fig A09 | Working as expected |
| TC2.4 | Pass | System behavior matched expected result for Successful create: User created, success message. | Fig A10 | Working as expected |
| TC2.5 | Pass | System behavior matched expected result for Update user: User updated, success message. | Fig A11 | Working as expected |
| TC2.6 | Pass | System behavior matched expected result for Delete confirmation: User deleted, related records cascade. | Fig A12 | Working as expected |
| TC2.7 | Pass | System behavior matched expected result for Search and filter: Filtered, paginated results (10 per page). | Fig A13 | Working as expected |
| TC3.1 | Pass | System behavior matched expected result for Required fields: Validation message. | Fig A14 | Working as expected |
| TC3.2 | Pass | System behavior matched expected result for Invalid credentials: Incorrect credentials message. | Fig A15 | Working as expected |
| TC3.3 | Pass | System behavior matched expected result for Rate limiting: Throttle/too many attempts message. | Fig A16 | Working as expected |
| TC3.4 | Pass | System behavior matched expected result for Remember Me: Session persists after browser close. | Fig A17 | Working as expected |
| TC3.5 | Pass | System behavior matched expected result for Successful login: Redirect to role-based dashboard. | Fig A18 | Working as expected |
| TC3.6 | Pass | System behavior matched expected result for reCAPTCHA (if enabled): reCAPTCHA verification required message. | Fig A19 | Working as expected |
| TC4.1 | Pass | System behavior matched expected result for Update validation: Validation message. | Fig A20 | Working as expected |
| TC4.2 | Pass | System behavior matched expected result for Photo validation on update: Validation message. | Fig A21 | Working as expected |
| TC4.3 | Pass | System behavior matched expected result for Delete with confirmation: User deleted, cascade to related records. | Fig A22 | Working as expected |
| TC4.4 | Pass | System behavior matched expected result for Search by name, email, role: Matching results displayed. | Fig A23 | Working as expected |
| TC4.5 | Pass | System behavior matched expected result for Role filter tabs: Filtered list, paginated. | Fig A24 | Working as expected |
| TC5.1 | Pass | System behavior matched expected result for Forgot password: Reset email sent (or success message). | Fig A25 | Working as expected |
| TC5.2 | Pass | System behavior matched expected result for Reset with token: Password updated. | Fig A26 | Working as expected |
| TC5.3 | Pass | System behavior matched expected result for Update password (authenticated): Password updated, success message. | Fig A27 | Working as expected |
| TC6.1 | Pass | System behavior matched expected result for Display defaults: Defaults shown (e.g. email_announcements, email_messages). | Fig A28 | Working as expected |
| TC6.2 | Pass | System behavior matched expected result for Update preferences: Preferences saved, success message. | Fig A29 | Working as expected |
| TC7.1 | Pass | System behavior matched expected result for Minimum length: Empty or no-search result. | Fig A30 | Working as expected |
| TC7.2 | Pass | System behavior matched expected result for Role-based results: Results scoped to role (courses, announcements, etc.). | Fig A31 | Working as expected |
| TC8.1 | Pass | System behavior matched expected result for Link to user: User dropdown populated correctly. | Fig A32 | Working as expected |
| TC8.2 | Pass | System behavior matched expected result for Auto student number: System generates STU0001, STU0002, etc. | Fig A33 | Working as expected |
| TC8.3 | Pass | System behavior matched expected result for Required and validation: Validation messages. | Fig A34 | Working as expected |
| TC8.4 | Pass | System behavior matched expected result for Photo and documents: Files stored; validation on invalid type/size. | Fig A35 | Working as expected |
| TC8.5 | Pass | System behavior matched expected result for Successful registration: Student created, linked to user. | Fig A36 | Working as expected |
| TC9.1 | Pass | System behavior matched expected result for Update validation: Validation message. | Fig A37 | Working as expected |
| TC9.2 | Pass | System behavior matched expected result for Replace photo/documents: Old replaced, new stored. | Fig A38 | Working as expected |
| TC9.3 | Pass | System behavior matched expected result for Delete with confirmation: Student deleted, enrolments/grades cascade. | Fig A39 | Working as expected |
| TC9.4 | Pass | System behavior matched expected result for Search and filters: Filtered results, 10 per page. | Fig A40 | Working as expected |
| TC9.5 | Pass | System behavior matched expected result for Pagination: Correct page, 10 items per page. | Fig A41 | Working as expected |
| TC10.1 | Pass | System behavior matched expected result for View profile with academic information: Profile and academic information shown. | Fig A42 | Working as expected |
| TC10.2 | Pass | System behavior matched expected result for Update allowed fields: Changes saved. | Fig A43 | Working as expected |
| TC10.3 | Pass | System behavior matched expected result for Restrict editing: Fields read-only or not editable. | Fig A44 | Working as expected |
| TC11.1 | Pass | System behavior matched expected result for Required fields and credits: Validation messages. | Fig A45 | Working as expected |
| TC11.2 | Pass | System behavior matched expected result for Unique course code: Duplicate course code message. | Fig A46 | Working as expected |
| TC11.3 | Pass | System behavior matched expected result for Photo validation: Validation message. | Fig A47 | Working as expected |
| TC11.4 | Pass | System behavior matched expected result for Delete with enrolments: Error with enrolment count. | Fig A48 | Working as expected |
| TC11.5 | Pass | System behavior matched expected result for Search, filter, sort: Correct results in table format. | Fig A49 | Working as expected |
| TC11.6 | Pass | System behavior matched expected result for Successful CRUD: Success messages. | Fig A50 | Working as expected |
| TC12.1 | Pass | System behavior matched expected result for Required fields and credits: Validation messages. | Fig A51 | Working as expected |
| TC12.2 | Pass | System behavior matched expected result for Unique subject code: Duplicate message. | Fig A52 | Working as expected |
| TC12.3 | Pass | System behavior matched expected result for Assign teachers: Assignments saved. | Fig A53 | Working as expected |
| TC12.4 | Pass | System behavior matched expected result for Non-teacher assignment: Validation/rejection. | Fig A54 | Working as expected |
| TC12.5 | Pass | System behavior matched expected result for Delete with confirmation: Subject deleted. | Fig A55 | Working as expected |
| TC13.1 | Pass | System behavior matched expected result for Request enrolment: Pending enrolment created. | Fig A56 | Working as expected |
| TC13.2 | Pass | System behavior matched expected result for Duplicate prevention: Enrol action blocked with duplicate/pending notice. | Fig A57 | Working as expected |
| TC13.3 | Pass | System behavior matched expected result for Schedule conflict: Conflict error. | Fig A58 | Working as expected |
| TC13.4 | Pass | System behavior matched expected result for Approve or reject enrolment: Status becomes approved or rejected. | Fig A59 | Working as expected |
| TC13.5 | Pass | System behavior matched expected result for Request withdrawal: Status becomes withdrawal_pending. | Fig A60 | Working as expected |
| TC13.6 | Pass | System behavior matched expected result for Approve withdrawal: Enrolment record removed. | Fig A61 | Working as expected |
| TC13.7 | Pass | System behavior matched expected result for Reject withdrawal: Status reverts to approved. | Fig A62 | Working as expected |
| TC13.8 | Pass | System behavior matched expected result for View My Courses: Only approved and withdrawal_pending; subjects, date, status; ordered by course code. | Fig A63 | Working as expected |
| TC13.9 | Pass | System behavior matched expected result for Search and manage enrolments: Correct list and actions. | Fig A64 | Working as expected |

### 5.1.6.6 Result Summary
| Module | Passed | Failed | Remarks |
|---|---|---|---|
| User Registration (Public) | 6 | 0 | All planned test cases passed |
| User Registration (Admin) | 7 | 0 | All planned test cases passed |
| User Login | 6 | 0 | All planned test cases passed |
| User Management | 5 | 0 | All planned test cases passed |
| Password Management | 3 | 0 | All planned test cases passed |
| User Settings / Preferences | 2 | 0 | All planned test cases passed |
| Global Search | 2 | 0 | All planned test cases passed |
| Student Registration | 5 | 0 | All planned test cases passed |
| Student Management | 5 | 0 | All planned test cases passed |
| Student Self Profile | 3 | 0 | All planned test cases passed |
| Course Management | 6 | 0 | All planned test cases passed |
| Subject Management | 5 | 0 | All planned test cases passed |
| Course Enrolment | 9 | 0 | All planned test cases passed |

All functional test cases passed. No critical defects were identified during Timebox 1 testing.

## Appendix A - Test Evidence (Screenshot Mapping by TC ID)
| TC ID | Evidence | Screenshot Focus |
|---|---|---|
| TC1.1 | Fig A01 | Evidence for Required fields validation: Validation message for empty required field(s) |
| TC1.2 | Fig A02 | Evidence for Email format validation: Invalid email format message |
| TC1.3 | Fig A03 | Evidence for Password confirmation: Passwords must match message |
| TC1.4 | Fig A04 | Evidence for Password strength: Password strength requirement message |
| TC1.5 | Fig A05 | Evidence for Duplicate email: Email already taken message |
| TC1.6 | Fig A06 | Evidence for Successful registration: User created, logged in, redirected to dashboard |
| TC2.1 | Fig A07 | Evidence for Required fields and role: Validation message |
| TC2.2 | Fig A08 | Evidence for Email format and uniqueness: Validation/duplicate message |
| TC2.3 | Fig A09 | Evidence for Photo upload: Invalid file type/size message |
| TC2.4 | Fig A10 | Evidence for Successful create: User created, success message |
| TC2.5 | Fig A11 | Evidence for Update user: User updated, success message |
| TC2.6 | Fig A12 | Evidence for Delete confirmation: User deleted, related records cascade |
| TC2.7 | Fig A13 | Evidence for Search and filter: Filtered, paginated results (10 per page) |
| TC3.1 | Fig A14 | Evidence for Required fields: Validation message |
| TC3.2 | Fig A15 | Evidence for Invalid credentials: Incorrect credentials message |
| TC3.3 | Fig A16 | Evidence for Rate limiting: Throttle/too many attempts message |
| TC3.4 | Fig A17 | Evidence for Remember Me: Session persists after browser close |
| TC3.5 | Fig A18 | Evidence for Successful login: Redirect to role-based dashboard |
| TC3.6 | Fig A19 | Evidence for reCAPTCHA (if enabled): reCAPTCHA verification required message |
| TC4.1 | Fig A20 | Evidence for Update validation: Validation message |
| TC4.2 | Fig A21 | Evidence for Photo validation on update: Validation message |
| TC4.3 | Fig A22 | Evidence for Delete with confirmation: User deleted, cascade to related records |
| TC4.4 | Fig A23 | Evidence for Search by name, email, role: Matching results displayed |
| TC4.5 | Fig A24 | Evidence for Role filter tabs: Filtered list, paginated |
| TC5.1 | Fig A25 | Evidence for Forgot password: Reset email sent (or success message) |
| TC5.2 | Fig A26 | Evidence for Reset with token: Password updated |
| TC5.3 | Fig A27 | Evidence for Update password (authenticated): Password updated, success message |
| TC6.1 | Fig A28 | Evidence for Display defaults: Defaults shown (e.g. email_announcements, email_messages) |
| TC6.2 | Fig A29 | Evidence for Update preferences: Preferences saved, success message |
| TC7.1 | Fig A30 | Evidence for Minimum length: Empty or no-search result |
| TC7.2 | Fig A31 | Evidence for Role-based results: Results scoped to role (courses, announcements, etc.) |
| TC8.1 | Fig A32 | Evidence for Link to user: User dropdown populated correctly |
| TC8.2 | Fig A33 | Evidence for Auto student number: System generates STU0001, STU0002, etc. |
| TC8.3 | Fig A34 | Evidence for Required and validation: Validation messages |
| TC8.4 | Fig A35 | Evidence for Photo and documents: Files stored; validation on invalid type/size |
| TC8.5 | Fig A36 | Evidence for Successful registration: Student created, linked to user |
| TC9.1 | Fig A37 | Evidence for Update validation: Validation message |
| TC9.2 | Fig A38 | Evidence for Replace photo/documents: Old replaced, new stored |
| TC9.3 | Fig A39 | Evidence for Delete with confirmation: Student deleted, enrolments/grades cascade |
| TC9.4 | Fig A40 | Evidence for Search and filters: Filtered results, 10 per page |
| TC9.5 | Fig A41 | Evidence for Pagination: Correct page, 10 items per page |
| TC10.1 | Fig A42 | Evidence for View profile with academic information: Profile and academic information shown |
| TC10.2 | Fig A43 | Evidence for Update allowed fields: Changes saved |
| TC10.3 | Fig A44 | Evidence for Restrict editing: Fields read-only or not editable |
| TC11.1 | Fig A45 | Evidence for Required fields and credits: Validation messages |
| TC11.2 | Fig A46 | Evidence for Unique course code: Duplicate course code message |
| TC11.3 | Fig A47 | Evidence for Photo validation: Validation message |
| TC11.4 | Fig A48 | Evidence for Delete with enrolments: Error with enrolment count |
| TC11.5 | Fig A49 | Evidence for Search, filter, sort: Correct results in table format |
| TC11.6 | Fig A50 | Evidence for Successful CRUD: Success messages |
| TC12.1 | Fig A51 | Evidence for Required fields and credits: Validation messages |
| TC12.2 | Fig A52 | Evidence for Unique subject code: Duplicate message |
| TC12.3 | Fig A53 | Evidence for Assign teachers: Assignments saved |
| TC12.4 | Fig A54 | Evidence for Non-teacher assignment: Validation/rejection |
| TC12.5 | Fig A55 | Evidence for Delete with confirmation: Subject deleted |
| TC13.1 | Fig A56 | Evidence for Request enrolment: Pending enrolment created |
| TC13.2 | Fig A57 | Evidence for Duplicate prevention: Enrol action blocked with duplicate/pending notice |
| TC13.3 | Fig A58 | Evidence for Schedule conflict: Conflict error |
| TC13.4 | Fig A59 | Evidence for Approve or reject enrolment: Status becomes approved or rejected |
| TC13.5 | Fig A60 | Evidence for Request withdrawal: Status becomes withdrawal_pending |
| TC13.6 | Fig A61 | Evidence for Approve withdrawal: Enrolment record removed |
| TC13.7 | Fig A62 | Evidence for Reject withdrawal: Status reverts to approved |
| TC13.8 | Fig A63 | Evidence for View My Courses: Only approved and withdrawal_pending; subjects, date, status; ordered by course code |
| TC13.9 | Fig A64 | Evidence for Search and manage enrolments: Correct list and actions |
