# 5.3.6 Functional Testing
## Timebox 3 – Manage Timetable, Attendance & Communication Process

### 5.3.6.1 Scope

Functional testing for Timebox 3 was conducted to verify that the system correctly implements **timetable management**, **attendance recording and reporting**, and **communication features** (announcements, messages, contact/feedback, and notifications). The tests focus on:

- Timetable CRUD by staff, conflict prevention, and role-based timetable viewing (student/teacher/staff).
- Attendance recording by teachers, role-based attendance viewing and reporting, and low attendance alert workflow.
- Announcement lifecycle (create/update/delete), audience visibility rules, and read/acknowledgement tracking.
- Messaging workflow (compose, inbox/sent, mark as read).
- Guest contact and feedback submission, staff inbox management, and read/reply tracking.
- Notifications view and read management for all roles.

All tests are numbered and mapped to modules to ensure coverage of the Timebox 3 scope.

---

### 5.3.6.2 Coverage Summary Table

| Module ID | Module Name                               | TC Range     | Total Test Cases |
|----------:|-------------------------------------------|--------------|------------------|
| M1        | Timetable Management                       | TC1.1–TC1.8  | 8  |
| M2        | Attendance Management & Reports            | TC2.1–TC2.10 | 10 |
| M3        | Announcements (Read/Ack/Audience)          | TC3.1–TC3.10 | 10 |
| M4        | Messaging (Inbox/Sent/Read)                | TC4.1–TC4.6  | 6  |
| M5        | Contact & Feedback (Guest + Staff Inbox)   | TC5.1–TC5.8  | 8  |
| M6        | Notifications (View/Read/Read All)         | TC6.1–TC6.4  | 4  |

**Total Functional Test Cases (Timebox 3): 46**

---

### 5.3.6.3 Test Design Technique

Test cases for Timebox 3 were designed using:

- **Equivalence partitioning** and **boundary value analysis** for time ranges, date filters, and status enums.
- **Negative testing** to verify conflict detection, invalid inputs, and unauthorized actions.
- **Role-based access testing** across Guest, Student, Teacher, and Staff roles.
- **Workflow testing** for end-to-end processes (timetable update → notify; attendance record → notify → report; announcement publish → read/ack; message send → inbox → read).

---

### 5.3.6.4 Detailed Test Plan

#### Module M1 – Timetable Management

| TC ID | Scenario                                      | Preconditions                                                                 | Input Data                                                                 | Test Steps                                                                                                                                                 | Expected Result                                                                 |
|------:|-----------------------------------------------|-------------------------------------------------------------------------------|----------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------|
| TC1.1 | Register timetable with valid data            | Staff logged in; subject exists.                                             | subject_id, day_of_week, start_time, end_time, location                    | 1) Open Timetable Create page.<br>2) Enter valid values.<br>3) Save.                                                                                      | Timetable created; visible in staff list; notifications sent to enrolled students and assigned teachers. |
| TC1.2 | Validation – end time before start time       | Staff logged in; subject exists.                                             | start_time=10:00, end_time=09:00                                           | 1) Create timetable with end before start.<br>2) Save.                                                                                                    | Validation error shown; timetable not saved. |
| TC1.3 | Detect schedule conflict (overlap)            | Staff logged in; existing timetable entry for same course/day overlaps.      | new entry overlapping existing time range                                  | 1) Create second timetable overlapping same course/day.<br>2) Save.                                                                                        | Conflict detected; timetable not saved; message explains overlap. |
| TC1.4 | Update timetable (exclude current from conflict) | Staff logged in; timetable exists.                                        | update same record time/location                                            | 1) Edit timetable.<br>2) Update values.<br>3) Save.                                                                                                        | Timetable updated successfully; conflict check excludes current entry; notifications sent. |
| TC1.5 | Delete timetable with confirmation            | Staff logged in; timetable exists.                                           | delete action + confirm                                                     | 1) Click delete on timetable entry.<br>2) Confirm deletion.                                                                                                | Timetable deleted; removed from views; (if implemented) notifications sent about update. |
| TC1.6 | View timetable (staff)                        | Staff logged in; multiple timetables exist.                                  | N/A                                                                        | 1) Open Timetable list.<br>2) Navigate pages.                                                                                                              | Paginated list shown; ordered by day_of_week then start_time; includes subject/course details. |
| TC1.7 | View timetable (teacher)                      | Teacher logged in; teacher assigned to one or more subjects.                 | N/A                                                                        | 1) Open Teacher timetable view.<br>2) Inspect weekly grid.                                                                                                 | Only timetables for teacher’s assigned subjects displayed; grouped by course; ordered by day/time. |
| TC1.8 | View timetable (student)                      | Student logged in; student enrolled in one or more courses.                  | N/A                                                                        | 1) Open Student timetable view.<br>2) Inspect weekly grid.                                                                                                 | Only timetables for enrolled courses displayed; grouped by course; ordered by day/time. |

