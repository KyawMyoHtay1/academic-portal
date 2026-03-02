# 5.1.6 Functional Testing
## Timebox 1 – Manage Student Registration and Course Registration Process

### 5.1.6.1 Scope

Functional testing was conducted to ensure that the system performs all required tasks correctly and completely. Testing verifies that the system meets each specified aim, rejects erroneous data, displays adequate prompts and output, and that data capture forms and outputs are clear and complete. Tests are numbered; screenshots and evidence are numbered to correspond and annotated where changes were necessary. The tests cover validation rules, role-based permissions, CRUD operations, business constraints, and end-to-end process transitions.

---

### 5.1.6.2 Test Plan

*(Completed before implementation testing. Each test is numbered; test data and expected results are defined.)*

#### Test Script (1) User Registration (Public Site)
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 1.1 | Required fields validation | Name/email/password left blank | Validation messages displayed; submission blocked |
| 1.2 | Email format validation | Email missing '@' or invalid format | Invalid email format message displayed |
| 1.3 | Password confirmation | Password and confirmation mismatch | Passwords must match message displayed |
| 1.4 | Password strength | Weak password (e.g. too short) | Password strength requirement message displayed |
| 1.5 | Duplicate email | Existing email used | Email already taken message displayed |
| 1.6 | Successful registration | Valid data, terms accepted, reCAPTCHA (if enabled), click Register | User created, logged in, redirected to dashboard |

#### Test Script (2) User Registration (Admin Site)
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 2.1 | Required fields and role | Name, email, or role blank | Validation message displayed |
| 2.2 | Email format and uniqueness | Invalid or duplicate email | Validation/duplicate message displayed |
| 2.3 | Photo upload | File other than jpeg/jpg/png or > 2MB | Invalid file type/size message displayed |
| 2.4 | Successful create | Valid data, role selected, click Create | User created, success message |
| 2.5 | Update user | Edit user, change name/email (unique), click Update | User updated, success message |
| 2.6 | Delete confirmation | Click Delete, confirm dialog | User deleted, related records cascade |
| 2.7 | Search and filter | Search by name/email/role, use role tabs | Filtered, paginated results (10 per page) |

#### Test Script (3) User Login
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 3.1 | Required fields | Email or password blank | Validation message displayed |
| 3.2 | Invalid credentials | Wrong email/password | Incorrect credentials message displayed |
| 3.3 | Rate limiting | Wrong credentials 5+ times (same email+IP) | Throttle/too many attempts message displayed |
| 3.4 | Remember Me | Login with Remember Me checked | Session persists after browser close |
| 3.5 | Successful login | Valid credentials | Redirect to role-based dashboard |
| 3.6 | reCAPTCHA (if enabled) | Submit without completing reCAPTCHA | reCAPTCHA verification required message displayed |

#### Test Script (4) User Update, Delete, Search
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 4.1 | Update validation | Update user with invalid/duplicate email | Validation message displayed |
| 4.2 | Photo validation on update | Upload invalid photo type/size | Validation message displayed |
| 4.3 | Delete with confirmation | Click Delete, confirm | User deleted, cascade to related records |
| 4.4 | Search by name, email, role | Enter keyword, apply filters | Matching results displayed |
| 4.5 | Role filter tabs | Select All / Students / Teachers / Staff | Filtered list, paginated |

#### Test Script (5) Password Management
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 5.1 | Forgot password | Enter email, request reset | Reset email sent (or success message) |
| 5.2 | Reset with token | Open reset link, enter new password with confirmation | Password updated |
| 5.3 | Update password (authenticated) | Logged-in user changes password with confirmation | Password updated, success message |

#### Test Script (6) User Settings / Preferences
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 6.1 | Display defaults | Open Settings page | Defaults shown (e.g. email_announcements, email_messages) |
| 6.2 | Update preferences | Toggle boolean options, save | Preferences saved, success message |

#### Test Script (7) Global Search
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 7.1 | Minimum length | Query < 2 characters | Empty or no-search result |
| 7.2 | Role-based results | Search as Student, Teacher, Staff, Guest | Results scoped to role (courses, announcements, etc.) |

