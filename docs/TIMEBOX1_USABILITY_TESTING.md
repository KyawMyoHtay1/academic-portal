# 5.1.7 Usability Testing
## Timebox 1: Manage Student Registration & Course Registration Process

Usability testing was conducted using Nielsen’s 10 Usability Heuristics. Selected heuristics with supporting examples from the academic portal are below.

---

## Visibility of System Status

**Explain:** The system keeps users informed about current state and location. Breadcrumb trails are used on admin pages (Manage Users, Manage Students, Manage Courses, Manage Subjects, Manage Enrollments) to show where the user is in the hierarchy. A page loading indicator appears during navigation. On Browse Courses and My Courses, enrolment status badges (Pending, Approved, Rejected, Withdrawal Pending) make request status visible.

*[Insert screenshot: Breadcrumb on Manage Courses Page]*

---

## Match between System and Real World

**Explain:** The system speaks the user’s language and uses familiar concepts. Terminology matches academic workflows: “Enrolment,” “Course,” “Subject,” “Semester,” “Student Number,” “Programme,” “Intake Year.” Dropdowns for Programme, Semester, Status, and Gender use common values (e.g. Active, Suspended, Graduated; Male, Female, Other) instead of codes or abbreviations.

*[Insert screenshot: Register Student Page with Programme dropdown]*

---

## User Control and Freedom

**Explain:** Users can navigate freely and undo or exit actions. Breadcrumb links allow returning to parent pages (e.g. Manage Courses → Courses). Cancel buttons on create/edit forms let users exit without saving. Delete actions use confirmation dialogs so users can back out. Navigation menus and role-based dashboards support moving between sections without getting stuck.

*[Insert screenshot: Cancel button on Create User form]*

---

## Consistency and Standards

**Explain:** The interface uses consistent patterns across modules. Search bars and filters use the same layout on Manage Users, Manage Students, Manage Courses, and Manage Subjects. Role filter tabs (All, Students, Teachers, Staff) share the same styling. Form layouts, required field markers, and validation messaging follow a common style across the portal.

*[Insert screenshot: Role filter tabs on Manage Users Page]*

---

## Error Prevention

**Explain:** The system reduces errors before they happen. Required fields are marked with an asterisk (*). Show/Hide Password on the Login page helps avoid typos. File upload limits (photo 2MB, documents 5MB) are communicated in labels. Confirmation dialogs prevent accidental deletion. Inline validation and clear error messages (e.g. “The email has already been taken”) guide correction.

*[Insert screenshot: Show/Hide Password on Login Page]*

---

## Recognition rather than Recall

**Explain:** The system limits memory load by making options visible. Dropdowns for Programme, Semester, Status, and Gender avoid manual recall. Role tabs, filter options, and sort controls show choices instead of requiring memorization. Global Search gives quick access to courses, announcements, and other items without recalling paths.

*[Insert screenshot: Programme dropdown on Register Student]*

---

## Flexibility and Efficiency of Use

**Explain:** Both new and frequent users can work effectively. Global Search in the header supports fast access across modules. Filter tabs (e.g. Pending, Approved, Rejected on Manage Enrollments) speed up workflow. Pagination with 10 items per page balances clarity and efficiency. Students can browse courses and see enrolment status in one place.

*[Insert screenshot: Global Search and Manage Enrollments filters]*

---

## Aesthetic and Minimalist Design

**Explain:** Screens show only necessary information. Tables use clear spacing and alignment. Forms are grouped logically (personal info, academic info, documents). Dashboards focus on role-specific content. Toast notifications and inline errors keep feedback simple and unobtrusive.

*[Insert screenshot: Manage Students Page table layout]*

---

## Help Users Recognise, Diagnose, and Recover from Errors

**Explain:** Errors are expressed in plain language and suggest fixes. Validation messages use clear text (e.g. “The password must be at least 8 characters”). File upload errors specify allowed types and sizes. Post-too-large uploads show a friendly message instead of a raw 413 page. Success and error toasts confirm whether actions succeeded or failed.

*[Insert screenshot: Validation message example]*

---

## Help and Documentation

**Explain:** Contextual help supports use without heavy documentation. Labels and placeholders indicate expected formats (e.g. phone, date of birth). Accept attributes on file inputs clarify allowed types. Required field markers and validation messages guide completion. Role-based dashboards and menus direct users to relevant sections.

*[Insert screenshot: Form with labels and placeholders]*

---

# 5.1.9 Iteration for Usability Testing

Iterations made after usability testing, aligned with Nielsen’s heuristics.

---

## Iteration 1: Visibility of System Status

**Users said** that when they reach pages like Manage Courses or Manage Enrollments, they lose track of where they are. Breadcrumb links were added so users can see their location and return to parent pages.

**(Iteration 1) Iteration for Visibility of System Status**

| Before | After |
|--------|-------|
| *[Placeholder: Page without breadcrumb]* | *[Placeholder: Page with breadcrumb – e.g. Admin > Courses > Manage Courses]* |

