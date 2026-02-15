# Database Attributes vs UI – Audit

This document checks that important database attributes are either **shown in the portal UI** or **used for logic** (e.g. permissions, audit). Attributes that are only in the DB and never used or shown are noted.

---

## Summary

| Entity | Attribute(s) | In UI? | Notes |
|--------|----------------|--------|--------|
| **Timetable** | `created_by` | ✅ Yes | Shown as "Created by" in Admin → Timetable Management (table view). |
| **Assignment** | `created_by` | ✅ Yes | Shown as "Created by" in Teacher → Assignments → [Subject] (per assignment). Also used for edit/delete/publish permission. |
| **Fee** | `processed_by` | ✅ Yes | Shown in Admin → Fees when status is paid ("Processed by" / paid date). |
| **Grade** | `graded_by`, `reviewed_by`, `rejection_reason` | ✅ Yes | Admin → Grades → [Subject]: shows Grader, Reviewer, Rejection reason. |
| **AssignmentSubmission** | `graded_by`, `graded_at` | ✅ Yes | Teacher → Assignments → Submissions: "Graded by … on …". |
| **Announcement** | `user_id` (author) | ✅ Yes | Shown as "By [author]" in Dashboard, Announcements list, Admin Announcements. |
| **User** | `preferences` | ✅ Yes | Settings page: email_announcements, email_messages, email_notifications. |
| **Student** | emergency_contact, previous_institution, id_card, transcript, etc. | ✅ Yes | Staff → Students Create/Edit forms; student self-profile where allowed. |
| **ContactMessage** | `reply`, `is_read`, `replied_at` | ✅ Yes | Admin → Contact Messages: reply, read/unread, replied at. |
| **FeedbackMessage** | `is_read`, `replied_at` | ✅ Yes | Admin → Feedback Messages: read/unread, "Mark as replied", replied at. |

---

## Attributes in DB but not shown (by design)

These are **intentionally** not displayed; they are used for logic, audit, or external systems.

| Entity | Attribute(s) | Why not in UI |
|--------|----------------|----------------|
| **Fee** | `payment_intent_id`, `payment_method` | Used by Stripe; internal only. No need to show in portal. |
| **User** | `password` | Never shown; hidden. |
| **GradeReviewLog** | `performed_by`, `action`, `reason`, `meta` | Audit trail for staff; could be shown in a future "Grade history" view. |
| **LowAttendanceAlertState** | `last_rate`, `is_below_threshold`, `last_alert_sent_at` | Used by job/alert logic; not currently shown in UI (could add to Staff attendance report later). |
| **Notification** | Laravel polymorphic fields | Shown as list in UI; internal structure not exposed. |

---

## Changes made from this audit

1. **Timetable `created_by`** – Already stored; **added** "Created by" column and `creator_name` in Admin Timetable list.
2. **Assignment `created_by`** – Already stored and used for permissions; **added** "Created by" in Teacher Assignments (Show) and passed `creator_name` from backend.

---

## Conclusion

- **User-facing "who did what" fields** (created_by, processed_by, graded_by, reviewed_by, author) are now **shown in the UI** where relevant.
- **Internal/audit-only fields** (e.g. payment_intent_id, grade review logs) remain DB-only by design.
- If you add new columns (e.g. `updated_by`, `approved_by`), add them to the controller payload and to the relevant Vue/Blade view so the portal stays in sync with the database.
