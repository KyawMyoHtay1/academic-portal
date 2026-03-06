# 6.3.2 User Manual

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

## Appendix Note

For remaining Timebox 1 administration screens (e.g., **Manage Students**, **Course Management**, **Subject Management**, and detailed **Course Registration Management** screens), include the screenshots and short step lists in the appendix section of your report using the same format as above.

