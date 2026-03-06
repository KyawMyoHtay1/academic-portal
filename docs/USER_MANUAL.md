# 6.3.2 User Manual

This user manual covers the Academic Portal features delivered across all three timeboxes. Each section includes purpose, steps, expected output, and figure placeholders to be replaced with real screenshots in the final report.

> Note: Figure numbers restart per timebox because they follow the prototypes in each Timebox Screen Design document.

---

## Timebox 1: Manage Student Registration & Course Registration Process

This user manual describes how end users (Guest, Student, Staff/Admin) use the Academic Portal features delivered in **Timebox 1**. Screens and figure numbers follow the prototypes listed in `docs/TIMEBOX1_SCREEN_DESIGN.md`. Replace the figure placeholders with your actual screenshots in the final report.

---

## A) Guest / Public Site

### 1. Register User Page (Public Site)

**Purpose:** Create a new user account (self-registration).

**Steps:**
- Open the Academic Portal public site.
- Navigate to **Register**.
- Enter Name, Email, Password, Confirm Password.
- Click **Register**.

**Expected output:** Account created and user is redirected to the dashboard (or login page depending on your implementation).

Fig (1.1) Register User Page (Public Site)  

---

### 2. Login Page (Public Site)

**Purpose:** Log in to the portal.

**Steps:**
- Navigate to **Login**.
- Enter Email and Password.
- (Optional) tick **Remember Me**.
- Click **Login**.

**Expected output:** User is authenticated and redirected to a role-based dashboard.

Fig (2.1) Login Page (Public Site)  

---

### 3. Forgot Password Page (Public Site)

**Purpose:** Request a password reset link.

**Steps:**
- From the Login page, click **Forgot Password**.
- Enter the registered email address.
- Click **Send Reset Link**.

**Expected output:** A success message is shown and a reset email is sent.

Fig (3.1) Forgot Password Page (Public Site)  

---

### 4. Reset Password Page (Public Site)

**Purpose:** Set a new password using a reset token.

**Steps:**
- Open the reset link received by email.
- Enter New Password and Confirm Password.
- Click **Reset Password**.

**Expected output:** Password updated and user can log in with the new password.

Fig (4.1) Reset Password Page (Public Site)  

---

## B) Student Site

### 5. Student Dashboard Page (Student Site)

**Purpose:** View an overview of student information and quick access to key functions.

**Steps:**
- Log in as a Student.
- The dashboard opens automatically.
- Use menu shortcuts to open Profile, Browse Courses, My Courses, and Settings.

**Expected output:** Dashboard loads and shows student-relevant modules.

Fig (5.1) Student Dashboard Page (Student Site)  

---

### 6. My Profile Page (Student Site)

**Purpose:** View student profile and academic information.

**Steps:**
- Open **My Profile**.
- Review personal and academic information.

**Expected output:** Profile details are displayed (some fields may be read-only).

Fig (6.1) My Profile Page (Student Site)  

---

### 7. Edit Self Profile Page (Student Site)

**Purpose:** Update editable profile fields (e.g., phone, address, photo).

**Steps:**
- Open **Edit Profile** from My Profile.
- Update the allowed fields.
- Click **Save / Update**.

**Expected output:** Changes are saved and success message is shown.

Fig (7.1) Edit Self Profile Page (Student Site)  

---

### 8. Browse Courses Page (Student Site)

**Purpose:** View available courses before requesting enrolment.

**Steps:**
- Open **Browse Courses**.
- Search/filter courses if available.
- Open a course to view details (if supported).

**Expected output:** Course list is displayed and navigation works correctly.

Fig (8.1) Browse Courses Page (Student Site)  

---

### 9. My Courses Page (Student Site)

**Purpose:** Manage course enrolment requests and view approved enrolments.

**Steps (Request enrolment):**
- Open **My Courses**.
- Select a course and click **Enroll / Request Enrolment**.
- Confirm the request if prompted.

