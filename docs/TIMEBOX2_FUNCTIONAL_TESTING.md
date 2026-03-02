# 5.2.6 Functional Testing
## Timebox 2 – Manage Grades, Fee Payment & Assignment Process

### 5.2.6.1 Scope

Functional testing for Timebox 2 was conducted to verify that the system correctly implements the **grade management**, **fee and payment management**, and **assignment workflow** as defined in the Timebox 2 functional requirements and use cases. Testing focuses on:

- Grade lifecycle: draft entry by teacher, submission for review, staff approval/rejection, and student view of approved grades and GPA.
- Computed grade calculation from graded assignments via `SubjectGradeCalculator`.
- Fee lifecycle: fee registration and update, late payment tracking, student fee views, and statistics.
- Payment lifecycle: student payment confirmation and Stripe online payment, staff approval/rejection, status logs, and receipt generation.
- Assignment lifecycle: teacher creates/updates/publishes assignments; students submit work; teachers grade submissions; students view feedback.

All tests are numbered and mapped to modules to ensure coverage of the Timebox 2 scope.

---

### 5.2.6.2 Coverage Summary Table

| Module ID | Module Name            | TC Range  | Total Test Cases |
|----------:|------------------------|-----------|------------------|
| M1        | Grade Management       | TC1.1–TC1.9 | 9 |
| M2        | Computed Grade & GPA   | TC2.1–TC2.4 | 4 |
| M3        | Fee Management         | TC3.1–TC3.8 | 8 |
| M4        | Payment & Receipt      | TC4.1–TC4.7 | 7 |
| M5        | Assignment Management  | TC5.1–TC5.9 | 9 |

**Total Functional Test Cases (Timebox 2): 37**

---

### 5.2.6.3 Test Design Technique

Test cases for Timebox 2 were designed using:

- **Equivalence partitioning** and **boundary value analysis** for score ranges, fee amounts, and status transitions.
- **Negative testing** to verify rejection of invalid scores, invalid payments, and unauthorized actions.
- **Role-based access testing** across Student, Teacher, and Staff roles.
- **Workflow testing** for end-to-end processes (grade submission → approval → student view; fee registration → payment → receipt; assignment creation → submission → grading → feedback).

---

### 5.2.6.4 Detailed Test Plan

#### Module M1 – Grade Management

| TC ID | Scenario                                      | Preconditions                                                                 | Input Data                                                       | Test Steps                                                                                                                                                                      | Expected Result                                                                                                   |
|------:|-----------------------------------------------|-------------------------------------------------------------------------------|------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------|
| TC1.1 | Save draft grade                             | Teacher is logged in and assigned to the subject; student is enrolled.       | subject_id, student_id, score = 75                              | 1) Open Grade Entry page for subject.<br>2) Enter score and click “Save Draft”.                                                                                               | Grade record created/updated with status = `draft`, `graded_by` set; no review log or staff notification.         |
| TC1.2 | Draft validation – invalid score             | Teacher is logged in and assigned; student enrolled.                          | score = 120 (out of 100)                                        | 1) Enter invalid score outside 0–100 range.<br>2) Click “Save Draft”.                                                                                                         | Validation message shown; grade not saved/updated.                                                                |
| TC1.3 | Submit final grade – new                     | Teacher is logged in and assigned; student enrolled; no existing grade.      | subject_id, student_id, score = 82                              | 1) Open Grade Entry page.<br>2) Click “Submit Final Grade”.                                                                                                                   | Grade created with status = `pending`; `graded_by` set; review log entry `submitted`; staff notified.             |
| TC1.4 | Submit final grade – from draft              | Draft grade exists with status `draft`.                                      | existing draft score = 70, submit with score = 88               | 1) Edit draft grade.<br>2) Click “Submit Final Grade”.                                                                                                                        | Grade updated to status = `pending` with new score; review log `submitted`; staff notified.                        |
| TC1.5 | Prevent resubmission when locked             | Grade exists with status `approved` or `pending`.                             | attempt Submit Final Grade again                                | 1) Open Grade Entry page for approved/pending grade.<br>2) Click “Submit Final Grade”.                                                                                       | Submission rejected; message indicates final grade is locked; no status change; no new review log.                |
| TC1.6 | Staff approves grade                         | Staff is logged in; pending grades exist.                                     | grade_id of grade with status `pending`                         | 1) Open Grade Review page.<br>2) Approve selected pending grade.                                                                                                             | Grade status changes `pending` → `approved`; `reviewed_by`/`reviewed_at` set; review log `approved`; student notified. |
| TC1.7 | Staff rejects grade                          | Staff is logged in; pending grades exist.                                     | grade_id with status `pending`, rejection_reason = “Incorrect”  | 1) Open Grade Review page.<br>2) Reject selected pending grade with reason.<br>3) Save.                                                                                      | Grade status changes `pending` → `rejected`; `rejection_reason` set; review log `rejected`; teacher notified.      |
| TC1.8 | Student views approved grades                | Student is logged in and has approved grades.                                 | N/A                                                              | 1) Open “My Grades” page.<br>2) Inspect course/subject list.                                                                                                                 | Only grades with status `approved` shown; grouped by course and subject with score and letter grade.              |
| TC1.9 | Teacher views grade overview for subject     | Teacher is assigned to subject; multiple students with grades/ungraded rows. | subject_id                                                      | 1) Open Grade Management page for subject.<br>2) Verify statistics and listing.                                                                                               | Page shows students, scores where present, counts of graded/ungraded, and aggregated statistics as designed.      |

