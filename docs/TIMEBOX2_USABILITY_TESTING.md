# 5.2.7 Usability Testing
## Timebox 2: Manage Grades, Fee Payment & Assignment Process

Usability testing for Timebox 2 focused on how easily **students**, **teachers**, and **staff** can work with grades, fees, payments, and assignments. The evaluation followed **Nielsen’s 10 Usability Heuristics**; selected heuristics and examples from the academic portal are described below.

---

## Visibility of System Status

**Explain:** The system keeps users informed about grade, fee, payment, and assignment status at all times.

- On the **My Grades** page, students see grade status (e.g. *Approved*, *Pending Review*, *Rejected*) and GPA clearly, so they know which grades are final.
- On **Manage Grades (Teacher/Staff)** pages, status chips and filters (Draft, Pending, Approved, Rejected) show the current state of each grade and how many are awaiting action.
- On **My Fees**, each fee shows a clear status badge (*Pending*, *Payment Pending*, *Paid*, *Failed/Overdue*), with overdue fees highlighted.
- On the **Assignments** list, students see per-assignment status (*Not Submitted*, *Submitted*, *Graded*), due dates, and any late/closed indicators.

*[Insert screenshot: My Grades with status badges and GPA]*

---

## Match between System and Real World

**Explain:** The interface uses terminology and concepts familiar to students, teachers, and staff.

- Grade-related screens use academic terms such as **Score**, **Grade**, **GPA**, **Subject**, **Course**, and **Final Grade**, avoiding internal codes or technical jargon.
- Fee pages use terms like **Amount**, **Due Date**, **Paid Date**, **Receipt**, and **Overdue**, matching typical financial workflows.
- Assignment pages show **Title**, **Due Date**, **Max Score**, **Allowed File Types**, and **Feedback**, reflecting how assignments are described in a real classroom.

*[Insert screenshot: Teacher Grade Entry page showing Subject, Student, Score, Status]*  

---

## User Control and Freedom

**Explain:** Users can undo or exit actions and are not trapped in unwanted states.

- Teachers can **save grades as draft** before submitting them for review, allowing corrections without notifying staff or students.
- Staff can **approve or reject** grades and payments with confirmation dialogs, giving control over high‑impact actions.
- Students can **resubmit assignments** while the assignment is open; new submissions replace previous files and reset grading.
- Navigation breadcrumbs and menu links let users move between **Grades**, **Fees**, and **Assignments** without losing their place.

*[Insert screenshot: Grade Review page with Approve/Reject actions and Cancel/Back controls]*  

---

## Consistency and Standards

**Explain:** Patterns, labels, and layouts are reused across grades, fees, and assignments.

- Status badges (e.g. green for success, orange for pending, red for rejected/failed) are consistent across **grades**, **fees**, and **assignment submissions**.
- Tables for **Manage Grades**, **Manage Fees**, and **Assignment Submissions** share similar layouts: search at the top, filters on the side or top-right, pagination at the bottom.
- Buttons such as **Save**, **Submit**, **Approve**, **Reject**, and **Generate Receipt** follow consistent styling and placement across staff pages.

*[Insert screenshot: Status badges reused on Grades, Fees, and Assignments]*  

---

## Error Prevention

**Explain:** The system prevents common mistakes before they happen.

- Grade entry enforces **score ranges** (e.g. 0–100) and blocks invalid values, reducing the chance of incorrect grading.
- Fee creation and update validate **amount**, **status**, and **dates** before saving, preventing invalid financial records.
- Assignment submission validates **file type** and **file size** against per-assignment rules, preventing unusable uploads.
- Payment confirmation and Stripe payments guard against duplicate submissions by checking fee status (e.g. not already paid or payment_pending).

*[Insert screenshot: Validation message for invalid score or fee amount]*  

---

## Recognition rather than Recall

**Explain:** Options are visible so users don’t need to remember codes or complex rules.

- Dropdown filters for **Grade Status**, **Fee Status**, **Semester**, and **Subject** allow users to select rather than remember valid values.
- Assignment detail pages show **allowed file types** and **maximum file size** explicitly, instead of requiring students to recall constraints.
- Fee pages show the **current statistics** (e.g. Total, Paid, Pending, Overdue) so staff don’t have to calculate these numbers manually.

*[Insert screenshot: Filters for Grade Status and Fee Status]*  

---

## Flexibility and Efficiency of Use

**Explain:** Frequent users can work quickly while new users are still supported.

- Teachers can enter or update multiple grades from a **single grade entry screen**, with inline editing and bulk save actions.
- Staff can filter fees by **status, student, amount, and due date**, quickly focusing on **overdue** or **payment_pending** items.
- Students can see all relevant information in one place on **My Grades**, **My Fees**, and **Assignments**, reducing clicks and navigation overhead.

*[Insert screenshot: Teacher Grade Entry with inline editing and filters]*  

