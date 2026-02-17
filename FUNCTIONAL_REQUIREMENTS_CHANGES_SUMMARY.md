# Functional Requirements Changes Summary

## What Needs to Change in Your Documentation

Based on the computed grade feature implementation, here are the **exact changes** you need to make to your functional requirements document.

---

## 5.2.1 Functional Requirement List 2 - Manage Grades, Fee Payment & Assignment Process

### Section 2.1 Manage Grade (HL) (M)

**ADD these new Mid-Level (ML) requirements AFTER "Log Grade Review Actions (ML)":**

```
•	Calculate Computed Grade from Assignments (ML) - NEW
o	Retrieve all published assignments for a subject (LL)
o	Find graded submissions for the student (LL)
o	Calculate percentage for each graded assignment: (score / max_score) * 100 (LL)
o	Calculate average percentage from all graded assignments (LL)
o	Exclude ungraded assignments from calculation (do not count as 0) (LL)
o	Return computed grade (0-100), assignment breakdown, and statistics (LL)
o	Return null if no assignments have been graded (LL)
o	Round computed grade to 2 decimal places (LL)

•	Submit Final Grade - Teacher (ML) - NEW
o	Validate Teacher is assigned to Subject (LL)
o	Validate Student is enrolled in Subject's Course (LL)
o	Allow use of computed grade from assignments OR manual input (LL)
o	If using computed grade: validate computed grade exists (LL)
o	If using manual input: validate Score - numeric, min: 0, max: 100 (LL)
o	Create or update Grade record with status: pending (LL)
o	Set graded_by to current Teacher (LL)
o	Create Review Log entry with action 'submitted' (LL)
o	Include meta data: use_computed flag and computed_score if applicable (LL)
o	Notify all Staff users via GradeReviewRequested notification (LL)
```

**UPDATE "View Grade - Student (ML)" - ADD these lines:**

```
o	Display computed grade from assignments alongside final approved grade (LL)
o	Display assignment breakdown per subject (expandable) (LL)
o	Show assignment status: graded, submitted, not submitted (LL)
o	Show computed grade statistics: graded assignments count, total assignments (LL)
```

**UPDATE "View Grade - Teacher (ML)" - ADD these lines:**

```
o	Display computed grade from assignments per student (LL)
o	Display assignment breakdown per student (expandable) (LL)
o	Show assignment status and scores per assignment (LL)
o	Show computed grade statistics: graded assignments count, total assignments (LL)
o	Provide "Submit Final Grade" action per student (LL)
```

---

### Section 2.4 Manage Assignment (HL) (M)

**UPDATE "Grade Submission – Teacher (ML)" - ADD this note:**

```
o	Note: Assignment grades contribute to computed subject grade calculation (LL)
```

---

## 5.2.2 Use Case Diagram Updates

**ADD to the "Manage Grade" rectangle:**

```plantuml
    usecase "Calculate Computed Grade" as UC_CalculateComputedGrade
    usecase "Submit Final Grade" as UC_SubmitFinalGrade
```

**ADD actor relationships:**

```plantuml
Teacher --> UC_CalculateComputedGrade
Teacher --> UC_SubmitFinalGrade
Student --> UC_CalculateComputedGrade
```

**ADD to Use Case Descriptions table:**

```
| Calculate Computed Grade | Teacher, Student | System automatically calculates a suggested subject grade by retrieving all graded assignment submissions for the subject, converting each assignment score to a percentage (score/max_score * 100), and calculating the average of all percentages. The computed grade is displayed alongside the final approved grade. |
| Submit Final Grade | Teacher | Teacher views the grade management page showing computed grades from assignments. Teacher can either use the computed grade or enter a manual score. Teacher clicks "Submit Final Grade" button, which creates or updates a Grade record with status 'pending' and triggers the approval workflow. |
```

---

## CLASS_DIAGRAM_FUNCTIONS.md Updates

**ADD new class:**

```
## **SubjectGradeCalculator**

_Attributes: None (service class)_

- +CalculateSuggestedGrade(subjectId: int, studentId: int): array
- +GetAssignmentBreakdown(subjectId: int, studentId: int): array
```

**ADD to Summary Table:**

```
| **SubjectGradeCalculator**  | CalculateSuggestedGrade, GetAssignmentBreakdown                            |
```

**ADD to PlantUML diagram:**

```plantuml
class SubjectGradeCalculator {
  --
  +CalculateSuggestedGrade(subjectId: int, studentId: int): array
  +GetAssignmentBreakdown(subjectId: int, studentId: int): array
}

SubjectGradeCalculator ..> Assignment : uses
SubjectGradeCalculator ..> AssignmentSubmission : uses
```

---

## Files Already Updated

✅ **docs/TIMEBOX2_USE_CASE_DIAGRAM.md** - Updated with new use cases
✅ **CLASS_DIAGRAM_FUNCTIONS.md** - Updated with SubjectGradeCalculator class

---

## Quick Reference: What Changed

### New Features:
1. ✅ Computed grade calculation from assignments
2. ✅ Submit final grade using computed or manual input
3. ✅ Assignment breakdown display in grade views

### Updated Features:
1. ✅ View Grade - Student (now shows computed grades)
2. ✅ View Grade - Teacher (now shows computed grades + submit action)
3. ✅ Grade Submission - Teacher (note about contributing to computed grade)

### Documentation Files:
1. ✅ Use Case Diagram - Updated
2. ✅ Class Diagram - Updated
3. ⚠️ **Functional Requirements Document** - Needs manual update (see above)

---

## Implementation Status

✅ **Backend:** Fully implemented
✅ **Frontend:** Fully implemented
✅ **Use Case Diagram:** Updated
✅ **Class Diagram:** Updated
⚠️ **Functional Requirements:** Needs manual update (template provided above)

---

**Note:** The functional requirements document you provided appears to be in a separate document format. Use the template above to add the new requirements to your actual functional requirements document.