---

#### Module M2 – Computed Grade & GPA

| TC ID | Scenario                                    | Preconditions                                                                 | Input Data                              | Test Steps                                                                                                                                                                    | Expected Result                                                                                         |
|------:|---------------------------------------------|-------------------------------------------------------------------------------|-----------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------|
| TC2.1 | Compute subject grade from assignments      | At least one `Assignment` with max_score set; student has graded submissions. | subject_id, student_id                  | 1) Student opens “My Grades” and selects subject.<br>2) System calls `SubjectGradeCalculator`.                                                                               | Computed grade = average percentage of graded submissions (score/max_score); breakdown per assignment shown. |
| TC2.2 | Computed grade excludes ungraded assignments | Some assignments have no graded submission for the student.                  | subject_id, student_id                  | 1) View grades for subject.<br>2) Compare assignments list vs breakdown.                                                                                                      | Unsubmitted or ungraded assignments are not included in the average; only graded submissions contribute. |
| TC2.3 | GPA calculation includes only approved grades | Student has multiple approved grades with non-null scores.                    | N/A                                     | 1) Open “My Grades” main view.<br>2) Check GPA display.                                                                                                                       | GPA is calculated as average of numeric scores for approved grades only; rounded to 2 decimal places.    |
| TC2.4 | GPA returns null/placeholder when no grades | Student has no approved grades with scores.                                  | N/A                                     | 1) Log in as student with no approved grades.<br>2) Open “My Grades”.                                                                                                        | GPA area shows “N/A” or equivalent placeholder (no divide-by-zero); no error is thrown.                |

---

#### Module M3 – Fee Management