**Steps (Withdraw):**
- For an approved course, click **Request Withdrawal**.
- Confirm the request if prompted.

**Expected output:** Enrolment status changes (e.g., pending / approved / withdrawal_pending) and the list updates.

Fig (9.1) My Courses Page (Student Site)  

---

### 10. Student Settings Page (Student Site)

**Purpose:** Update preferences (e.g., notification/email preferences if enabled).

**Steps:**
- Open **Settings**.
- Toggle preferences.
- Click **Save**.

**Expected output:** Preferences saved successfully.

Fig (10.1) Student Settings Page (Student Site)  

---

## C) Staff/Admin Site (Administration)

### 11. Staff Dashboard Page (Admin Site)

**Purpose:** Access administrative modules (users, students, courses, subjects, enrolments).

**Steps:**
- Log in as Staff/Admin.
- Use dashboard navigation to open management pages.

**Expected output:** Staff dashboard loads with admin menus.

Fig (11.1) Staff Dashboard Page (Admin Site)  

---

### 12. Register User Page (Admin Site)

**Purpose:** Staff creates a new user (Student/Teacher/Staff) with role assignment.

**Steps:**
- Open **Register User** (Admin Site).
- Enter user details and select role.
- (Optional) upload photo if supported.
- Click **Create / Register**.

**Expected output:** New user created and appears in Manage Users list.

Fig (12.1) Register User Page (Admin Site)  

---

### 13. Manage Users Page (Admin Site)

**Purpose:** View/search/filter users; navigate to edit/delete.

**Steps:**
- Open **Manage Users**.
- Use search box and role filters/tabs.
- Click **Edit** to modify a user, or **Delete** to remove a user (with confirmation).

**Expected output:** List updates based on search/filter; user changes persist.

Fig (13.1) Manage Users Page (Admin Site)  

---

### 14. Edit User Page (Admin Site)

**Purpose:** Update user details (e.g., name, email, role, photo).

**Steps:**
- From Manage Users, click **Edit**.
- Update fields.
- Click **Save / Update**.

**Expected output:** User updated and success message displayed.

Fig (14.1) Edit User Page (Admin Site)  

---

### 15. Register Student Page (Admin Site)

**Purpose:** Create a student profile and link it to a Student user account.

**Steps:**
- Open **Register Student**.
- Select or link an existing user with Student role.
- Fill in student details (student_no may auto-generate).
- Upload required documents (photo / id_card / transcript) if enabled.
- Click **Create / Register**.

**Expected output:** Student profile created successfully and appears in Student management list.

Fig (15.1) Register Student Page (Admin Site)  

---

## Timebox 2: Manage Grades, Fee Payment & Assignment Process

This user manual describes how Students, Teachers, and Staff/Admin use the Academic Portal features delivered in **Timebox 2**. Screen names and figure placeholders follow `docs/TIMEBOX2_SCREEN_DESIGN.md`. Replace each figure placeholder with your actual screenshots in the final report.

---

## A) Student Site

### 1. My Grades Page (Student Site)

**Purpose:** View approved grades, grade status (pending/approved/rejected), and GPA (if available).

**Steps:**
- Log in as Student.
- Open **My Grades**.
- Use filters (course/subject/semester) if available.
- Select a subject/grade to view details.

**Expected output:** Grades list displays with clear status and only the grades allowed for student viewing (typically approved grades).

Fig (1.1) My Grades Page (Student Site)  

---

### 2. Grade Details & Breakdown Page (Student Site)

**Purpose:** View grade detail and computed breakdown (if provided by the system).

**Steps:**
- From **My Grades**, select a grade/subject.
- View score, letter grade (if shown), and breakdown information.

**Expected output:** Grade details are displayed clearly; if breakdown exists, the calculation summary is shown.

Fig (2.1) Grade Details & Breakdown Page (Student Site)  

---

