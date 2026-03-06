# 6.3.2 User Manual

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

## Appendix Note

For additional supporting screens (filters, modals, confirmation dialogs, and minor sub-pages), place the screenshots and short steps in the appendix section of your report using the same structure as above.