| TC ID | Scenario                             | Preconditions                                                     | Input Data                                   | Test Steps                                                                                                                                 | Expected Result                                                                                                 |
|------:|--------------------------------------|-------------------------------------------------------------------|----------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------|
| TC3.1 | Register fee with valid data         | Staff is logged in; student record exists.                        | student_id, amount = 500.00, due_date        | 1) Open Register Fee page.<br>2) Enter valid student, amount, description, due date.<br>3) Save.                                         | Fee created with status = `pending`; appears in staff list and student My Fees; student notified.               |
| TC3.2 | Register fee validation errors       | Staff logged in.                                                  | missing student_id or invalid amount         | 1) Submit fee form with missing student or negative/invalid amount.<br>2) Save.                                                          | Validation messages displayed; fee not created.                                                                  |
| TC3.3 | Update fee and change status to paid | Existing fee in `pending` or `payment_pending`.                   | set status = `paid`, set paid_date           | 1) Open Edit Fee page.<br>2) Change status to `paid` and save.                                                                           | Status becomes `paid`; `paid_date` set (or auto-set to today); fee visible as paid in student view.             |
| TC3.4 | Auto-clear paid date when reverting  | Fee currently `paid`.                                             | change status from `paid` to `pending`       | 1) Edit fee; change status to `pending`.<br>2) Save.                                                                                     | Status = `pending`; `paid_date`, `payment_intent_id`, `payment_method`, `payment_processed_at`, `processed_by` cleared. |
| TC3.5 | Search fees (staff)                  | Multiple fees exist with different statuses and students.         | search by description, student name/number   | 1) Open Manage Fees page.<br>2) Enter keywords and apply filters (status, student).                                                     | Results filtered correctly; pagination and counts match; statistics panel shows totals by status.               |
| TC3.6 | View fees (student) with filters     | Student has multiple fees with different statuses and due dates.  | filter by status = pending/paid              | 1) Student opens My Fees page.<br>2) Filter by different statuses and search terms.                                                     | Student sees only own fees; filters and search work; overdue fees highlighted; statistics panel updated.        |
| TC3.7 | Overdue fee detection                | At least one fee with status `pending` and due_date < today.      | N/A                                          | 1) Staff opens Manage Fees or Late Payments view.<br>2) Inspect overdue section.                                                        | Overdue fees appear in Late Payments/overdue section with correct days overdue; counts match underlying data.   |
| TC3.8 | Fee status logs recorded             | Status transitions have been performed on one fee.                | N/A                                          | 1) Open Fee Detail / Status Log panel.<br>2) Review log entries.                                                                        | `fee_status_logs` shows from_status, to_status, action, note, performer, created_at for each transition.        |

---

#### Module M4 – Payment & Receipt

| TC ID | Scenario                                   | Preconditions                                                        | Input Data                                      | Test Steps                                                                                                                                  | Expected Result                                                                                              |
|------:|--------------------------------------------|----------------------------------------------------------------------|-------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------|
| TC4.1 | Submit manual payment confirmation         | Student has a fee with status `pending`.                             | feeId, proof/confirmation                      | 1) Student opens My Fees.<br>2) Click “Confirm Payment” on a pending fee.<br>3) Submit confirmation.                                      | Fee status changes `pending` → `payment_pending`; student sees updated status; entry logged in FeeStatusLog.  |
| TC4.2 | Prevent duplicate payment confirmation     | Fee already `payment_pending` or `paid`.                             | attempt second confirmation                     | 1) Try to submit confirmation again on payment_pending/paid fee.                                                                           | Action rejected; message indicates payment already pending/paid; no status change.                           |
| TC4.3 | Staff approves payment                     | Fee in `payment_pending` state.                                      | approve action                                  | 1) Staff opens Payment Approval list.<br>2) Approve selected fee.                                                                         | Status changes `payment_pending` → `paid`; `paid_date`, `processed_by` set; status log entry `paid`; student notified. |
| TC4.4 | Staff rejects payment                      | Fee in `payment_pending` state.                                      | reject action, optional note                    | 1) Staff rejects payment confirmation.<br>2) Save.                                                                                        | Status changes `payment_pending` → `pending`; payment-related fields cleared; status log entry `rejected`.    |
| TC4.5 | Stripe checkout – happy path               | Stripe configured; unpaid fee for student.                           | click “Pay with Card”                           | 1) Student clicks “Pay with Card”.<br>2) System creates Stripe Checkout Session, redirects to Stripe, then back on success URL.          | Fee moves to `payment_pending`/`paid` according to webhook; student sees paid status after successful payment. |
| TC4.6 | Stripe webhook processing                  | Stripe sends `checkout.session.completed` / `payment_intent.succeeded`. | webhook payload from Stripe                     | 1) Simulate Stripe webhook callbacks.<br>2) Inspect fee record and FeeStatusLog.                                                          | On success, fee marked `paid`, payment fields updated; on failure, status reverted to `pending` and logged.   |
| TC4.7 | Generate PDF receipt for paid fee          | Fee with status `paid` exists.                                       | feeId                                           | 1) Staff selects a paid fee.<br>2) Click “Generate Receipt”.                                                                              | System generates PDF receipt `receipt-{student_no}-{fee_id}.pdf` with all required fields and PAID badge.     |