### 3. My Fees Page (Student Site)

**Purpose:** View personal fee records, due dates, and fee/payment status.

**Steps:**
- Open **My Fees**.
- Review fee list (pending/payment_pending/paid/failed where applicable).
- Use status filters and search if available.
- Select a fee to proceed with payment/confirmation if required.

**Expected output:** Only the logged-in student’s fees are shown, with overdue fees highlighted (if implemented).

Fig (3.1) My Fees Page (Student Site)  

---

### 4. Fee Payment / Confirmation Page (Student Site)

**Purpose:** Submit payment confirmation for a fee (manual or Stripe initiation depending on your flow).

**Steps:**
- From **My Fees**, open a pending fee.
- Click **Pay / Confirm Payment**.
- Follow the instructions (upload/enter payment info if manual, or proceed to Stripe checkout).

**Expected output:** Fee status is updated to **payment_pending** (or equivalent) and the student sees confirmation feedback.

Fig (4.1) Fee Payment / Confirmation Page (Student Site)  

---

### 5. Stripe Checkout Page (Student Site)

**Purpose:** Complete fee payment using Stripe Checkout.

**Steps:**
- Click **Pay with Card** (or equivalent) on the fee payment page.
- Enter card details and submit payment in Stripe Checkout.

**Expected output:** Payment succeeds and the portal updates the fee status (typically to **paid**) via webhook processing.

Fig (5.1) Stripe Checkout Page (Student Site)  

---

### 6. Assignments List Page (Student Site)

**Purpose:** View available assignments by course/subject and check due dates/status.

**Steps:**
- Open **Assignments**.
- Browse the assignment list.
- Filter by subject/course if available.
- Select an assignment to view details and submit.

**Expected output:** Assignments are listed with status (open/closed) and deadlines.

Fig (6.1) Assignments List Page (Student Site)  

---

### 7. Assignment Detail & Submit Page (Student Site)

**Purpose:** View assignment instructions and submit (or resubmit) a file.

**Steps:**
- From Assignments list, open an assignment.
- Review allowed file types and maximum file size.
- Upload submission file and click **Submit**.
- If resubmission is allowed, upload a new file to replace the previous one.

**Expected output:** Submission is stored successfully and submission status updates (Submitted / Graded / Returned).

Fig (7.1) Assignment Detail & Submit Page (Student Site)  

---

## B) Teacher Site – Grade & Assignment Management

### 8. Teacher Grades Overview Page (Teacher Site)

**Purpose:** View grade tasks by subject and student, and track draft/pending/approved statuses.

**Steps:**
- Log in as Teacher.
- Open **Teacher Grades Overview**.
- Select a subject to enter grades or review submission status.

**Expected output:** Teacher sees subjects assigned to them and the grade workflow status per student.

Fig (8.1) Teacher Grades Overview Page (Teacher Site)  

---

### 9. Subject Grade Entry Page (Teacher Site)

**Purpose:** Enter or update draft grades and prepare them for review.

**Steps:**
- Select a subject from Teacher Grades Overview.
- Enter scores for students.
- Click **Save Draft** (if available).
- When ready, click **Submit for Review**.

**Expected output:** Grades are saved as **draft** and can be submitted to **pending** for staff review.

Fig (9.1) Subject Grade Entry Page (Teacher Site)  

---

### 10. Grade Review Status Page (Teacher Site)

**Purpose:** Monitor grade review outcomes (approved/rejected) and respond to rejections if allowed.

**Steps:**
- Open **Grade Review Status**.
- Review which grades are pending/approved/rejected.
- If rejected, update the grade if needed and resubmit.

**Expected output:** The review status is visible and any rejection reasons are displayed.

Fig (10.1) Grade Review Status Page (Teacher Site)  

---

### 11. Manage Assignments Page (Teacher Site)

**Purpose:** Create, edit, publish, and close assignments.

