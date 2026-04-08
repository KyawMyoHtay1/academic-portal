# University Academic Portal Program Demonstration Video Recording Script

## Recording Setup

- Prepare four browser tabs before recording:
  - guest / public site
  - staff account
  - teacher account
  - student account
- Use seeded demo accounts:
  - Staff: `alice.staff@example.com`
  - Teacher: `amelia.teacher@example.com`
  - Student: `student01@example.com`
  - Password: `Password123!`
- If Stripe, email delivery, or queue processing are not configured locally, point at the related workflow and explain it instead of risking a live failure.
- If a page contains long tables, use the first visible seeded record rather than scrolling too much.

# Recording Flow

## Segment 1: Opening and Home Page

Time: `0:00-1:00`

Say:

- Open `/`
- Pause briefly on the home page

"Hello, my name is Kyaw Myo Htay, and in this screencast I am going to demonstrate my final year project, the University Academic Portal."

"This system is developed using Laravel, Vue.js, Inertia, Tailwind CSS, and MySQL. The main purpose of the project is to replace manual and spreadsheet-based academic administration with one integrated web-based platform."

"The portal supports the major academic processes including student registration, course enrollment, grades, fee management, timetable access, attendance tracking, and communication for students, teachers, staff, and public users."

## Segment 2: Public Pages and Portal Entry

Time: `1:00-2:00`

Say:

- Use the public navigation to open `Courses`
- Open `News`
- Open `About`
- Open `Contact`
- Open `Feedback`
- Open `User Manual`

"I will begin with the public-facing side of the system. Public visitors can browse university information, view courses, read announcements, send contact or feedback messages, and access the user manual."

"This is important because the portal is not only a back-office system. It also gives the university a proper public entry point, improves accessibility, and provides a more professional and organised experience."

"The guest pages also connect naturally to the authenticated parts of the portal, so users can move from public information into the academic workflows more smoothly."

## Segment 3: Login and Role-Based Access

Time: `2:00-3:00`

Say:

- Open the login page if needed
- Briefly show the prepared `Staff`, `Teacher`, and `Student` tabs
- Switch to the staff tab for the next section

"Before entering the main modules, the system also supports secure login, password reset, email verification, user settings, and role-based access control."

"To save time during this recording, I have already prepared separate tabs for the main roles: staff, teacher, and student."

"The key point is that each role is redirected to the correct dashboard and only sees the functions that belong to that role. That keeps the system easier to use and also reinforces security."

## Segment 4: Staff Dashboard and User Management

Time: `3:00-4:20`

Say:

- In the staff tab, open `Dashboard`
- Click `People > Manage Users`
- Point at `Add User`
- Point at the role tabs: `All`, `Students`, `Teachers`, `Staff`
- Point at the search box and sorting controls

"I am now on the staff side. The staff dashboard gives an overview of the main academic administration modules and acts as the operational centre of the portal."

"From `Manage Users`, staff can create and manage system accounts for students, teachers, and staff members. The page also supports search, filtering, sorting, and role separation."

"This improves on the manual system because user records are no longer scattered across paper files or spreadsheets. Instead, they are controlled in one place with clearer validation and better visibility."

## Segment 5: Student Records, Courses, Subjects, and Teacher Assignment

Time: `4:20-6:20`

Say:

- Click `People > Student Records`
- On the first visible record, click `Quick view`
- Point at the student details
- Click `Done`
- Click `Academics > Manage Courses`
- On the first visible course, click `Quick view`
- Point at the course details
- Click `Done`
- Click `Academics > Manage Subjects`
- Point at `Create Subject`
- On the first visible subject, point at or open `Assign Teachers`

"These pages represent the main Timebox 1 foundation of the system. Staff can manage student records, course records, and subject records through structured digital interfaces."

"In `Student Records`, staff can register and maintain student academic data with filtering and quick-view support. In `Manage Courses`, staff can control course information such as course code, credits, semester, and enrollment visibility."

"In `Manage Subjects`, staff can create subjects and assign teachers. This is important because the later workflows such as grades, attendance, and timetable management depend on correct subject and teacher relationships."

"Together, these modules replace manual registration work with more consistent and validated records."

## Segment 6: Student Course Browsing and Enrollment

Time: `6:20-7:35`

