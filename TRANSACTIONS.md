I checked the whole codebase (web.php, auth.php, and related controllers/services).

Yes, like library has Purchase/Borrow/Return, your top-level core transactions are:

Enrollment (request, approve, reject)
Withdrawal (request, approve, reject)
Fee Payment (manual confirmation flow + Stripe flow)
For the full program, transactions group into these:

Account registration/login/logout/password/email-verification flows
Profile update/delete
Settings update
Student self-profile update/remove photo
Student management (create/update/delete/remove photo)
User management (create/update/delete/remove photo)
Course management (create/update/delete/remove photo)
Subject management (create/update/delete/remove photo)
Teacher assignment to courses/subjects
Enrollment workflow (request/approve/reject)
Withdrawal workflow (request/approve/reject)
Fee management (create/update/delete)
Payment confirmation workflow (submit/approve/reject)
Stripe payment workflow (checkout/success/cancel/webhook)
Grade workflow (teacher submit, staff approve/reject)
Assignment workflow (create/update/publish/delete)
Submission workflow (student submit/resubmit, teacher grade)
Attendance recording
Timetable management (create/update/delete)
Announcement workflow (create/update/delete/read/acknowledge)
Messaging workflow (send, mark read)
Contact/feedback processing (submit, mark read, reply/mark replied)
Notification workflow (mark one/all as read)
Admin ops transactions (run attendance alerts, retry/flush/delete failed jobs)
Technical count from routes: 84 mutation endpoints (POST/PUT/PATCH/DELETE plus payment.success and payment.cancel GET flows that also update state, and local dev.verify-email-now).

🏆 Final Answer

Timetable is a management transaction, not a core academic transaction.

Your strongest core set remains:

Enrollment

Withdrawal

Fee Payment

Assignment Submission

Grade Processing

Attendance Recording