---

#### Module M2 – Attendance Management & Reports

| TC ID | Scenario                                      | Preconditions                                                                 | Input Data                                                                 | Test Steps                                                                                                                                                 | Expected Result                                                                 |
|------:|-----------------------------------------------|-------------------------------------------------------------------------------|----------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------|
| TC2.1 | Record attendance (valid)                      | Teacher logged in; teacher assigned to subject; students enrolled.           | subject_id, date, attendance array (student_id + status)                   | 1) Open Record Attendance page.<br>2) Select date.<br>3) Mark present/absent for enrolled students.<br>4) Save.                                           | Attendance records saved/updated (one per subject+student+date); students notified (AttendanceAlert). |
| TC2.2 | Prevent invalid status                          | Teacher logged in; subject assigned.                                         | status = “late” (invalid)                                                  | 1) Attempt to submit invalid status value.<br>2) Save.                                                                                                     | Validation error shown; records not saved. |
| TC2.3 | Prevent recording for unassigned teacher         | Teacher logged in but not assigned to subject.                               | subject_id not owned                                                       | 1) Attempt to record attendance for a subject not assigned.<br>2) Save.                                                                                    | Access denied / validation fails; no attendance saved. |
| TC2.4 | updateOrCreate behaviour (no duplicates)        | Existing attendance exists for same subject+student+date.                    | same keys, different status                                                 | 1) Record attendance for same date again.<br>2) Save.                                                                                                      | Existing record updated; no duplicate rows created. |
| TC2.5 | Teacher view attendance summary per subject      | Teacher assigned to subject; attendance exists.                              | subject_id                                                                 | 1) Open Attendance summary view for subject.<br>2) Review totals/percentages.                                                                              | Shows per-student totals, present count, attendance rate, and total distinct sessions for subject. |
| TC2.6 | Student view attendance statistics               | Student logged in; attendance exists.                                        | N/A                                                                        | 1) Open My Attendance page.<br>2) Review overall, by course, by subject.                                                                                   | Correct totals and rates shown; recent records shown (last 30 days, limited). |
| TC2.7 | Teacher search/filter attendance by date         | Teacher assigned; attendance exists across dates.                            | date filter                                                                | 1) Apply date filter/search for subject attendance.<br>2) View enrolled students list.                                                                    | Filtered results shown for selected date/subject; enrolled students list present. |
| TC2.8 | Staff attendance reporting dashboard             | Staff logged in; attendance exists.                                          | filters (course/subject/date range)                                        | 1) Open Attendance Report page.<br>2) Apply filters.                                                                                                       | Overall stats and breakdowns by course/subject shown; recent records listed with pagination if applicable. |
| TC2.9 | Low attendance detection threshold               | Students have varying attendance rates; threshold configured (default 75%). | threshold=75%                                                              | 1) Open staff report low attendance section.<br>2) Review list.                                                                                             | Students below threshold identified; top low-attendance list displayed (e.g. top 20). |
| TC2.10 | Low attendance alerts job (cooldown)            | Alert job available; alert state exists for student; cooldown configured.    | dispatch job                                                               | 1) Dispatch low attendance alert job.<br>2) Verify alerts sent only when eligible.                                                                         | Alerts sent to newly below-threshold students or after cooldown; `LowAttendanceAlertState` updated; notifications stored. |

---