Say:

- Switch to the student tab
- Click `Academics > Courses`
- Optionally use the search or filter controls
- On one course card, click `Quick view`
- Point at the course information
- Click `Enroll to course` or `Enroll` if visible
- Click `Academics > My Courses`

"Now I am showing the student perspective. Students can browse the course catalog, search for courses, review the details, and then submit an enrollment request through the portal."

"This is much more efficient than paper-based course registration, because the student can see available courses directly and the request is recorded immediately in the system."

"On `My Courses`, the student can also see their own enrollment status, so there is no need to ask staff manually for progress updates."

## Segment 7: Staff Enrollment Approval Workflow

Time: `7:35-8:20`

Say:

- Switch back to the staff tab
- Click `Academics > Enrollment Requests`
- If needed, click the `Pending` tab
- On one request, click `Details`
- Point at `Approve Enrollment` and `Reject Enrollment`

"Once a student submits an enrollment request, the staff side handles the approval workflow. Requests appear in the enrollment management area with their current status."

"Staff can review the request details and then approve or reject the enrollment. This creates a more controlled and traceable process than the manual system."

"The workflow also supports better validation, status tracking, and clearer communication between students and staff."

## Segment 8: Teacher Assignments and Grade Entry

Time: `8:20-9:50`

Say:

- Switch to the teacher tab
- Click `Teaching > Assignments`
- Open the first visible subject card
- Return to the teacher menu if needed
- Click `Teaching > Grades`
- Open the first visible subject card
- On the grade page, point at `Save Draft Grades`
- If visible, point at the `Submit Final Grade` action

"Next is Timebox 2, which focused on grades and fee payment. During development, assignment management was also added to improve the academic grading workflow."

"Teachers can manage assignments for their own subjects, review student submissions, and then use that information to support grading."

"On the grades page, the teacher can record results and save draft grades first. That supports a realistic workflow because teachers often need to review work before sending the final result for approval."

"When the result is ready, the final grade can be submitted to staff for review."

## Segment 9: Staff Grade Review and Student Result View

Time: `9:50-11:00`

Say:

- Switch to the staff tab
- Click `Academics > Grade Reviews`
- On the subject list, click `Review`
- Point at `Approve`
- Point at `Reject`
- Point at the `Rejection reason (required)` field
- Switch to the student tab
- Click `Academics > Grades`

"The grade workflow is not only about teacher entry. It also includes a formal review stage on the staff side."

"Here, staff can review pending grade submissions, approve them, or reject them with a recorded reason. That improves control, traceability, and accountability."

"After approval, the result becomes visible to the student. On the student page, the system displays approved grades, subject breakdowns, and GPA information."

"So this module replaces delayed manual grade release with a clearer and more structured academic result process."

## Segment 10: Fee Management, Payment Review, and Receipt

Time: `11:00-12:15`

Say:

- Switch to the staff tab
- Click `Finance > Manage Fees`
- Point at `Create Fee`
- Point at `Remind Filtered Overdue`
- Point at `Export CSV`
- Point at `Export PDF`
- On one visible fee row, click `Details`
- If a row shows `payment_pending`, point at `Approve Payment` and `Reject`
- If a row shows `paid`, point at `Receipt`

"The same timebox also includes fee and payment management. Staff can create fee records, review payment status, track overdue payments, export fee data, and generate receipts."

"This is a clear improvement over manual fee tracking because payment information is stored centrally and can be monitored through filters, reminders, and audit-style timelines."

"If a payment is waiting for confirmation, staff can approve or reject it. If it has already been paid, the system can generate a receipt."

## Segment 11: Student Fee View and Stripe Workflow

Time: `12:15-12:50`

Say:

- Switch to the student tab
- Click `Finance > Fees`
- Point at the `Cards` and `Table` view toggle
- Point at `Pay Now`, `Submit Proof`, or `Payment Pending Approval` if visible

"From the student side, fee information is shown more clearly. The student can see fee status, due dates, and the current payment state in one place."

"The portal also supports Stripe checkout and webhook processing for online payment integration. In this recording, I am focusing on the fee workflow and payment state management rather than performing a live payment."

"This gives students better visibility and reduces the need to visit staff just to ask about payment status."

## Segment 12: Timetable Management and Role-Based Timetable Views