#### Test Script (8) Student Registration
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 8.1 | Link to user | Select user with Student role and no student profile | User dropdown populated correctly |
| 8.2 | Auto student number | Submit without student_no | System generates STU0001, STU0002, etc. |
| 8.3 | Required and validation | Omit full_name, email, phone, programme, intake_year; invalid DOB, phone, gender, status | Validation messages displayed |
| 8.4 | Photo and documents | Upload photo (jpeg/png, 2MB), id_card/transcript (pdf/jpg/png, 5MB) | Files stored; validation on invalid type/size |
| 8.5 | Successful registration | Fill valid data, submit | Student created, linked to user |

#### Test Script (9) Student Update, Delete, Search
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 9.1 | Update validation | Update with duplicate student_no or email | Validation message displayed |
| 9.2 | Replace photo/documents | Upload new photo or document | Old replaced, new stored |
| 9.3 | Delete with confirmation | Click Delete, confirm | Student deleted, enrolments/grades cascade |
| 9.4 | Search and filters | Search by student_no, name, email, programme; filter by programme, intake_year, status | Filtered results, 10 per page |
| 9.5 | Pagination | Navigate pages | Correct page, 10 items per page |

#### Test Script (10) Student Self Profile
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 10.1 | View profile with academic information | Student opens profile page | Profile and academic information shown |
| 10.2 | Update allowed fields | Update phone, address, photo | Changes saved |
| 10.3 | Restrict editing | Attempt to edit student_no, programme, email, name | Fields read-only or not editable |

#### Test Script (11) Course Register, Update, Delete, Search
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 11.1 | Required fields and credits | Omit course_code, title, credits, semester; credits < 1 or > 10 | Validation messages displayed |
| 11.2 | Unique course code | Submit duplicate course_code | Duplicate course code message displayed |
| 11.3 | Photo validation | Upload invalid type or > 2MB | Validation message displayed |
| 11.4 | Delete with enrolments | Delete course that has enrolled students | Error with enrolment count |
| 11.5 | Search, filter, sort | Search by code/title/semester; filter by semester, enrollment status; sort | Correct results in table format |
| 11.6 | Successful CRUD | Valid create, update, delete (when no enrolments) | Success messages |

#### Test Script (12) Subject Register, Update, Delete, Assign Teacher
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 12.1 | Required fields and credits | Omit course_id, subject_code, title; invalid credits | Validation messages displayed |
| 12.2 | Unique subject code | Submit duplicate subject_code | Duplicate message displayed |
| 12.3 | Assign teachers | Select subject, assign one or more teachers with teacher role | Assignments saved |
| 12.4 | Non-teacher assignment | Attempt to assign user without teacher role | Validation/rejection |
| 12.5 | Delete with confirmation | Delete subject, confirm | Subject deleted |

#### Test Script (13) Course Registration (Enrolment & Withdrawal)
| Test Case No. | Description | Input | Expected Result |
|---------------|-------------|-------|-----------------|
| 13.1 | Request enrolment | Student requests enrolment in course | Pending enrolment created |
| 13.2 | Duplicate prevention | Request when already enrolled or pending | Enrol button hidden or duplicate/pending notice |
| 13.3 | Schedule conflict | Request enrolment that conflicts with timetable | Conflict error displayed |
| 13.4 | Approve or reject enrolment | Staff approves or rejects pending request | Status becomes approved or rejected |
| 13.5 | Request withdrawal | Student requests withdrawal from approved enrolment | Status becomes withdrawal_pending |
| 13.6 | Approve withdrawal | Staff approves withdrawal | Enrolment record removed |
| 13.7 | Reject withdrawal | Staff rejects withdrawal | Status reverts to approved |
| 13.8 | View My Courses | Student opens My Courses | Only approved and withdrawal_pending; subjects, date, status; ordered by course code |
| 13.9 | Search and manage enrolments | Staff views pending enrolment/withdrawal lists, filters, performs actions | Correct list and actions |

---

### 5.1.6.3 Test Log

*(Completed after running the system. Screenshots are numbered to correspond to test numbers; see Appendix A.)*

#### Test Script (1) User Registration (Public Site)
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 1.1 | Validation messages displayed; submission blocked | Validation messages displayed correctly | None – Test Passed |
| 1.2 | Invalid email format message displayed | Invalid email format message displayed | None – Test Passed |
| 1.3 | Passwords must match message displayed | Passwords must match message displayed | None – Test Passed |
| 1.4 | Password strength requirement message displayed | Password strength requirement message displayed | None – Test Passed |
| 1.5 | Email already taken message displayed | Email already taken message displayed | None – Test Passed |
| 1.6 | User created, logged in, redirected to dashboard | User created and redirected to dashboard | None – Test Passed |