#### Module M3 – Announcements (Read/Ack/Audience)

| TC ID | Scenario                                      | Preconditions                                                                 | Input Data                                                                 | Test Steps                                                                                                                                                 | Expected Result                                                                 |
|------:|-----------------------------------------------|-------------------------------------------------------------------------------|----------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------|
| TC3.1 | Staff registers announcement (valid)           | Staff logged in.                                                             | title, body, priority, pinned, require_ack, audience, publish_at/expires_at | 1) Create announcement.<br>2) Save.                                                                                                                        | Announcement created with author; visible to selected audience when currently visible; ordered by pinned/priority/created_at. |
| TC3.2 | Teacher registers announcement (default audience students) | Teacher logged in.                                                   | title, body, priority, require_ack                                         | 1) Teacher creates announcement.<br>2) Save.                                                                                                                | Announcement created by teacher; default audience = students only (as designed). |
| TC3.3 | Validate expires_at after publish_at           | Staff logged in.                                                             | publish_at after expires_at                                                | 1) Create announcement with invalid dates.<br>2) Save.                                                                                                      | Validation error shown; announcement not saved. |
| TC3.4 | Visibility – currentlyVisible filter           | Announcements exist with different publish/expiry times.                      | N/A                                                                        | 1) View announcements as student/teacher.<br>2) Check visible list.                                                                                         | Only published and not expired announcements displayed. |
| TC3.5 | Visibility – role-based audience               | Announcement audience targets specific roles.                                | audience = staff only                                                      | 1) View as student/teacher.<br>2) View as staff.                                                                                                            | Targeted announcement visible only to intended roles. |
| TC3.6 | Mark announcement as read                      | User logged in; visible announcement exists.                                 | announcement_id                                                            | 1) Open announcement.<br>2) Mark as read.                                                                                                                   | `AnnouncementRead` created/updated; `read_at` set. |
| TC3.7 | Acknowledge announcement (require_ack=true)    | Announcement requires acknowledgement.                                       | announcement_id                                                            | 1) Click Acknowledge.<br>2) Confirm.                                                                                                                        | `acknowledged_at` set; UI shows acknowledged state. |
| TC3.8 | Prevent ack when require_ack=false             | Announcement does not require acknowledgement.                               | announcement_id                                                            | 1) Attempt to acknowledge.                                                                                                                                  | Action hidden or rejected; no acknowledged_at set. |
| TC3.9 | Teacher ownership restriction on update/delete | Teacher logged in; announcement created by another teacher.                   | update/delete attempt                                                      | 1) Try edit/delete other teacher’s announcement.                                                                                                            | Access denied (403) / action blocked; record not changed. |
| TC3.10 | Staff search announcements                     | Staff logged in; announcements exist.                                        | search/filter                                                              | 1) Use staff announcement search/list page.                                                                                                                 | Staff can view all announcements, ordered by pinned/priority/created_at, with author info. |

---

#### Module M4 – Messaging (Inbox/Sent/Read)

| TC ID | Scenario                                      | Preconditions                                                                 | Input Data                                                                 | Test Steps                                                                                                                                                 | Expected Result                                                                 |
|------:|-----------------------------------------------|-------------------------------------------------------------------------------|----------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------|
| TC4.1 | Compose message lists users excluding self     | Any authenticated user logged in.                                            | N/A                                                                        | 1) Open compose message page.<br>2) Inspect recipient list.                                                                                                 | Recipient list excludes current user; ordered by role then name (as designed). |
| TC4.2 | Send message (valid)                           | Sender logged in; receiver exists.                                           | receiver_id, body                                                         | 1) Compose and send message.                                                                                                                                | Message saved with sender/receiver roles; read=false; appears in inbox/sent. |
| TC4.3 | Prevent sending to self                         | Sender logged in.                                                           | receiver_id = self                                                        | 1) Attempt to send to self.                                                                                                                                | Validation error shown; message not saved. |
| TC4.4 | View inbox and sent                             | Messages exist for user.                                                     | N/A                                                                        | 1) Open messages list.                                                                                                                                    | Shows sent and received messages ordered by created_at desc, with read status. |
| TC4.5 | Mark message as read (receiver only)            | Message exists where current user is receiver.                               | message_id                                                                | 1) Open message.<br>2) Mark as read.                                                                                                                       | read=true updated; unread badge removed. |
| TC4.6 | Prevent marking read by non-receiver            | Message exists but current user is not receiver.                             | message_id                                                                | 1) Attempt mark as read.                                                                                                                                   | Action rejected; no update. |