Time: `12:50-14:05`

Say:

- Switch to the staff tab
- Click `Academics > Manage Timetable`
- Point at `Create Entry`
- Point at the filters
- Point at `Export PDF`
- Point at `Export CSV`
- Point at `Week view`
- Click `Details` on one row if useful
- Switch to the teacher tab
- Click `Teaching > Timetable`
- Switch to the student tab
- Click `Academics > Timetable`

"Timebox 3 focused on timetable, attendance, and communication. On the staff side, timetable records can be created and managed with filters, exports, and different views."

"This helps avoid conflicts and provides a more organised way to maintain class schedules than using spreadsheets alone."

"The teacher and student roles each receive their own timetable view, so the schedule becomes easier to access and easier to understand for different users."

## Segment 13: Attendance Recording, Student View, and Reports

Time: `14:05-15:25`

Say:

- Switch to the teacher tab
- Click `Teaching > Mark Attendance`
- Open the first visible subject card
- Show the attendance interface briefly
- Switch to the student tab
- Click `Academics > Attendance`
- Switch to the staff tab
- Click `Academics > Attendance Report`

"Attendance is also handled digitally in the portal. Teachers can select a subject and record attendance for enrolled students directly through the system."

"Students can then review their own attendance information through the student area, which improves transparency."

"On the staff side, `Attendance Report` provides a broader view across courses and subjects. This makes it much easier to identify attendance issues than paper-based registers."

## Segment 14: Low-Attendance Monitoring and Alerts

Time: `15:25-16:00`

Say:

- Stay on the staff attendance reporting area
- If low-attendance controls are visible, point at them

"An important enhancement in this module is low-attendance monitoring. The system can identify students who fall below the attendance threshold and support alert workflows."

"These alerts can also work with queue-based processing and email delivery when the environment is configured."

"This makes the attendance process more proactive, because staff can identify risk cases earlier instead of waiting for manual reports."

## Segment 15: Announcements, Messages, and Notifications

Time: `16:00-17:30`

Say:

- Stay in the staff tab
- Click `Communication > Announcements`
- Point at `Create Announcement`
- Point at filters and the `Remind` action
- Switch to the student or teacher tab
- Click `Communication > Announcements`
- Open one announcement
- Point at or click `Acknowledge` if visible
- Click `Communication > Messages`
- Point at the message tabs, search, `Unread only`, `Reply`, and `Send Reply`
- Click `Communication > Notifications`
- Point at `Mark all as read`, `Open`, and `Mark as read`

"Communication is centralised through announcements, direct messages, and notifications."

"Staff can create announcements, set priority, control audience visibility, and send reminders. Students and teachers can then read those announcements and acknowledge important ones when required."

"The portal also includes internal messaging and a notification centre, which helps keep academic communication inside one system instead of spreading it across notice boards, paper messages, or disconnected emails."

## Segment 16: Contact Messages, Feedback, Search, Settings, Failed Jobs, and Closing

Time: `17:30-20:00`

Say:

- Switch to the staff tab
- Click `Communication > Contact Messages`
- Point at filters, `Add note` or `Edit note`, and `Mark read`
- Click `Communication > Feedback Messages`
- Point at filters, `Mark read`, and `Mark as handled`
- Use the global search field in the header
- Click `Settings`
- Click `Academics > Failed Jobs`
- Return to `Dashboard` for the closing summary if needed

"The portal also supports public communication through the guest contact and feedback forms. Staff can review those submissions, add internal notes, mark items as read, and mark feedback as handled."

"In addition to the main workflows, the system also includes global search, user settings, notification preferences, and supporting administration tools such as failed-jobs monitoring."

"From the software engineering perspective, the project was developed using Agile DSDM and divided into three timeboxes. The design was supported by use case diagrams, class diagrams, sequence diagrams, ERD, sitemap, prototypes, testing evidence, deployment planning, and user manual documentation."

"In conclusion, the University Academic Portal successfully integrates the major academic administration processes into one platform. It improves efficiency, reduces manual errors, strengthens communication, and supports staff, teachers, students, and guests through role-based workflows."

"Future improvements could include advanced analytics, stronger accessibility support, MFA, mobile optimisation, and deeper integration with other university systems."

"Thank you for watching."