---

#### Module M5 – Assignment Management

| TC ID | Scenario                                      | Preconditions                                                          | Input Data                                      | Test Steps                                                                                                                                  | Expected Result                                                                                              |
|------:|-----------------------------------------------|------------------------------------------------------------------------|-------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------|
| TC5.1 | Create assignment with valid data             | Teacher logged in and assigned to subject.                             | subject_id, title, due_date, max_score, status  | 1) Open Create Assignment page.<br>2) Enter valid data and save.                                                                          | Assignment created with status `draft`, linked to subject and course; visible in teacher’s list.              |
| TC5.2 | Assignment validation errors                  | Teacher logged in.                                                     | missing title or invalid due_date/max_score     | 1) Submit create form with missing/invalid fields.<br>2) Save.                                                                            | Validation errors displayed; assignment not created.                                                          |
| TC5.3 | Publish assignment                            | Draft assignment exists.                                               | assignmentId                                   | 1) From Manage Assignments, click “Publish”.                                                                                               | Status changes `draft` → `published`; assignment becomes visible to enrolled students.                        |
| TC5.4 | Close assignment                              | Published assignment exists.                                           | assignmentId                                   | 1) From Manage Assignments, click “Close”.                                                                                                 | Status changes `published` → `closed`; students can no longer submit.                                        |
| TC5.5 | Student submits assignment                     | Student enrolled in course; assignment is `published` and not overdue. | assignmentId, valid file, optional comments     | 1) Student opens Assignment Detail.<br>2) Upload valid file and submit.                                                                   | Submission record created/updated with status `submitted`; file stored; student sees confirmation.           |
| TC5.6 | Submission validation (file type/size)        | Assignment has allowed_file_types and max_file_size set.               | unsupported file type or size > max            | 1) Attempt to submit invalid file (type or size).                                                                                         | Validation error shown; submission not saved; previous submission (if any) remains unchanged.                |
| TC5.7 | Resubmission replaces previous file           | Existing submission in `submitted` or `graded` state.                  | new file upload and comments                   | 1) Student resubmits assignment.<br>2) Submit new file.                                                                                   | File path and metadata updated to new file; grading fields reset as designed; status set back to `submitted`. |
| TC5.8 | Teacher grades submission                     | One or more student submissions exist.                                 | submissionId, score, feedback                  | 1) Teacher opens Submissions list.<br>2) Enters valid score and feedback.<br>3) Save.                                                     | Submission updated with score, feedback, `graded_by`, `graded_at`, status `graded`; visible to student.      |
| TC5.9 | Student views graded submission and feedback  | Submission has been graded.                                            | N/A                                            | 1) Student opens Assignment Detail / My Submissions.<br>2) View submission.                                                              | Student sees uploaded file, score, and feedback; status shown as `graded` or equivalent.                     |

---

### 5.2.6.5 Test Execution Log

The following table summarises execution results for the Timebox 2 test cases. All tests were executed on the implemented system; screenshots and evidence are stored separately and referenced by figure IDs (Fig B01, Fig B02, …).