#### Test Script (2) User Registration (Admin Site)
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 2.1 | Validation message displayed | Validation message displayed correctly | None – Test Passed |
| 2.2 | Validation/duplicate message displayed | Validation/duplicate message displayed | None – Test Passed |
| 2.3 | Invalid file type/size message displayed | Invalid file type/size message displayed | None – Test Passed |
| 2.4 | User created, success message | User created, success message shown | None – Test Passed |
| 2.5 | User updated, success message | User updated, success message shown | None – Test Passed |
| 2.6 | User deleted, related records cascade | User deleted, related records cascade | None – Test Passed |
| 2.7 | Filtered, paginated results (10 per page) | Filtered, paginated results displayed | None – Test Passed |

#### Test Script (3) User Login
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 3.1 | Validation message displayed | Validation message displayed correctly | None – Test Passed |
| 3.2 | Incorrect credentials message displayed | Incorrect credentials message displayed | None – Test Passed |
| 3.3 | Throttle/too many attempts message displayed | Throttle/too many attempts message displayed | None – Test Passed |
| 3.4 | Session persists after browser close | Session persisted after browser close | None – Test Passed |
| 3.5 | Redirect to role-based dashboard | Redirected to role-based dashboard | None – Test Passed |
| 3.6 | reCAPTCHA verification required message displayed | reCAPTCHA verification required message displayed | None – Test Passed |

#### Test Script (4) User Update, Delete, Search
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 4.1 | Validation message displayed | Validation message displayed correctly | None – Test Passed |
| 4.2 | Validation message displayed | Validation message displayed correctly | None – Test Passed |
| 4.3 | User deleted, cascade to related records | User deleted, cascade to related records | None – Test Passed |
| 4.4 | Matching results displayed | Matching results displayed correctly | None – Test Passed |
| 4.5 | Filtered list, paginated | Filtered list, paginated correctly | None – Test Passed |

#### Test Script (5) Password Management
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 5.1 | Reset email sent (or success message) | Reset email sent / success message shown | None – Test Passed |
| 5.2 | Password updated | Password updated successfully | None – Test Passed |
| 5.3 | Password updated, success message | Password updated, success message shown | None – Test Passed |

#### Test Script (6) User Settings / Preferences
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 6.1 | Defaults shown (e.g. email_announcements, email_messages) | Defaults displayed correctly | None – Test Passed |
| 6.2 | Preferences saved, success message | Preferences saved, success message shown | None – Test Passed |

#### Test Script (7) Global Search
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 7.1 | Empty or no-search result | Empty or no-search result as expected | None – Test Passed |
| 7.2 | Results scoped to role (courses, announcements, etc.) | Results scoped to role correctly | None – Test Passed |

#### Test Script (8) Student Registration
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 8.1 | User dropdown populated correctly | User dropdown populated correctly | None – Test Passed |
| 8.2 | System generates STU0001, STU0002, etc. | System generated student numbers correctly | None – Test Passed |
| 8.3 | Validation messages displayed | Validation messages displayed correctly | None – Test Passed |
| 8.4 | Files stored; validation on invalid type/size | Files stored; validation worked as expected | None – Test Passed |
| 8.5 | Student created, linked to user | Student created, linked to user | None – Test Passed |

#### Test Script (9) Student Update, Delete, Search
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 9.1 | Validation message displayed | Validation message displayed correctly | None – Test Passed |
| 9.2 | Old replaced, new stored | Old replaced, new stored correctly | None – Test Passed |
| 9.3 | Student deleted, enrolments/grades cascade | Student deleted, cascade as expected | None – Test Passed |
| 9.4 | Filtered results, 10 per page | Filtered results, 10 per page displayed | None – Test Passed |
| 9.5 | Correct page, 10 items per page | Correct page, 10 items per page | None – Test Passed |

#### Test Script (10) Student Self Profile
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 10.1 | Profile and academic information shown | Profile and academic information shown correctly | None – Test Passed |
| 10.2 | Changes saved | Changes saved successfully | None – Test Passed |
| 10.3 | Fields read-only or not editable | Fields read-only or not editable as expected | None – Test Passed |

