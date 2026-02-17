# Updated Functional Requirements - Computed Grade Feature

## Changes Made to Functional Requirements

This document outlines the updates needed to the functional requirements documentation to reflect the new **Computed Grade from Assignments** feature.

---

## 5.2.1 Functional Requirement List 2 - Updates

### 2.1 Manage Grade (HL) (M) - **UPDATED**

Add the following new Mid-Level (ML) requirements:

#### • Calculate Computed Grade from Assignments (ML) - **NEW**
o	Retrieve all published assignments for a subject (LL)
o	Find graded submissions for the student (LL)
o	Calculate percentage for each graded assignment: (score / max_score) * 100 (LL)
o	Calculate average percentage from all graded assignments (LL)
o	Exclude ungraded assignments from calculation (do not count as 0) (LL)
o	Return computed grade (0-100), assignment breakdown, and statistics (LL)
o	Return null if no assignments have been graded (LL)
o	Round computed grade to 2 decimal places (LL)
#### Record Grade - Teacher (ML) - **UPDATED**
o	Validate current User has Teacher role and is assigned to Subject (LL)
o	Save entered scores as draft status (LL)
o	Draft save does not create review log and does not notify Staff (LL)
o	Pending and approved grades are locked from teacher editing (LL)
o	Store graded_by as current Teacher who last edited the grade (LL)

#### • Submit Final Grade - Teacher (ML) - **NEW**
o	Validate Teacher is assigned to Subject (LL)
o	Validate Student is enrolled in Subject's Course (LL)
o	Allow use of computed grade from assignments OR manual input (LL)
o	If using computed grade: validate computed grade exists (LL)
o	If using manual input: validate Score - numeric, min: 0, max: 100 (LL)
o	Create or update Grade record with status: pending (LL)
o	Set graded_by to current Teacher (LL)
o	Allow submission only when grade status is draft or rejected (LL)
o	Prevent re-submission when grade status is pending or approved (locked) (LL)
o	Create Review Log entry with action 'submitted' (LL)
o	Include meta data: use_computed flag and computed_score if applicable (LL)
o	Notify all Staff users via GradeReviewRequested notification (LL)

#### • View Grade - Student (ML) - **UPDATED**
o	Show only approved grades (LL)
o	Group grades by Course with Subjects (LL)
o	Display Course Code, Title, Credits, Semester (LL)
o	Display Subject Code, Title, and Score (LL)
o	**Display computed grade from assignments alongside final approved grade (LL)** - **NEW**
o	**Display assignment breakdown per subject (expandable) (LL)** - **NEW**
o	**Show assignment status: graded, submitted, not submitted (LL)** - **NEW**
o	**Show computed grade statistics: graded assignments count, total assignments (LL)** - **NEW**
o	Calculate and display overall GPA (LL)
o	Show total count of approved grades (LL)

#### • View Grade - Teacher (ML) - **UPDATED**
o	List Subjects teacher is assigned to (LL)
o	Show Students and existing grades for each Subject (LL)
o	**Display computed grade from assignments per student (LL)** - **NEW**
o	**Display assignment breakdown per student (expandable) (LL)** - **NEW**
o	**Show assignment status and scores per assignment (LL)** - **NEW**
o	**Show computed grade statistics: graded assignments count, total assignments (LL)** - **NEW**
o	Display graded count, ungraded count, average, highest, lowest (LL)
o	Color-coded grades based on letter grade ranges (LL)
o	**Provide "Submit Final Grade" action per student (LL)** - **NEW**

---

### 2.4 Manage Assignment (HL) (M) - **UPDATED**

#### • Grade Submission – Teacher (ML) - **UPDATED**
o	Validate Teacher is owner of the Assignment (LL)
o	Validate Score – numeric, min 0, max assignment max_score (LL)
o	Validate Feedback – optional, max 5000 chars (LL)
o	Set graded_by and graded_at (LL)
o	Set submission status to graded (LL)
o	**Note: Assignment grades contribute to computed subject grade calculation (LL)** - **NEW**

---

## Use Case Diagram Updates

### 5.2.2 Use Case Diagram – Timebox 2 - **UPDATED**

Add the following use cases to the "Manage Grade" rectangle:

```plantuml
  rectangle "Manage Grade" {
    usecase "Record Grade" as UC_RecordGrade
    usecase "Approve Grade" as UC_ApproveGrade
    usecase "Reject Grade" as UC_RejectGrade
    usecase "View Grade" as UC_ViewGrade
    usecase "Search Grade" as UC_SearchGrade
    usecase "Calculate Computed Grade" as UC_CalculateComputedGrade
    usecase "Submit Final Grade" as UC_SubmitFinalGrade
  }
```

Add actor relationships:

```plantuml
Teacher --> UC_CalculateComputedGrade
Teacher --> UC_SubmitFinalGrade
Student --> UC_CalculateComputedGrade
```

### Updated Use Case Descriptions

Add to Section A: Use Case Descriptions:

| Use Case Name | Actor | Flow of Event |
|---------------|-------|----------------|
| Calculate Computed Grade | Teacher, Student | System automatically calculates a suggested subject grade by retrieving all graded assignment submissions for the subject, converting each assignment score to a percentage (score/max_score * 100), and calculating the average of all percentages. The computed grade is displayed alongside the final approved grade. |
| Submit Final Grade | Teacher | Teacher views the grade management page showing computed grades from assignments. Teacher can either use the computed grade or enter a manual score. Teacher clicks "Submit Final Grade" button, which creates or updates a Grade record with status 'pending' and triggers the approval workflow. |

---

## Summary of Changes

### New Requirements Added:
1. ✅ **Calculate Computed Grade from Assignments (ML)** - Calculates suggested grade from assignment scores
2. ✅ **Submit Final Grade - Teacher (ML)** - Allows teachers to submit final grade using computed or manual input

### Updated Requirements:
1. ✅ **View Grade - Student (ML)** - Now shows computed grades and assignment breakdown
2. ✅ **View Grade - Teacher (ML)** - Now shows computed grades, assignment breakdown, and submit final grade action
3. ✅ **Grade Submission – Teacher (ML)** - Note added that assignment grades contribute to computed grade

### Use Case Diagram Updates:
1. ✅ Added "Calculate Computed Grade" use case
2. ✅ Added "Submit Final Grade" use case
3. ✅ Updated actor relationships

---

## Implementation Notes

### Architecture Decision:
- **Option C (Hybrid Approach)** was implemented:
  - Assignment grades remain in `assignment_submissions` table
  - Final grades go through approval workflow in `grades` table
  - Computed grades are calculated on-the-fly (not stored)
  - Teachers can use computed grade or enter manual grade when submitting final grade

### Key Features:
- ✅ Computed grade calculation excludes ungraded assignments (doesn't count as 0)
- ✅ Computed grade is displayed as "suggested" alongside final approved grade
- ✅ Assignment breakdown shows all assignments with status indicators
- ✅ Final grade submission triggers existing approval workflow
- ✅ Clean audit trail maintained via GradeReviewLog

---

## Documentation Files to Update

1. ✅ **Functional Requirements Document** - Add new ML requirements under 2.1 Manage Grade
2. ✅ **Use Case Diagram (5.2.2)** - Add new use cases and actor relationships
3. ✅ **Class Diagram** - Add SubjectGradeCalculator class (already documented in CLASS_DIAGRAM_FUNCTIONS.md)
4. ✅ **Sequence Diagram** - Optional: Add sequence for computed grade calculation flow

---

**Last Updated:** Based on implementation completed on [Current Date]
**Status:** ✅ Implemented and tested