**Steps:**
- Open **Manage Assignments**.
- Click **Create Assignment** to add a new assignment (saved as draft).
- Click **Publish** to make it visible to students.
- Click **Close** to stop submissions.

**Expected output:** Assignment lifecycle changes correctly (draft → published → closed).

Fig (11.1) Manage Assignments Page (Teacher Site)  

---

### 12. Assignment Submissions Page (Teacher Site)

**Purpose:** Review student submissions and grade them.

**Steps:**
- Open an assignment from Manage Assignments.
- View the submissions list.
- Download/view a submission file.
- Enter score and feedback, then save.

**Expected output:** Submission status updates (e.g., graded/returned) and the student can view feedback.

Fig (12.1) Assignment Submissions Page (Teacher Site)  

---

### 13. Grade Submission Page (Teacher Site)

**Purpose:** Submit final grades for staff review/approval.

**Steps:**
- From Subject Grade Entry, select **Submit** / **Submit for Review**.
- Confirm submission.

**Expected output:** Grade status moves to **pending** and becomes available for staff review.

Fig (13.1) Grade Submission Page (Teacher Site)  

---

## C) Staff/Admin Site – Grade, Fee & Payment Management

### 14. Manage Grades Review Page (Admin Site)

**Purpose:** Review grades submitted by teachers and approve/reject them.

**Steps:**
- Log in as Staff/Admin.
- Open **Manage Grades Review**.
- Select a pending grade for detail review.

**Expected output:** Staff sees a list of submitted grades with filtering and review actions.

Fig (14.1) Manage Grades Review Page (Admin Site)  

---

### 15. Grade Detail & Review Log Page (Admin Site)

**Purpose:** Inspect grade details, review logs, and apply approval/rejection actions.

**Steps:**
- Open a grade from Manage Grades Review.
- Verify student/subject/score.
- Review the audit log (submitted/approved/rejected).
- Click **Approve** or **Reject** (provide reason if rejecting).

**Expected output:** Grade status updates, logs are recorded, and relevant users are notified (if enabled).

Fig (15.1) Grade Detail & Review Log Page (Admin Site)  

---

### 16. Manage Fees Page (Admin Site)

**Purpose:** Create, search, filter, and manage fee records.

**Steps:**
- Open **Manage Fees**.
- Search by student or fee description.
- Filter by status and overdue (if available).
- Click **Register/Edit Fee** to create or update a fee.

**Expected output:** Fee list updates correctly and changes persist.

Fig (16.1) Manage Fees Page (Admin Site)  

---

### 17. Register / Edit Fee Page (Admin Site)

**Purpose:** Register a new fee or update an existing fee record.

**Steps:**
- Select a student.
- Enter amount, description, and due date.
- Save the fee.

**Expected output:** Fee is created/updated with correct status and due date.

Fig (17.1) Register / Edit Fee Page (Admin Site)  

---

### 18. Late Payments Dashboard Page (Admin Site)

**Purpose:** Monitor overdue fees and prioritize follow-up actions.

**Steps:**
- Open **Late Payments Dashboard**.
- Review overdue/pending fees.
- Open a fee to take action (e.g., contact student, review payment evidence).

**Expected output:** Overdue fees are clearly highlighted and sortable/filterable.

Fig (18.1) Late Payments Dashboard Page (Admin Site)  

---

### 19. Payment Approval Page (Admin Site)

**Purpose:** Approve or reject student payment confirmations.

**Steps:**
- Open **Payment Approval**.
- View fee details and payment evidence/intent.
- Click **Approve** or **Reject** (with reason if rejecting).

**Expected output:** Fee status changes accordingly (e.g., payment_pending → paid, or payment_pending → pending/failed).

Fig (19.1) Payment Approval Page (Admin Site)  

---

### 20. Receipt Generation / Preview Page (Admin Site)

**Purpose:** Generate and preview a receipt for a paid fee.