---

## Aesthetic and Minimalist Design

**Explain:** Timebox 2 screens present only the necessary information, grouped logically.

- Grade tables show only key fields: **Course**, **Subject**, **Score**, **Status**, and basic statistics; detailed logs are kept in a separate panel.
- Fee views separate **summary cards** (totals and counts) from **detailed lists**, so users can scan high-level information quickly.
- Assignment lists show **Title**, **Due Date**, **Status**, and **Score/Feedback**, avoiding clutter on the main page.

*[Insert screenshot: My Fees page with summary and clean table layout]*  

---

## Help Users Recognise, Diagnose, and Recover from Errors

**Explain:** Error messages are clear and help users correct problems.

- If a grade cannot be submitted because it is **locked** (already approved/pending), the message clearly explains why and what to do next.
- Payment errors (e.g. Stripe failures) are converted into friendly error messages rather than raw error codes.
- Assignment upload errors specify which **file types** or **sizes** are allowed so students can adjust and try again.
- Fee and grade validation messages identify exactly which field is incorrect.

*[Insert screenshot: Example of payment/grade validation error]*  

---

## Help and Documentation

**Explain:** Contextual hints guide users without requiring a separate manual.

- Labels and placeholders on grade and fee forms clarify required formats (e.g. **score 0–100**, **amount**, **due date**).
- Tooltips or helper text on Stripe-related buttons explain that payment will be processed securely and may take a moment.
- Assignment pages include short instructions about how submissions and resubmissions work.

*[Insert screenshot: Grade/Fee form with helper text and placeholders]*  

---

# 5.2.9 Iteration for Usability Testing

Iterations made after usability testing for Timebox 2, aligned with Nielsen’s heuristics.

---

## Iteration 1: Grade Overview Clarity (Visibility of System Status)

**Users said** that it was hard to quickly see which grades were pending review versus approved. Status information was displayed only in text.

**Change:** Status badges (Draft, Pending, Approved, Rejected) and a filter bar were added to the grade overview screens for teachers and staff, making review queues more visible.

**(Iteration 1) Iteration for Grade Overview Page**

| Before | After |
|--------|-------|
| *[Placeholder: Grade list without coloured status badges or filters]* | *[Placeholder: Grade list with coloured status badges and status filter tabs]* |

---

## Iteration 2: Fee Overview and Overdue Highlight (Match & Visibility)

**Users said** that the My Fees and Manage Fees pages did not clearly indicate overdue items or fee totals, requiring manual calculation.

**Change:** A summary panel (Total, Paid, Pending, Overdue) and visual overdue highlights (e.g. red labels) were added. Filters were adjusted so staff and students can quickly isolate problem fees.

**(Iteration 2) Iteration for My Fees and Manage Fees Pages**

| Before | After |
|--------|-------|
| *[Placeholder: Fees listed without summary or overdue highlight]* | *[Placeholder: Fees with summary cards and overdue badges]* |

---

## Iteration 3: Assignment Detail & Submission Guidance (Error Prevention)

**Users said** that the Assignment Detail page did not clearly show allowed file types, maximum file sizes, or whether resubmission was allowed, leading to upload errors and confusion.

**Change:** The page was updated to show allowed file extensions, maximum size, due date/time, and a short note about resubmission behaviour. Validation messages were improved to reference these limits.

**(Iteration 3) Iteration for Assignment Detail Page**

| Before | After |
|--------|-------|
| *[Placeholder: Assignment detail without file/size guidance]* | *[Placeholder: Assignment detail with allowed types, max size, and resubmission note]* |

---

## Iteration 4: Payment Approval Feedback (User Control and Freedom)

**Users said** that after approving or rejecting payments, it was not obvious what changed or whether an action succeeded. Students were also unsure of their updated fee status.

**Change:** The Payment Approval page was updated to show inline success/error toasts, status badges updating in-place, and clearer labels. Student-facing My Fees view was updated to immediately reflect new statuses and show a confirmation message.

**(Iteration 4) Iteration for Payment Approval and My Fees Pages**

| Before | After |
|--------|-------|
| *[Placeholder: Payment approval without clear feedback]* | *[Placeholder: Payment approval with status badges and confirmation messages]* |

---

## Iteration 5: My Grades Filters and Sorting (Flexibility & Efficiency)

**Users said** that the My Grades page was difficult to use when many grades were present, and they wanted to focus on specific semesters or subjects.

**Change:** Filters for **Semester**, **Course**, and **Status**, plus sorting by **Course Code** and **Score**, were added to My Grades. This makes it easier to find relevant grades and understand GPA per period.

**(Iteration 5) Iteration for My Grades Page**

| Before | After |
|--------|-------|
| *[Placeholder: My Grades without filters or sort controls]* | *[Placeholder: My Grades with filters (semester, course, status) and sort options]* |

