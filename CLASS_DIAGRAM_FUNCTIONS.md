# Class Diagram Functions for University Academic Portal

Each class lists its suggested functions in the format: **+FunctionName()** — suitable for use in a class diagram (PlantUML, draw.io, StarUML, etc.).  
**Schema note:** *Timetable* and *Attendance* have no `course_id`; course is derived via Subject → Course (normalized schema). Use this document for both class diagram and ERD/entity-attribute lists.

---

## **User**
*Attributes: id, name, email, password, role, photo, email_verified_at*

- +FillGetData()
- +Login()
- +Logout()
- +Register()
- +UpdateProfile()
- +ResetPassword()
- +VerifyEmail()
- +GetRole()
- +AutoID()

---

## **Student**
*Attributes: id, user_id, student_no, full_name, dob, gender, email, phone, address, programme, intake_year, status, photo*

- +FillGetData()
- +AddStudent()
- +UpdateStudent()
- +DeleteStudent()
- +SearchStudent()
- +GetEnrollments()
- +GetGrades()
- +GetFees()
- +GetAttendance()
- +CalculateGPA()
- +AutoID()

---

## **Course**
*Attributes: id, course_code, title, credits, semester, photo*

- +FillGetData()
- +AddCourse()
- +UpdateCourse()
- +DeleteCourse()
- +SearchCourse()
- +GetSubjects()
- +GetEnrollments()
- +GetTimetables()
- +ValidateCourseCode()
- +AutoID()

---

## **Subject**
*Attributes: id, course_id, subject_code, title, credits, description, photo*

- +FillGetData()
- +AddSubject()
- +UpdateSubject()
- +DeleteSubject()
- +SearchSubject()
- +GetGrades()
- +GetTimetables()
- +GetAttendances()
- +ValidateSubjectCode()
- +AutoID()

---

## **Enrollment** (course_student)
*Attributes: id, course_id, student_id, status, timestamps*

- +FillGetData()
- +RequestEnrollment()
- +ApproveEnrollment()
- +RejectEnrollment()
- +RequestWithdrawal()
- +ApproveWithdrawal()
- +RejectWithdrawal()
- +SearchEnrollment()
- +GetByStudent()
- +GetByCourse()
- +AutoID()

---

## **course_teacher** (pivot)
*Attributes: id, course_id, user_id*

- +FillGetData()
- +AssignTeacher()
- +RemoveTeacher()
- +GetTeachersByCourse()
- +GetCoursesByTeacher()
- +AutoID()

---

## **subject_teacher** (pivot)
*Attributes: id, subject_id, user_id*

- +FillGetData()
- +AssignTeacher()
- +RemoveTeacher()
- +GetTeachersBySubject()
- +GetSubjectsByTeacher()
- +AutoID()

---

## **Grade**
*Attributes: id, subject_id, course_id, student_id, graded_by, reviewed_by, score, status, reviewed_at, rejection_reason*

- +FillGetData()
- +SubmitGrade()
- +ApproveGrade()
- +RejectGrade()
- +UpdateScore()
- +SearchGrade()
- +GetLetterGrade()
- +GetByStudent()
- +GetBySubject()
- +AutoID()

---

## **GradeReviewLog**
*Attributes: id, grade_id, performed_by, action, reason, meta, timestamps*

- +FillGetData()
- +LogAction()
- +GetByGrade()
- +SearchLog()
- +AutoID()

---

## **Fee**
*Attributes: id, student_id, amount, description, status, due_date, paid_date, payment_intent_id, payment_method, payment_processed_at, processed_by*

- +FillGetData()
- +CreateFee()
- +UpdateFee()
- +DeleteFee()
- +ApprovePayment()
- +RejectPayment()
- +GenerateReceipt()
- +SearchFee()
- +GetLatePayments()
- +AutoID()

---

## **Timetable**
*Attributes: id, subject_id, day_of_week, start_time, end_time, location*  
*(Course is derived via Subject → Course; no course_id column.)*

- +FillGetData()
- +AddTimetable()
- +UpdateTimetable()
- +DeleteTimetable()
- +SearchTimetable()
- +CheckConflict()
- +GetByCourse()
- +GetBySubject()
- +GetByStudent()
- +GetByTeacher()
- +AutoID()

---

## **Attendance**
*Attributes: id, subject_id, student_id, date, status*  
*(Course is derived via Subject → Course; no course_id column.)*

- +FillGetData()
- +RecordAttendance()
- +UpdateAttendance()
- +MarkPresent()
- +MarkAbsent()
- +SearchAttendance()
- +GetByStudent()
- +GetBySubject()
- +GetByDate()
- +CalculateRate()
- +AutoID()

---

## **Announcement**
*Attributes: id, user_id, title, body, priority, pinned, require_ack, audience, publish_at, expires_at*