#### Test Script (11) Course Register, Update, Delete, Search
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 11.1 | Validation messages displayed | Validation messages displayed correctly | None – Test Passed |
| 11.2 | Duplicate course code message displayed | Duplicate course code message displayed | None – Test Passed |
| 11.3 | Validation message displayed | Validation message displayed correctly | None – Test Passed |
| 11.4 | Error with enrolment count | Error with enrolment count displayed | None – Test Passed |
| 11.5 | Correct results in table format | Correct results in table format | None – Test Passed |
| 11.6 | Success messages | Success messages displayed | None – Test Passed |

#### Test Script (12) Subject Register, Update, Delete, Assign Teacher
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 12.1 | Validation messages displayed | Validation messages displayed correctly | None – Test Passed |
| 12.2 | Duplicate message displayed | Duplicate message displayed | None – Test Passed |
| 12.3 | Assignments saved | Assignments saved successfully | None – Test Passed |
| 12.4 | Validation/rejection | Validation/rejection as expected | None – Test Passed |
| 12.5 | Subject deleted | Subject deleted successfully | None – Test Passed |

#### Test Script (13) Course Registration (Enrolment & Withdrawal)
| No. | Expected Result | Actual Result | Action Taken |
|-----|----------------|---------------|--------------|
| 13.1 | Pending enrolment created | Pending enrolment created | None – Test Passed |
| 13.2 | Enrol button hidden or duplicate/pending notice | Enrol button hidden as expected | None – Test Passed |
| 13.3 | Conflict error displayed | Conflict error displayed | None – Test Passed |
| 13.4 | Status becomes approved or rejected | Status became approved or rejected correctly | None – Test Passed |
| 13.5 | Status becomes withdrawal_pending | Status became withdrawal_pending | None – Test Passed |
| 13.6 | Enrolment record removed | Enrolment record removed | None – Test Passed |
| 13.7 | Status reverts to approved | Status reverted to approved | None – Test Passed |
| 13.8 | Only approved and withdrawal_pending; subjects, date, status; ordered by course code | Displayed as expected | None – Test Passed |
| 13.9 | Correct list and actions | Correct list and actions displayed | None – Test Passed |

---

### 5.1.6.4 Result Summary

| Module | Total Tests | Passed | Failed |
|--------|-------------|--------|--------|
| User Registration (Public) | 6 | 6 | 0 |
| User Registration (Admin) | 7 | 7 | 0 |
| User Login | 6 | 6 | 0 |
| User Update, Delete, Search | 5 | 5 | 0 |
| Password Management | 3 | 3 | 0 |
| User Settings / Preferences | 2 | 2 | 0 |
| Global Search | 2 | 2 | 0 |
| Student Registration | 5 | 5 | 0 |
| Student Update, Delete, Search | 5 | 5 | 0 |
| Student Self Profile | 3 | 3 | 0 |
| Course Register, Update, Delete, Search | 6 | 6 | 0 |
| Subject Register, Update, Delete, Assign Teacher | 5 | 5 | 0 |
| Course Registration (Enrolment & Withdrawal) | 9 | 9 | 0 |

**Total: 64 test cases. All passed.**

All functional test cases passed. No critical defects were identified during Timebox 1 testing. Any defects found were corrected and re-tested. Evidence (screenshots and printouts) is stored in date order and numbered to correspond to the test numbers.

---

## Appendix A – Test Evidence (Screenshots)

Screenshots and output are numbered to correspond to the test numbers (e.g. Fig.1.1 & 1.2 for Test 1.1; Fig.2.1 & 2.2 for Test 2.1). Before Testing and After Testing figures are annotated to state whether any changes to that part of the system were necessary and what was done. Evidence of failures (if any) is retained in date order for regression testing reference.

| Test | Evidence (Figure reference) |
|------|----------------------------|
| 1.1–1.6 | Fig.1.1–1.12 |
| 2.1–2.7 | Fig.2.1–2.16 |
| 3.1–3.6 | Fig.3.1–3.12 |
| 4.1–4.5 | Fig.4.1–4.10 |
| 5.1–5.3 | Fig.5.1–5.6 |
| 6.1–6.2 | Fig.6.1–6.6 |
| 7.1–7.2 | Fig.7.1–7.4 |
| 8.1–8.5 | Fig.8.1–8.16 |
| 9.1–9.5 | Fig.9.1–9.11 |
| 10.1–10.3 | Fig.10.1–10.6 |
| 11.1–11.6 | Fig.11.1–11.13 |
| 12.1–12.5 | Fig.12.1–12.12 |
| 13.1–13.9 | Fig.13.1–13.19 |