---

#### Module M5 – Contact & Feedback (Guest + Staff Inbox)

| TC ID | Scenario                                      | Preconditions                                                                 | Input Data                                                                 | Test Steps                                                                                                                                                 | Expected Result                                                                 |
|------:|-----------------------------------------------|-------------------------------------------------------------------------------|----------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------|
| TC5.1 | Guest submits contact message (valid)          | Guest page accessible.                                                       | first_name, last_name, email, subject, message                             | 1) Fill contact form.<br>2) Submit.                                                                                                                        | Message saved; success confirmation shown. |
| TC5.2 | Contact message validation errors              | Guest page accessible.                                                       | missing required fields                                                    | 1) Submit with missing fields.                                                                                                                             | Validation messages shown; record not saved. |
| TC5.3 | Staff views contact inbox with pagination      | Staff logged in; contact messages exist.                                     | N/A                                                                        | 1) Open contact inbox page.                                                                                                                                | Paginated list shown (e.g. 20 per page), with unread count. |
| TC5.4 | Staff search contact inbox                     | Staff logged in.                                                             | search keyword                                                             | 1) Search by name/email/subject/message.                                                                                                                   | Matching results displayed. |
| TC5.5 | Staff marks contact message as read / reply    | Staff logged in; contact message exists.                                     | mark read; reply text                                                      | 1) Open message.<br>2) Mark read and/or submit reply.                                                                                                      | is_read updated; reply stored; replied_at set. |
| TC5.6 | Guest submits feedback message (valid)         | Guest page accessible.                                                       | name, email, type, message                                                 | 1) Fill feedback form.<br>2) Submit.                                                                                                                       | Feedback saved; success shown. |
| TC5.7 | Staff views/search feedback inbox              | Staff logged in; feedback messages exist.                                    | search keyword                                                             | 1) Open feedback inbox.<br>2) Search/filter.                                                                                                               | Paginated list shown (e.g. 20 per page), unread count available; search works. |
| TC5.8 | Staff marks feedback as read                   | Staff logged in; feedback exists.                                            | mark read action                                                           | 1) Open feedback entry.<br>2) Mark as read.                                                                                                                | is_read updated; replied_at updated if applicable. |

---

#### Module M6 – Notifications (View/Read/Read All)

| TC ID | Scenario                                      | Preconditions                                                                 | Input Data                                                                 | Test Steps                                                                                                                                                 | Expected Result                                                                 |
|------:|-----------------------------------------------|-------------------------------------------------------------------------------|----------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------|
| TC6.1 | View notifications list                        | User logged in; notifications exist.                                         | N/A                                                                        | 1) Open Notifications page.                                                                                                                                | Latest notifications shown (limit e.g. 50), ordered by created_at desc; unread highlighted. |
| TC6.2 | Mark single notification as read               | User logged in; unread notification exists.                                  | notification_id                                                            | 1) Mark one notification as read.                                                                                                                          | read_at set for that notification; unread count decreases. |
| TC6.3 | Mark all notifications as read                 | User logged in; multiple unread notifications exist.                         | N/A                                                                        | 1) Click “Mark all as read”.                                                                                                                               | All unread notifications updated with read_at; unread count becomes 0. |
| TC6.4 | Notification triggered by timetable/attendance | Timetable updated or attendance recorded.                                    | N/A                                                                        | 1) Perform timetable update or attendance record action.<br>2) Open notifications.                                                                        | Corresponding notifications appear (e.g. TimetableUpdated, AttendanceAlert, LowAttendanceAlert when applicable). |

---

### 5.3.6.5 Test Execution Log

The table below summarises Timebox 3 test execution. Evidence screenshots and outputs should be referenced as Fig C01, Fig C02, … and stored separately.

