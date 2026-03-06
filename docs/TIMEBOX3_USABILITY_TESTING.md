# 5.3.7 Usability Testing
## Timebox 3: Manage Timetable, Attendance & Communication Process

Usability testing for Timebox 3 evaluated how well **students**, **teachers**, and **staff** can use the timetable, attendance, and communication features. The evaluation followed **Nielsen’s 10 Usability Heuristics**; selected heuristics and examples are described below.

---

## Visibility of System Status

**Explain:** The system clearly shows timetable, attendance, and communication status.

- The **Timetable** pages for students and teachers use a **week grid** view with colour-coded sessions, making it obvious which classes occur on each day and time.  
- The **Attendance** summary shows total sessions, present/absent counts, and attendance percentages so students and teachers can immediately see current status.  
- **Announcements** and **Messages** show read/unread indicators, and notifications display badges/counts until they are marked as read.  
- **Low Attendance Alerts** and other notifications (e.g. TimetableUpdated) appear in the notification panel, confirming that key background processes have run.

*[Insert screenshot: Student Timetable week grid with colour-coded sessions]*  

---

## Match between System and Real World

**Explain:** Labels and workflows use familiar academic and communication language.

- Timetable screens use terms like **Day of Week**, **Start Time**, **End Time**, and **Location**, aligning with typical course schedules.  
- Attendance uses **Present** and **Absent** rather than technical codes, and attendance thresholds (e.g. 75%) are expressed as percentages.  
- Communication features use **Announcements**, **Messages**, **Contact**, and **Feedback**, which are familiar terms for portal and email users.

*[Insert screenshot: Record Attendance page with Present/Absent options]*  

---

## User Control and Freedom

**Explain:** Users can navigate between communication features and undo/exit actions.

- Navigation menus and breadcrumb patterns from earlier timeboxes are reused for **Timetable**, **Attendance**, and **Announcements**, so users can quickly move between sections.  
- Teachers recording attendance can **review the list** before saving and can update attendance for a given date if a mistake is found.  
- Users can **dismiss or mark notifications as read** and **acknowledge announcements**, giving them control over what remains highlighted.

*[Insert screenshot: Teacher Attendance page with Back/Save controls]*  

---

## Consistency and Standards

**Explain:** Screen layouts and patterns are consistent across Timebox 3 features.

- Tables for **Attendance reports**, **Announcements**, **Contact Messages**, and **Feedback Messages** share common layouts: table headers, search bar, pagination, and unread highlighting.  
- Status indicators (e.g. unread, acknowledged, low attendance) use consistent colours and badge styles.  
- Messaging and notification icons and entry points are placed consistently in the header/navigation, matching Timebox 1 and 2 conventions.

*[Insert screenshot: Staff Attendance Report and Announcements lists showing consistent table layout]*  

---

## Error Prevention

**Explain:** The system helps users avoid timetable, attendance, and messaging mistakes.

- The Timetable form prevents invalid times (end time before start time) and detects **schedule conflicts** before saving.  
- Attendance entry ensures that only **enrolled students** can be marked and that status values are limited to **present/absent**.  
- Contact and feedback forms validate required fields and email formats, preventing incomplete or unusable submissions.  
- Messaging prevents sending messages to oneself and validates that the receiver exists.

*[Insert screenshot: Error message for timetable conflict or invalid attendance input]*  

---

## Recognition rather than Recall

**Explain:** Options are visible so users don’t need to remember codes or procedures.

- Timetable views show all classes in a grid, so students do not need to remember subject times or locations.  
- Attendance reports and low-attendance lists visually highlight at-risk students, avoiding manual calculations.  
- Announcement and notification lists clearly show **what** happened (title, short summary), so users do not have to recall system events from memory.

*[Insert screenshot: Low attendance list with highlighted rows]*  

---

## Flexibility and Efficiency of Use

**Explain:** Frequent users (teachers and staff) can work efficiently while preserving clarity for new users.

- Teachers can quickly record attendance for a session using a **single page** with bulk “mark all present” behaviour (if enabled), and then adjust individuals.  
- Staff can filter attendance reports by **course, subject, date** and quickly identify problem areas.  
- Staff contact/feedback inboxes support search, filters, and pagination, making it efficient to handle many messages.

*[Insert screenshot: Staff Attendance Report with filters and summary cards]*  