---

## Iteration 2: Error Prevention – Show/Hide Password

**Users said** that the Login page lacked a way to show the password, which led to typing mistakes. A Show/Hide Password control was added so users can verify their input before submitting.

**(Iteration 2) Iteration for Error Prevention on Login**

| Before | After |
|--------|-------|
| *[Placeholder: Password field without toggle]* | *[Placeholder: Password field with eye icon toggle]* |

---

## Iteration 3: Flexibility – Search on My Courses

**Users said** that My Courses had no search, so finding specific courses was difficult. A search bar and filters were added for quicker access.

**(Iteration 3) Iteration for Search on My Courses**

| Before | After |
|--------|-------|
| *[Placeholder: My Courses without search]* | *[Placeholder: My Courses with search bar and filters]* |

---

## Iteration 4: Aesthetic and Efficiency – Pagination on Manage Users & Students

**Users said** that long user and student lists without pagination were hard to read. Pagination (10 per page) was added for easier scanning and performance.

**(Iteration 4) Iteration for Manage Users & Manage Students Pagination**

| Before | After |
|--------|-------|
| *[Placeholder: Long list without pagination]* | *[Placeholder: Table with pagination controls]* |

---

## Iteration 5: Recognition rather than Recall – Required Field Markers

**Users said** that forms did not clearly show which fields were required. Required fields were marked with an asterisk (*) to reduce guesswork.

**(Iteration 5) Iteration for Required Field Indicators**

| Before | After |
|--------|-------|
| *[Placeholder: Form without asterisks]* | *[Placeholder: Form with asterisks on required fields]* |

---

## Iteration 6: User Control and Freedom – Cancel on Forms

**Users said** that if they entered wrong data, it was tedious to clear every field. A Cancel button was added so users can abandon changes and return without saving.

**(Iteration 6) Iteration for Form Cancel Button**

| Before | After |
|--------|-------|
| *[Placeholder: Form with only Submit]* | *[Placeholder: Form with Cancel and Submit]* |

---

# 5.1.10 Timebox Summary

## Work Done

The development work for Timebox 1 (Manage Student Registration & Course Registration Process) is complete. Implemented features include:

- **Manage User:** Register (public and admin), Update, Delete, Search with role filters
- **Manage Student:** Register, Update, Delete, Search with Programme/Intake Year/Status filters
- **Manage Course:** Register, Update, Delete, Search with semester and enrolment filters
- **Manage Subject:** Register, Update, Delete, Assign Teacher
- **Course Registration:** Request Enrolment, Request Withdrawal, Approve/Reject (enrolment and withdrawal), View My Courses
- **User Login, Password Management, Settings, Global Search**

Prototypes for screen design, use case descriptions, class diagrams, and sequence diagrams for Timebox 1 were completed. Functional tests were run according to the test plan, and usability testing led to iterations (e.g. Show/Hide Password, breadcrumbs, pagination, required field markers).

---

## Problems (Issues)

During design and testing, the following issues were identified:

1. **Navigation:** Users lost their place in admin pages and requested clearer location cues.
2. **Validation feedback:** Some messages were unclear, and file size/type limits were not obvious.
3. **Data entry:** Programme, Semester, and Status were initially free text; users were unsure what to enter.
4. **List usability:** Long lists without search or pagination were hard to scan.
5. **Error prevention:** The login page had no way to show the password, increasing typo risk.
6. **Form control:** Users wanted an easy way to cancel and exit without saving.

---

## Solutions

1. **Breadcrumb navigation** was added on admin pages to show current location and support quick navigation.
2. **Validation messages** were clarified, and file upload rules (e.g. photo 2MB, documents 5MB) were stated in labels and error text.
3. **Text inputs** for Programme, Semester, and Status were replaced with dropdowns to reduce confusion.
4. **Search and filters** were added on My Courses and Manage Enrollments.
5. **Pagination** (10 per page) was added for Manage Users and Manage Students.
6. **Show/Hide Password** was added on the Login page.
7. **Required field markers** (asterisks) were added on all relevant forms.
8. **Cancel buttons** were added on create/edit forms.
9. **Post-too-large upload handler** was added so oversized uploads show a clear message instead of a raw 413 error.

---

## Remaining Timeboxes

Planned work for the remaining timeboxes:

| Timebox | Scope | Description |
|---------|-------|-------------|
| **Timebox 2** | Manage Grades, Fee Payment & Assignment Process | Recording and approving grades, managing fees and payments (including Stripe), creating and submitting assignments. |
| **Timebox 3** | Manage Timetable, Attendance & Communication Process | Timetables, attendance recording and alerts, announcements, messaging. |

Timebox 2 focuses on academic assessment and fee workflows. Timebox 3 focuses on scheduling, attendance, and communication. Together with Timebox 1, they provide the core academic portal functionality for students, teachers, and staff.

---

*Document version: Timebox 1 Usability Testing & Summary*