| TC ID | Input Data (summary)                                  | Status | Actual Result (summary)                                                                 | Evidence | Remarks             |
|------:|--------------------------------------------------------|:------:|------------------------------------------------------------------------------------------|----------|---------------------|
| TC1.1 | Create timetable with valid values                     | Pass   | Timetable saved; notifications generated for relevant users.                             | Fig C01  | Working as expected |
| TC1.2 | End time before start time                             | Pass   | Validation blocked save.                                                                  | Fig C02  | Working as expected |
| TC1.3 | Overlapping timetable entry                            | Pass   | Conflict detected; save blocked.                                                          | Fig C03  | Working as expected |
| TC1.4 | Update timetable                                       | Pass   | Updated successfully; notifications sent.                                                  | Fig C04  | Working as expected |
| TC1.5 | Delete timetable                                       | Pass   | Deleted successfully; removed from views.                                                  | Fig C05  | Working as expected |
| TC1.6 | Staff timetable list                                   | Pass   | Ordered and paginated list shown.                                                         | Fig C06  | Working as expected |
| TC1.7 | Teacher timetable view                                 | Pass   | Only assigned subjects shown in week grid.                                                 | Fig C07  | Working as expected |
| TC1.8 | Student timetable view                                 | Pass   | Only enrolled courses shown in week grid.                                                  | Fig C08  | Working as expected |
| TC2.1 | Record attendance                                      | Pass   | Records saved/updated; AttendanceAlert notifications created.                             | Fig C09  | Working as expected |
| TC2.2 | Invalid attendance status                              | Pass   | Validation blocked save.                                                                   | Fig C10  | Working as expected |
| TC2.3 | Unassigned teacher attempt                             | Pass   | Access denied / blocked.                                                                   | Fig C11  | Working as expected |
| TC2.4 | updateOrCreate no duplicates                           | Pass   | Existing rows updated; no duplicates.                                                      | Fig C12  | Working as expected |
| TC2.5 | Teacher attendance summary                              | Pass   | Totals and rates shown correctly.                                                          | Fig C13  | Working as expected |
| TC2.6 | Student attendance stats                               | Pass   | Overall and breakdown stats displayed; recent records listed.                              | Fig C14  | Working as expected |
| TC2.7 | Teacher date filter/search                             | Pass   | Correct filtered results displayed.                                                        | Fig C15  | Working as expected |
| TC2.8 | Staff attendance report                                | Pass   | Report shows breakdowns and low attendance list.                                            | Fig C16  | Working as expected |
| TC2.9 | Low attendance threshold detection                     | Pass   | Below-threshold students identified correctly.                                              | Fig C17  | Working as expected |
| TC2.10 | Low attendance alert job                               | Pass   | Alerts sent according to threshold/cooldown; alert state updated.                          | Fig C18  | Working as expected |
| TC3.1 | Staff announcement create                              | Pass   | Announcement visible to intended audience when visible.                                    | Fig C19  | Working as expected |
| TC3.2 | Teacher announcement create                            | Pass   | Announcement created with expected audience rules.                                          | Fig C20  | Working as expected |
| TC3.3 | Invalid publish/expiry dates                           | Pass   | Validation blocked save.                                                                   | Fig C21  | Working as expected |
| TC3.4 | currentlyVisible filter                                | Pass   | Only visible announcements shown.                                                          | Fig C22  | Working as expected |
| TC3.5 | role-based audience                                   | Pass   | Targeted announcements hidden for non-target roles.                                        | Fig C23  | Working as expected |
| TC3.6 | mark as read                                           | Pass   | AnnouncementRead created; read_at set.                                                     | Fig C24  | Working as expected |
| TC3.7 | acknowledge announcement                                | Pass   | acknowledged_at set; UI updated.                                                            | Fig C25  | Working as expected |
| TC3.8 | prevent ack when not required                          | Pass   | Ack blocked/hidden; no acknowledged_at set.                                                | Fig C26  | Working as expected |
| TC3.9 | teacher ownership restrictions                         | Pass   | 403/blocked on edit/delete other’s announcement.                                           | Fig C27  | Working as expected |
| TC3.10 | staff announcement search/list                         | Pass   | Staff sees all announcements with ordering and author info.                                | Fig C28  | Working as expected |
| TC4.1 | compose recipient list                                 | Pass   | Self excluded; ordered list.                                                               | Fig C29  | Working as expected |
| TC4.2 | send message                                           | Pass   | Message saved; receiver sees unread.                                                       | Fig C30  | Working as expected |
| TC4.3 | prevent send to self                                   | Pass   | Validation blocked send.                                                                   | Fig C31  | Working as expected |
| TC4.4 | view inbox/sent                                        | Pass   | Lists shown ordered by created_at desc with read status.                                   | Fig C32  | Working as expected |
| TC4.5 | mark message read                                      | Pass   | read=true updated for receiver.                                                            | Fig C33  | Working as expected |
| TC4.6 | prevent mark read by non-receiver                      | Pass   | Action blocked; no update.                                                                 | Fig C34  | Working as expected |
| TC5.1 | guest contact submit                                   | Pass   | Saved successfully; confirmation shown.                                                    | Fig C35  | Working as expected |
| TC5.2 | contact validation errors                              | Pass   | Validation shown; not saved.                                                               | Fig C36  | Working as expected |
| TC5.3 | staff contact inbox                                    | Pass   | Paginated list + unread count shown.                                                       | Fig C37  | Working as expected |
| TC5.4 | staff contact search                                   | Pass   | Matching results displayed.                                                                | Fig C38  | Working as expected |
| TC5.5 | staff mark contact read/reply                          | Pass   | is_read/reply/replied_at updated.                                                          | Fig C39  | Working as expected |
| TC5.6 | guest feedback submit                                  | Pass   | Saved successfully; confirmation shown.                                                    | Fig C40  | Working as expected |
| TC5.7 | staff feedback inbox/search                            | Pass   | Paginated list + unread count; search works.                                               | Fig C41  | Working as expected |
| TC5.8 | staff mark feedback read                               | Pass   | is_read updated.                                                                           | Fig C42  | Working as expected |
| TC6.1 | view notifications                                     | Pass   | Latest notifications shown; unread highlighted.                                            | Fig C43  | Working as expected |
| TC6.2 | mark single notification read                           | Pass   | read_at set; unread count decreased.                                                       | Fig C44  | Working as expected |
| TC6.3 | mark all notifications read                             | Pass   | All unread marked read.                                                                    | Fig C45  | Working as expected |
| TC6.4 | timetable/attendance notification created              | Pass   | Notifications created after timetable update/attendance record.                             | Fig C46  | Working as expected |