**Steps:**
- Open a paid fee.
- Click **Generate Receipt** / **Preview**.
- Download or print the receipt (PDF) if supported.

**Expected output:** Receipt is generated successfully and can be saved/shared.

Fig (20.1) Receipt Generation / Preview Page (Admin Site)  

---

### 21. Assignment Monitoring Page (Admin Site) (Optional)

**Purpose:** Monitor assignment activity for oversight and reporting.

**Steps:**
- Open **Assignment Monitoring**.
- View assignment list and submission status by subject/course.

**Expected output:** Staff can see overall assignment progress and identify issues.

Fig (21.1) Assignment Monitoring Page (Admin Site)  

---

## Timebox 3: Manage Timetable, Attendance & Communication Process

This user manual describes how Guests, Students, Teachers, and Staff/Admin use the Academic Portal features delivered in **Timebox 3**. Screen names and figure placeholders follow `docs/TIMEBOX3_SCREEN_DESIGN.md`. Replace each figure placeholder with your actual screenshots in the final report.

---

## A) Guest / Public Site

### 1. Contact Us Page (Public Site)

**Purpose:** Allow guests to submit enquiries to the university via the portal.

**Steps:**
- Open the public site.
- Navigate to **Contact Us**.
- Fill in required fields (name, email, phone if required, subject, message).
- Click **Submit**.

**Expected output:** A success confirmation message is shown and the message is stored for staff review.

Fig (1.1) Contact Us Page (Public Site)  

---

### 2. Feedback Page (Public Site)

**Purpose:** Allow guests to submit feedback about the portal or services.

**Steps:**
- Navigate to **Feedback**.
- Enter name, email, feedback type (if provided), and message.
- Click **Submit**.

**Expected output:** A success confirmation message is shown and feedback is stored for staff review.

Fig (2.1) Feedback Page (Public Site)  

---

## B) Student Site

### 3. Student Timetable Page (Student Site)

**Purpose:** View timetable sessions for enrolled course(s) in a week grid layout.

**Steps:**
- Log in as Student.
- Open **Timetable**.
- Review sessions by day/time; use filters (course/subject) if available.

**Expected output:** Timetable displays correctly for the student’s enrolled course(s), ordered by day/time.

Fig (3.1) Student Timetable Page (Student Site)  

---

### 4. Student Attendance Page (Student Site)

**Purpose:** View attendance statistics and recent attendance records.

**Steps:**
- Open **Attendance**.
- Review overall attendance rate and breakdown by course/subject.
- View recent attendance records and statuses (present/absent).

**Expected output:** Attendance summaries and records display correctly for the logged-in student.

Fig (4.1) Student Attendance Page (Student Site)  

---

### 5. Announcements Page (Student Site)

**Purpose:** View announcements targeted to the student role and currently visible announcements.

**Steps:**
- Open **Announcements**.
- Browse pinned/priority announcements first.
- Select an announcement to view details.

**Expected output:** Student sees only announcements visible to them, in correct priority order.

Fig (5.1) Announcements Page (Student Site)  

---

### 6. Announcement Detail / Acknowledge Page (Student Site)

**Purpose:** Read an announcement and acknowledge it if required.

**Steps:**
- Open an announcement from the list.
- Read the content.
- If acknowledgement is required, click **Acknowledge**.

**Expected output:** The announcement is marked as read; acknowledgement timestamp is recorded when required.

Fig (6.1) Announcement Detail / Acknowledge Page (Student Site)  

---

### 7. Messages Inbox Page (Student Site)

**Purpose:** View received and sent messages, and read message details.

**Steps:**
- Open **Messages**.
- Select a message thread or message.
- Read content; unread messages become read automatically (or via a button depending on design).

**Expected output:** Inbox shows messages ordered by newest first, with read/unread indicators.

Fig (7.1) Messages Inbox Page (Student Site)  

---

### 8. Compose Message Page (Student Site)