| TC ID | Input Data (summary)                                      | Status | Actual Result (summary)                                                                                     | Evidence  | Remarks               |
|------:|-----------------------------------------------------------|--------|------------------------------------------------------------------------------------------------------------|-----------|-----------------------|
| TC1.1 | Save draft grade with valid score                         | Pass   | Grade saved with status `draft`, `graded_by` set; no review log/notification created.                      | Fig B01   | Working as expected   |
| TC1.2 | Save draft grade with score = 120                         | Pass   | Validation error shown; grade not updated.                                                                 | Fig B02   | Working as expected   |
| TC1.3 | Submit final grade (new, score 82)                        | Pass   | Grade created with status `pending`; review log `submitted`; staff notification sent.                      | Fig B03   | Working as expected   |
| TC1.4 | Submit final grade from draft (score 88)                  | Pass   | Draft updated to `pending` with new score; review log `submitted`; staff notified.                         | Fig B04   | Working as expected   |
| TC1.5 | Try to resubmit when grade is approved                    | Pass   | Submission blocked; message indicates grade is locked; no status or log change.                            | Fig B05   | Working as expected   |
| TC1.6 | Staff approves pending grade                              | Pass   | Status `pending` → `approved`; `reviewed_by`/`reviewed_at` set; log `approved`; student notified.          | Fig B06   | Working as expected   |
| TC1.7 | Staff rejects pending grade                               | Pass   | Status `pending` → `rejected`; `rejection_reason` set; log `rejected`; teacher notified.                   | Fig B07   | Working as expected   |
| TC1.8 | Student views approved grades                             | Pass   | Only approved grades displayed; grouped by course/subject with score and letter grade.                     | Fig B08   | Working as expected   |
| TC1.9 | Teacher views grade overview                              | Pass   | Subject view shows students, graded/ungraded counts, averages, min/max correctly.                          | Fig B09   | Working as expected   |
| TC2.1 | Compute subject grade with several graded submissions     | Pass   | Computed percentage matches average of assignment percentages; breakdown correct.                          | Fig B10   | Working as expected   |
| TC2.2 | Computed grade with ungraded assignments                  | Pass   | Only graded submissions contribute; ungraded assignments ignored.                                          | Fig B11   | Working as expected   |
| TC2.3 | GPA with multiple approved grades                         | Pass   | GPA equals average of scores of approved grades; rounded to 2 decimals.                                    | Fig B12   | Working as expected   |
| TC2.4 | GPA for student with no approved grades                   | Pass   | GPA displays “N/A”/placeholder; no error thrown.                                                           | Fig B13   | Working as expected   |
| TC3.1 | Register fee with valid data                              | Pass   | Fee created with status `pending`; appears in staff/student views; notification sent.                      | Fig B14   | Working as expected   |
| TC3.2 | Register fee with invalid data                            | Pass   | Validation errors shown; fee not created.                                                                  | Fig B15   | Working as expected   |
| TC3.3 | Update fee to status = paid                               | Pass   | Status changed to `paid`; `paid_date` set; visible as paid for student.                                    | Fig B16   | Working as expected   |
| TC3.4 | Revert fee from paid to pending                           | Pass   | Status set to `pending`; payment fields cleared.                                                           | Fig B17   | Working as expected   |
| TC3.5 | Staff search/filter fees                                  | Pass   | Search and filters return correct rows and statistics.                                                     | Fig B18   | Working as expected   |
| TC3.6 | Student view/filter My Fees                               | Pass   | Only own fees shown; filters and search work; overdue fees highlighted; stats correct.                     | Fig B19   | Working as expected   |
| TC3.7 | Overdue fee listing                                       | Pass   | Overdue fees appear in late payments/overdue section with correct days overdue.                            | Fig B20   | Working as expected   |
| TC3.8 | Fee status logs                                           | Pass   | Fee status changes recorded in `fee_status_logs` with correct from/to status, action, performer, note.     | Fig B21   | Working as expected   |
| TC4.1 | Student submit payment confirmation                       | Pass   | Fee status `pending` → `payment_pending`; student sees updated status; status log entry created.           | Fig B22   | Working as expected   |
| TC4.2 | Duplicate payment confirmation on payment_pending/paid    | Pass   | Action blocked; no additional status change; message indicates payment already pending/paid.               | Fig B23   | Working as expected   |
| TC4.3 | Staff approves payment                                    | Pass   | Status `payment_pending` → `paid`; `paid_date`/`processed_by` set; student notified.                       | Fig B24   | Working as expected   |
| TC4.4 | Staff rejects payment                                     | Pass   | Status `payment_pending` → `pending`; payment fields cleared; student notified.                            | Fig B25   | Working as expected   |
| TC4.5 | Stripe checkout success                                   | Pass   | Checkout session created; on success, fee updated to paid via webhook; student sees paid status.           | Fig B26   | Working as expected   |
| TC4.6 | Stripe webhook events                                     | Pass   | Success events set status to paid; failure events revert to pending; events logged in `stripe_webhook_events`. | Fig B27   | Working as expected   |
| TC4.7 | Generate receipt for paid fee                             | Pass   | PDF receipt generated with correct filename and details; downloaded successfully.                           | Fig B28   | Working as expected   |
| TC5.1 | Create assignment                                         | Pass   | Assignment created with status `draft` and correct links to subject/course.                                | Fig B29   | Working as expected   |
| TC5.2 | Assignment create validation errors                       | Pass   | Validation errors shown; assignment not created.                                                           | Fig B30   | Working as expected   |
| TC5.3 | Publish assignment                                        | Pass   | Status `draft` → `published`; assignment visible to enrolled students.                                     | Fig B31   | Working as expected   |
| TC5.4 | Close assignment                                          | Pass   | Status `published` → `closed`; submissions no longer accepted.                                            | Fig B32   | Working as expected   |
| TC5.5 | Student submits assignment                                | Pass   | Submission created/updated with status `submitted`; file stored; confirmation shown.                       | Fig B33   | Working as expected   |
| TC5.6 | Invalid assignment submission (type/size)                 | Pass   | Invalid file rejected with error; submission not changed.                                                  | Fig B34   | Working as expected   |
| TC5.7 | Resubmission replaces file and resets grading             | Pass   | New file stored; grading fields reset; status set to `submitted`.                                          | Fig B35   | Working as expected   |
| TC5.8 | Teacher grades submission                                 | Pass   | Submission updated with score, feedback, `graded_by`, `graded_at`, status `graded`.                        | Fig B36   | Working as expected   |
| TC5.9 | Student views graded submission and feedback              | Pass   | Student sees file, score, and feedback; status displayed as graded.                                        | Fig B37   | Working as expected   |

