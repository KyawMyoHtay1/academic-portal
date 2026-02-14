# 4.3 MOSCOW Prioritization – Manage Assignment (add to existing 4.3)

Add the following **Manage Assignment** blocks into your existing section 4.3 in the appropriate places (Must / Should / Could / Would).

---

## Must

**Manage Assignment**

| Feature | Justification |
|--------|----------------|
| Create Assignment – Teacher | Allow teachers to create assignments for their subjects |
| - Validate Teacher is assigned to Subject | Ensure proper authorization |
| - Check Text Fields – title required, max 255 chars | Ensure assignment has a title |
| - Validate Due Date – required, date format | Set submission deadline |
| - Validate Max Score – integer, min 1, max 1000 | Ensure valid grading scale |
| - Validate Status – enum: draft, published, closed | Control visibility and submission |
| - Store Assignment linked to Subject and Course | Correct course/subject association |
| - Set created_by to current User | Track who created the assignment |
| Publish Assignment – Teacher | Make assignment visible and submittable by students |
| - Validate ownership – only creator can publish | Ensure proper authorization |
| - Set status to published | Allow students to view and submit |
| Submit Assignment – Student | Allow students to hand in work |
| - Validate Student is enrolled in assignment's course | Ensure only enrolled students can submit |
| - Validate assignment is open (published and not overdue) | Enforce deadlines |
| - Validate file – required | Ensure a file is uploaded |
| - Store file in storage | Persist submission |
| - One submission per student per assignment (create or update) | Prevent duplicate submissions; allow resubmission |
| Grade Submission – Teacher | Allow teachers to score and give feedback |
| - Validate Teacher is owner of the Assignment | Ensure proper authorization |
| - Validate Score – numeric, min 0, max assignment max_score | Ensure valid score |
| - Set graded_by and graded_at | Track who graded and when |
| - Set submission status to graded | Mark as graded |
| View Assignments – Student | Students see assignments for their courses |
| - Show only published assignments for enrolled courses | Display relevant assignments only |
| - Include subject and course details | Provide context |
| - Show submission status and score/feedback if graded | Show progress and results |
| - Order by due date | Most urgent first |
| View Assignments – Teacher | Teachers see their assignments |
| - List Subjects teacher is assigned to | Show only assigned subjects |
| - Show assignments per subject with due date, status, submission count | Track workload and progress |

---

## Should

**Manage Assignment**

| Feature | Justification |
|--------|----------------|
| Update Assignment – Teacher | Keep assignment details current |
| - Validate ownership – only creator can update | Ensure proper authorization |
| - Validate all fields same as Create | Consistent validation |
| Delete Assignment – Teacher | Remove assignments when no longer needed |
| - Validate ownership – only creator can delete | Ensure proper authorization |
| - Confirm Deletion | Prevent accidental removal |
| - Cascade delete submission records and stored files | Maintain data integrity |
| View Assignment Detail – Student | Student sees full assignment and own submission |
| - Validate Assignment is published | Prevent access to draft/closed |
| - Validate Student is enrolled in assignment's course | Ensure authorization |
| - Display assignment details and existing submission if any | Complete information |
| View Submissions – Teacher | Teachers see all submissions for an assignment |
| - Validate assignment ownership | Ensure proper authorization |
| - List submissions with student info, file, score, feedback, status, dates | Complete grading view |
| Download Submission – Teacher | Teachers can open submitted files |
| - Validate assignment ownership | Ensure proper authorization |
| - Allow download of student submission file | Review work offline |
| Download Own Submission – Student | Students can retrieve their submitted file |
| - Validate submission belongs to current Student | Ensure authorization |
| - Allow download of own submitted file | Student keeps a copy |
| Submit Assignment – Student | |
| - Validate file type against assignment allowed_file_types | Ensure acceptable formats |
| - Validate file size against assignment max_file_size | Prevent oversized uploads |
| - Validate comments – optional, max 2000 chars | Limit comment length |
| - On resubmission, replace file and reset grading fields | Teacher grades latest version |

---

## Could

**Manage Assignment**

| Feature | Justification |
|--------|----------------|
| Create Assignment – Teacher | |
| - Validate Description – optional | Allow longer instructions |
| - Validate Due Time – optional, format H:i | Allow time-of-day deadline |
| - Validate Allowed File Types – optional array (e.g. pdf, doc, docx, txt, zip, rar) | Restrict submission formats per assignment |
| - Validate Max File Size – optional, integer in KB, min 1, max 10240 (10MB) | Restrict file size per assignment |
| Update Assignment – Teacher | |
| - Validate all fields same as Create | Consistent validation |

---

## Would

**Manage Assignment**

| Feature | Justification |
|--------|----------------|
| View Assignments – Student | |
| - Toggle or filter by subject/course | User preference for large lists |
| View Assignments – Teacher | |
| - Export submissions list (e.g. CSV) | Offline reporting |

---

## Where to insert in your 4.3 document

- **Must:** Add the “Manage Assignment” block after **Manage Attendance** (before the “Should” section).
- **Should:** Add the “Manage Assignment” block after **Manage Attendance** under Should (e.g. after “Show recent records – last 30 days, limit 50”).
- **Could:** Add the “Manage Assignment” block after **Manage Announcement** under Could (e.g. after “Acknowledge Announcement”).
- **Would:** Add the “Manage Assignment” block after **Manage Fee** under Would (e.g. after “Toggle between Cards view and Table view”).