**Purpose:** Send a message to another user (teacher/staff) where permitted.

**Steps:**
- Click **Compose**.
- Choose a recipient.
- Enter message body.
- Click **Send**.

**Expected output:** Message is sent successfully and appears in Sent/Inbox lists.

Fig (8.1) Compose Message Page (Student Site)  

---

### 9. Notifications Page (Student Site)

**Purpose:** View system notifications such as timetable updates, attendance alerts, grade/fee updates (if enabled).

**Steps:**
- Open **Notifications**.
- Review unread notifications first.
- Mark a notification as read (or mark all as read if provided).

**Expected output:** Notifications are listed with correct timestamps and read status.

Fig (9.1) Notifications Page (Student Site)  

---

## C) Teacher Site

### 10. Teacher Timetable Page (Teacher Site)

**Purpose:** View timetable entries for subjects assigned to the teacher.

**Steps:**
- Log in as Teacher.
- Open **Timetable**.
- Review sessions by subject/course and by day/time.

**Expected output:** Teacher sees timetable for assigned subjects only.

Fig (10.1) Teacher Timetable Page (Teacher Site)  

---

### 11. Record Attendance Page (Teacher Site)

**Purpose:** Record attendance for a subject on a specific date.

**Steps:**
- Open **Record Attendance**.
- Select subject and date (if required).
- Mark each student as Present/Absent (use bulk actions if available).
- Click **Save**.

**Expected output:** Attendance records are created/updated; students may receive alerts/notifications where enabled.

Fig (11.1) Record Attendance Page (Teacher Site)  

---

### 12. Teacher Attendance Summary Page (Teacher Site)

**Purpose:** Review attendance summaries for subjects and enrolled students.

**Steps:**
- Open **Attendance Summary**.
- Select a subject.
- Review per-student totals and attendance percentage.

**Expected output:** Teacher can see attendance rates and identify low-attendance students.

Fig (12.1) Teacher Attendance Summary Page (Teacher Site)  

---

### 13. Manage Announcements Page (Teacher Site)

**Purpose:** View and manage announcements created by the teacher (if allowed).

**Steps:**
- Open **Manage Announcements**.
- View existing announcements.
- Click **Create** to add a new announcement or **Edit** to update.

**Expected output:** Teacher announcements list displays correctly and supports navigation to create/edit.

Fig (13.1) Manage Announcements Page (Teacher Site)  

---

### 14. Create / Edit Announcement Page (Teacher Site)

**Purpose:** Create or edit an announcement with audience, priority, and visibility settings.

**Steps:**
- Enter Title and Body.
- Select priority and pinned options (if available).
- Set audience/visibility dates (if enabled).
- Click **Publish/Save**.

**Expected output:** Announcement is created/updated and appears to the intended audience.

Fig (14.1) Create / Edit Announcement Page (Teacher Site)  

---

### 15. Messages Inbox Page (Teacher Site)

**Purpose:** View messages from students/staff and reply as needed.

**Steps:**
- Open **Messages**.
- Read incoming messages.
- Compose and send replies.

**Expected output:** Messaging works with correct sender/receiver visibility and read status.

Fig (15.1) Messages Inbox Page (Teacher Site)  

---

## D) Staff/Admin Site – Timetable, Attendance & Communication

### 16. Manage Timetables Page (Admin Site)

**Purpose:** View all timetable entries and manage them (create/edit/delete).

**Steps:**
- Log in as Staff/Admin.
- Open **Manage Timetables**.
- Use search/filters to locate entries.
- Click **Register/Edit** to update or **Delete** to remove (with confirmation).

**Expected output:** Timetable list updates correctly and conflicts are prevented according to rules.

Fig (16.1) Manage Timetables Page (Admin Site)  

---

### 17. Register / Edit Timetable Page (Admin Site)

**Purpose:** Create or update timetable entries with validation and conflict checking.