- +FillGetData()
- +CreateAnnouncement()
- +UpdateAnnouncement()
- +DeleteAnnouncement()
- +Publish()
- +SearchAnnouncement()
- +GetVisibleToUser()
- +IsExpired()
- +AutoID()

---

## **AnnouncementRead**
*Attributes: id, announcement_id, user_id, read_at, acknowledged_at*

- +FillGetData()
- +MarkAsRead()
- +MarkAsAcknowledged()
- +GetReadStatus()
- +GetByUser()
- +GetByAnnouncement()
- +AutoID()

---

## **Message**
*Attributes: id, sender_id, receiver_id, body, read, timestamps*

- +FillGetData()
- +SendMessage()
- +MarkAsRead()
- +DeleteMessage()
- +GetInbox()
- +GetSent()
- +SearchMessages()
- +GetConversation()
- +AutoID()

---

## **Notification** (Laravel)
*Attributes: id, notifiable_type, notifiable_id, type, data, read_at*

- +FillGetData()
- +MarkAsRead()
- +GetUnread()
- +GetByUser()
- +Notify()
- +AutoID()

---

## **ContactMessage**
*Attributes: id, first_name, last_name, email, phone, subject, message, timestamps*

- +FillGetData()
- +SubmitContact()
- +MarkAsRead()
- +SearchContactMessage()
- +GetUnread()
- +Reply()
- +AutoID()

---

## **FeedbackMessage**
*Attributes: id, name, email, type, message, is_read, replied_at, timestamps*

- +FillGetData()
- +SubmitFeedback()
- +MarkAsRead()
- +MarkAsReplied()
- +SearchFeedbackMessage()
- +GetUnread()
- +AutoID()

---

## **LowAttendanceAlertState**
*Attributes: id, student_id, last_rate, is_below_threshold, last_alert_sent_at*

- +FillGetData()
- +UpdateState()
- +CheckThreshold()
- +SendAlert()
- +GetByStudent()
- +GetStudentsBelowThreshold()
- +ResetAlert()
- +AutoID()

---

## **Assignment**
*Attributes: id, subject_id, course_id, created_by, title, description, due_date, due_time, max_score, status, allowed_file_types, max_file_size, timestamps*

- +FillGetData()
- +CreateAssignment()
- +UpdateAssignment()
- +DeleteAssignment()
- +PublishAssignment()
- +GetSubmissions()
- +Subject()
- +Course()
- +Creator()
- +Submissions()
- +IsOverdue()
- +CanSubmit()
- +AutoID()

---

## **AssignmentSubmission**
*Attributes: id, assignment_id, student_id, file_path, original_filename, comments, score, feedback, graded_by, graded_at, status, timestamps*

- +FillGetData()
- +Submit()
- +GradeSubmission()
- +UpdateScore()
- +Assignment()
- +Student()
- +Grader()
- +IsGraded()
- +GetPercentage()
- +GetByStudent()
- +GetByAssignment()
- +AutoID()

---

## Summary Table

| Class | Main Functions |
|-------|----------------|
| **User** | Login, Logout, Register, UpdateProfile, ResetPassword |
| **Student** | Add/Update/Delete, Search, GetEnrollments, GetGrades, CalculateGPA |
| **Course** | Add/Update/Delete, Search, GetSubjects, GetEnrollments |
| **Subject** | Add/Update/Delete, Search, GetGrades, GetTimetables |
| **Enrollment** | Request/Approve/Reject, RequestWithdrawal, ApproveWithdrawal |
| **course_teacher** | AssignTeacher, RemoveTeacher |
| **subject_teacher** | AssignTeacher, RemoveTeacher |
| **Grade** | SubmitGrade, ApproveGrade, RejectGrade, GetLetterGrade |
| **GradeReviewLog** | LogAction, GetByGrade |
| **Fee** | Create/Update/Delete, ApprovePayment, RejectPayment, GenerateReceipt |
| **Timetable** | Add/Update/Delete, CheckConflict, GetByCourse/Subject/Student |
| **Attendance** | RecordAttendance, MarkPresent/MarkAbsent, CalculateRate |
| **Announcement** | Create/Update/Delete, Publish, GetVisibleToUser |
| **AnnouncementRead** | MarkAsRead, MarkAsAcknowledged |
| **Message** | SendMessage, MarkAsRead, GetInbox, GetConversation |
| **Notification** | MarkAsRead, GetUnread, Notify |
| **ContactMessage** | SubmitContact, MarkAsRead, Reply |
| **FeedbackMessage** | SubmitFeedback, MarkAsRead, MarkAsReplied |
| **LowAttendanceAlertState** | UpdateState, CheckThreshold, SendAlert |
| **Assignment** | Create/Update/Delete, Publish, GetSubmissions, IsOverdue, CanSubmit |
| **AssignmentSubmission** | Submit, GradeSubmission, IsGraded, GetPercentage |

---

## PlantUML Class Diagram with Functions

Paste this into PlantUML to generate a class diagram with attributes and functions:

```
@startuml
hide circle
skinparam classAttributeIconSize 0

class User {
  +id: int
  +name: string
  +email: string
  +role: enum
  --
  +FillGetData()
  +Login()
  +Logout()
  +Register()
  +UpdateProfile()
  +ResetPassword()
}

class Student {
  +id: int
  +user_id: int
  +student_no: string
  +full_name: string
  +programme: string
  +status: enum
  --
  +FillGetData()
  +AddStudent()
  +UpdateStudent()
  +DeleteStudent()
  +SearchStudent()
  +GetEnrollments()
  +CalculateGPA()
}

class Course {
  +id: int
  +course_code: string
  +title: string
  +credits: int
  +semester: string
  --
  +FillGetData()
  +AddCourse()
  +UpdateCourse()
  +DeleteCourse()
  +SearchCourse()
  +GetSubjects()
}

class Subject {
  +id: int
  +course_id: int
  +subject_code: string
  +title: string
  +credits: int
  --
  +FillGetData()
  +AddSubject()
  +UpdateSubject()
  +DeleteSubject()
  +SearchSubject()
}

class Enrollment {
  +id: int
  +course_id: int
  +student_id: int
  +status: enum
  --
  +FillGetData()
  +RequestEnrollment()
  +ApproveEnrollment()
  +RejectEnrollment()
  +RequestWithdrawal()
}

class Grade {
  +id: int
  +subject_id: int
  +student_id: int
  +score: decimal
  +status: enum
  --
  +FillGetData()
  +SubmitGrade()
  +ApproveGrade()
  +RejectGrade()
  +GetLetterGrade()
}

class Fee {
  +id: int
  +student_id: int
  +amount: decimal
  +status: enum
  +due_date: date
  --
  +FillGetData()
  +CreateFee()
  +ApprovePayment()
  +RejectPayment()
  +GenerateReceipt()
}

class Timetable {
  +id: int
  +subject_id: int
  +day_of_week: string
  +start_time: time
  +end_time: time
  +location: string
  --
  +FillGetData()
  +AddTimetable()
  +UpdateTimetable()
  +CheckConflict()
  note right: course via Subject
}

class Attendance {
  +id: int
  +subject_id: int
  +student_id: int
  +date: date
  +status: enum
  --
  +FillGetData()
  +RecordAttendance()
  +MarkPresent()
  +MarkAbsent()
  +CalculateRate()
  note right: course via Subject
}

class Announcement {
  +id: int
  +user_id: int
  +title: string
  +body: text
  +priority: enum
  --
  +FillGetData()
  +CreateAnnouncement()
  +UpdateAnnouncement()
  +Publish()
}

class AnnouncementRead {
  +id: int
  +announcement_id: int
  +user_id: int
  +read_at: datetime
  --
  +FillGetData()
  +MarkAsRead()
  +MarkAsAcknowledged()
}

class Message {
  +id: int
  +sender_id: int
  +receiver_id: int
  +body: text
  +read: bool
  --
  +FillGetData()
  +SendMessage()
  +MarkAsRead()
  +GetInbox()
}

class ContactMessage {
  +id: int
  +email: string
  +subject: string
  +message: text
  --
  +FillGetData()
  +SubmitContact()
  +MarkAsRead()
}

class FeedbackMessage {
  +id: int
  +email: string
  +type: string
  +message: text
  --
  +FillGetData()
  +SubmitFeedback()
  +MarkAsRead()
}

class LowAttendanceAlertState {
  +id: int
  +student_id: int
  +last_rate: float
  +is_below_threshold: bool
  --
  +FillGetData()
  +UpdateState()
  +CheckThreshold()
  +SendAlert()
}

class Assignment {
  +id: int
  +subject_id: int
  +course_id: int
  +created_by: int
  +title: string
  +description: text
  +due_date: date
  +due_time: time
  +max_score: int
  +status: enum (draft, published, closed)
  +allowed_file_types: json
  +max_file_size: int
  --
  +FillGetData()
  +CreateAssignment()
  +UpdateAssignment()
  +DeleteAssignment()
  +PublishAssignment()
  +GetSubmissions()
  +IsOverdue()
  +CanSubmit()
}

class AssignmentSubmission {
  +id: int
  +assignment_id: int
  +student_id: int
  +file_path: string
  +original_filename: string
  +comments: text
  +score: decimal
  +feedback: text
  +graded_by: int
  +graded_at: datetime
  +status: enum (submitted, graded, returned)
  --
  +FillGetData()
  +Submit()
  +GradeSubmission()
  +IsGraded()
  +GetPercentage()
}

Subject "1" --> "*" Assignment : has many
Course "1" --> "*" Assignment : has many
User "1" --> "*" Assignment : created_by
Assignment "1" --> "*" AssignmentSubmission : has many
Student "1" --> "*" AssignmentSubmission : has many
User "1" --> "*" AssignmentSubmission : graded_by
@enduml
```