---

## Aesthetic and Minimalist Design

**Explain:** Timebox 3 screens present necessary information clearly without clutter.

- Timetable views show subject code, title, and location in each cell, without extra technical fields.  
- Attendance reports separate **high-level statistics** (cards) from **detailed tables**, avoiding overwhelming the user.  
- Announcements show key metadata (priority, pinned, require_ack) using small badges instead of long text blocks.

*[Insert screenshot: Announcements list with badges and clean layout]*  

---

## Help Users Recognise, Diagnose, and Recover from Errors

**Explain:** Error messages are understandable and indicate how to fix issues.

- When a timetable conflict occurs, the error states which day/time and course conflict, helping staff adjust the timeslot.  
- Attendance errors (e.g. missing statuses) clearly highlight problematic rows or fields.  
- Contact and feedback forms show field-level validation messages so guests know what to correct.  
- Notification actions (e.g. “mark all as read”) include confirmation messages to reassure users that the action succeeded.

*[Insert screenshot: Example of timetable conflict error message]*  

---

## Help and Documentation

**Explain:** On-screen labels and hints help users understand Timebox 3 features without separate manuals.

- Timetable forms include labels and placeholders for day/time formats and location names.  
- Attendance pages include short headings or helper text explaining how totals and percentages are calculated.  
- Announcement pages describe when acknowledgements are required and how they are used for compliance.  
- Contact and feedback pages have short descriptions explaining who receives the messages and expected response behaviour.

*[Insert screenshot: Timetable or Attendance form with explanatory labels]*  

---

# 5.3.9 Iteration for Usability Testing

Iterations performed after usability testing for Timebox 3, aligned with the identified usability issues.

---

## Iteration 1: Timetable Readability (Visibility of System Status)

**Users said** that the timetable list view made it hard to understand weekly schedules at a glance.  
**Change:** The timetable was redesigned into a **week-grid layout** with clear time slots, colours per course/subject, and hover details for each session.

**(Iteration 1) Iteration for Timetable Page**

| Before | After |
|--------|-------|
| *[Placeholder: Timetable as plain list]* | *[Placeholder: Timetable week grid with colour-coded sessions]* |

---

## Iteration 2: Faster Attendance Entry (Flexibility & Efficiency)

**Users said** that marking attendance one student at a time was slow and error‑prone.  
**Change:** The Record Attendance page was updated with a **searchable student list**, improved present/absent toggle controls, and bulk helpers (e.g. “mark all present” then adjust absences).

**(Iteration 2) Iteration for Record Attendance Page**

| Before | After |
|--------|-------|
| *[Placeholder: Simple list with individual checkboxes]* | *[Placeholder: Enhanced list with bulk actions and search]* |

---

## Iteration 3: Announcement Acknowledgement Clarity (Match & Visibility)

**Users said** that they did not notice which announcements required acknowledgement.  
**Change:** Announcements with `require_ack = true` were visually highlighted with an **“Action required” badge** and a clear **Acknowledge** button in the detail view.

**(Iteration 3) Iteration for Announcement Detail Page**

| Before | After |
|--------|-------|
| *[Placeholder: Announcement without special ack indicator]* | *[Placeholder: Announcement with “Requires acknowledgement” badge and button]* |

---

## Iteration 4: Contact & Feedback Inbox Usability (User Control and Freedom)

**Users said** that it was difficult for staff to manage many contact and feedback messages.  
**Change:** The staff inbox pages were enhanced with **search**, filters (e.g. unread/answered), clearer unread counts, and better pagination indicators.

**(Iteration 4) Iteration for Contact & Feedback Inbox Pages**

| Before | After |
|--------|-------|
| *[Placeholder: Flat list without filters or unread indicator]* | *[Placeholder: Inbox with filters, unread badges, and improved pagination]* |

---

## Iteration 5: Notifications Overload (Help Users Recognise & Recover)

**Users said** that notifications built up and were hard to clear or distinguish.  
**Change:** A **“Mark all as read”** action was added, unread items gained stronger visual emphasis, and grouping/sorting were improved so important notifications appear first.

**(Iteration 5) Iteration for Notifications Page**

| Before | After |
|--------|-------|
| *[Placeholder: Long notification list with weak unread highlighting]* | *[Placeholder: Notification list with strong unread highlight and Mark All as Read]* |