**Steps:**
- Select a Subject.
- Set day of week, start time, end time, and location.
- Click **Save**.

**Expected output:** Timetable entry is saved if no conflicts; notifications may be sent to students/teachers where implemented.

Fig (17.1) Register / Edit Timetable Page (Admin Site)  

---

### 18. Attendance Report Dashboard Page (Admin Site)

**Purpose:** View attendance reporting across courses/subjects and identify low attendance.

**Steps:**
- Open **Attendance Report Dashboard**.
- Review overall statistics and breakdowns.
- Use filters (course/subject/date range) if available.

**Expected output:** Reports display correctly and highlight low attendance students based on thresholds.

Fig (18.1) Attendance Report Dashboard Page (Admin Site)  

---

### 19. Low Attendance Alerts Management Page (Admin Site)

**Purpose:** Manage and dispatch low attendance alerts (threshold/cooldown logic).

**Steps:**
- Open **Low Attendance Alerts Management**.
- Review alert list/state.
- Trigger alert dispatch action if available.

**Expected output:** Alerts are sent and alert state updates (cooldown respected).

Fig (19.1) Low Attendance Alerts Management Page (Admin Site)  

---

### 20. Manage Announcements Page (Admin Site)

**Purpose:** Create, edit, and manage all announcements (staff/admin).

**Steps:**
- Open **Manage Announcements**.
- Search/filter announcements.
- Navigate to create/edit pages.

**Expected output:** Announcements list displays with correct status, priority, and pinned indicators.

Fig (20.1) Manage Announcements Page (Admin Site)  

---

### 21. Create / Edit Announcement Page (Admin Site)

**Purpose:** Publish announcements to selected audiences with scheduling/expiry options.

**Steps:**
- Enter title/body.
- Select audience (student/teacher/staff/all), priority, pinned, require acknowledgement.
- Set publish and expiry dates (if used).
- Click **Publish/Save**.

**Expected output:** Announcement appears to the correct users during its visibility window.

Fig (21.1) Create / Edit Announcement Page (Admin Site)  

---

### 22. Contact Messages Inbox Page (Admin Site)

**Purpose:** Review and manage contact messages submitted by guests.

**Steps:**
- Open **Contact Messages Inbox**.
- Search messages and open details.
- Mark messages as read and record reply info if used.

**Expected output:** Messages are manageable (unread/read), searchable, and paginated.

Fig (22.1) Contact Messages Inbox Page (Admin Site)  

---

### 23. Feedback Messages Inbox Page (Admin Site)

**Purpose:** Review and manage guest feedback submissions.

**Steps:**
- Open **Feedback Messages Inbox**.
- Search feedback and open details.
- Mark as read and track reply status if used.

**Expected output:** Feedback messages display correctly with unread/read management.

Fig (23.1) Feedback Messages Inbox Page (Admin Site)  

---

### 24. Notifications Page (Admin Site)

**Purpose:** View system notifications and manage read status.

**Steps:**
- Open **Notifications**.
- Review notifications.
- Mark one or all as read if available.

**Expected output:** Notifications list updates correctly and unread items decrease as read.

Fig (24.1) Notifications Page (Admin Site)  

---

### 25. Messages Inbox Page (Admin Site)

**Purpose:** View and respond to messages relevant to staff/admin users.

**Steps:**
- Open **Messages**.
- Read incoming messages.
- Compose and send replies as needed.

**Expected output:** Messaging works with correct sender/receiver roles and read indicators.

Fig (25.1) Messages Inbox Page (Admin Site)  

---

## Appendix Note

- Timebox 1: For remaining administration screens (Manage Students, Course Management, Subject Management, Course Registration Management), include the screenshots and short step lists in the appendix section using the same format.
- Timebox 2 and Timebox 3: For smaller supporting dialogs and variations (filters, confirmation prompts, minor sub-pages), include screenshots and short steps in the appendix section using the same format.