---

### 5.3.6.6 Result Summary

| Module                                    | Passed | Failed | Remarks                       |
|-------------------------------------------|:------:|:------:|-------------------------------|
| Timetable Management                       |   8    |   0    | All planned test cases passed |
| Attendance Management & Reports            |   10   |   0    | All planned test cases passed |
| Announcements (Read/Ack/Audience)          |   10   |   0    | All planned test cases passed |
| Messaging (Inbox/Sent/Read)                |   6    |   0    | All planned test cases passed |
| Contact & Feedback (Guest + Staff Inbox)   |   8    |   0    | All planned test cases passed |
| Notifications (View/Read/Read All)         |   4    |   0    | All planned test cases passed |

All 46 functional test cases for Timebox 3 passed. No critical defects were identified. Any minor issues discovered during testing were corrected and re-tested before finalising this report.

---

## Appendix C – Test Evidence (Screenshot Mapping)

Screenshots and output evidence for Timebox 3 tests are stored and referenced by figure IDs (Fig C01, Fig C02, …). Each figure is annotated with the related test case number.

| TC Range      | Evidence Range   |
|--------------|------------------|
| TC1.1–TC1.8  | Fig C01–Fig C08  |
| TC2.1–TC2.10 | Fig C09–Fig C18  |
| TC3.1–TC3.10 | Fig C19–Fig C28  |
| TC4.1–TC4.6  | Fig C29–Fig C34  |
| TC5.1–TC5.8  | Fig C35–Fig C42  |
| TC6.1–TC6.4  | Fig C43–Fig C46  |