---

### 5.2.6.6 Result Summary

| Module                   | Passed | Failed | Remarks                          |
|--------------------------|:------:|:------:|----------------------------------|
| Grade Management         |   9    |   0    | All planned test cases passed    |
| Computed Grade & GPA     |   4    |   0    | All planned test cases passed    |
| Fee Management           |   8    |   0    | All planned test cases passed    |
| Payment & Receipt        |   7    |   0    | All planned test cases passed    |
| Assignment Management    |   9    |   0    | All planned test cases passed    |

All 37 functional test cases for Timebox 2 passed. No critical defects were identified. Any minor issues discovered during testing were corrected and re-tested before finalising this report.

---

## Appendix B – Test Evidence (Screenshot Mapping)

Screenshots and output evidence for Timebox 2 tests are stored and referenced by figure IDs (Fig B01, Fig B02, …). Each figure is annotated to indicate the test case number and whether any corrective action was required.

| TC Range    | Evidence Range |
|-------------|----------------|
| TC1.1–TC1.9 | Fig B01–Fig B09 |
| TC2.1–TC2.4 | Fig B10–Fig B13 |
| TC3.1–TC3.8 | Fig B14–Fig B21 |
| TC4.1–TC4.7 | Fig B22–Fig B28 |
| TC5.1–TC5.9 | Fig B29–Fig B37 |

