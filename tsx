4.3 MoSCoW Prioritization
Must
Manage User
Feature	Justification
Register User	Add new users to the system
- Email already exists check	Avoid duplicate accounts
- Validate Email Format	Ensure valid email addresses
- Hash Password	Protect user credentials from theft
- Assign Default Role as Student	Automatically categorize new users
User Login	Allow secure access to the system
- Verify Login Credentials	Ensure only authorized users can login
- Provide Feedback for incorrect login attempts	Help users understand login failures
Manage Student
Feature	Justification
Register Student	Add new student records to the system
- Link to existing User account	Connect student profile to user authentication
- Auto-generate Student Number	Ensure unique identification for each student
- Check Text Fields Null	Ensure all required information is filled
- Validate Email Format and Uniqueness	Prevent duplicate student emails
- Validate Phone Format	Ensure valid contact information
Update Student	Keep student records up to date
- Ensure no duplication of Student Number	Prevent duplicate student IDs
- Ensure no duplication of Email	Prevent duplicate email addresses
Manage Course
Feature	Justification
Register Course	Add new courses to the system
- Check Text Fields Null	Ensure all course details are filled
- Ensure Course Code is Unique	Avoid duplicate course entries
- Validate Credits	Ensure valid credit values (1-10)
Update Course	Keep course information current
- Validate Course Code uniqueness	Prevent duplicate codes after update
Manage Subject
Feature	Justification
Register Subject	Add subjects to courses
- Check Text Fields Null	Ensure all subject details are filled
- Ensure Subject Code is Unique	Avoid duplicate subject entries
- Validate Course exists	Ensure subject is linked to valid course
Manage Course Registration
Feature	Justification
Request Enrollment	Allow students to enroll in courses
- Validate Student record exists	Ensure valid student is enrolling
- Check if already enrolled	Prevent duplicate enrollments
- Create enrollment with status pending	Track enrollment requests
Approve Enrollment	Staff confirms student enrollment
- Validate enrollment exists and is pending	Ensure valid approval process
- Change status from pending to approved	Complete the enrollment process
Request Withdrawal	Allow students to withdraw from courses
- Validate Student is enrolled	Ensure student can withdraw
- Change status to withdrawal_pending	Track withdrawal requests
Manage Grade
Feature	Justification
Record Grade - Teacher	Allow teachers to submit student grades
- Validate Teacher is assigned to Subject	Ensure proper authorization
- Validate Student is enrolled in Course	Ensure valid grade assignment
- Validate Score (0-100)	Ensure valid grade values
- Set status to pending	Require staff approval for grades
Approve Grade - Staff	Staff confirms submitted grades
- Validate Grade exists and is pending	Ensure valid approval process
- Update status to approved	Make grades visible to students
View Grade - Student	Students can see their approved grades
- Show only approved grades	Display verified grades only
- Calculate and display overall GPA	Show academic performance
Manage Fee
Feature	Justification
Register Fee - Staff	Create fee records for students
- Validate Student exists	Ensure fee is assigned to valid student
- Validate Amount	Ensure valid fee amount
- Validate Due Date	Set payment deadline
- Notify Student	Inform student of new fee
View Fee - Student	Students can see their fees
- Show only authenticated Student's fees	Protect fee privacy
- Display Amount, Status, Due Date	Show payment information
Manage Payment
Feature	Justification
Submit Payment Confirmation - Student	Students confirm manual payment
- Validate Fee belongs to Student	Ensure proper authorization
- Validate Fee is not already paid	Prevent duplicate payments
- Update status to payment_pending	Track payment confirmations
Approve Payment - Staff	Staff confirms payment received
- Validate Fee is in payment_pending status	Ensure valid approval
- Update status to paid	Complete payment process
- Notify Student	Confirm payment to student
Manage Timetable
Feature	Justification
Register Timetable - Staff	Create class schedules
- Validate Subject exists	Ensure valid subject assignment
- Validate Start/End Time format	Ensure proper time entry
- Validate End Time after Start Time	Ensure logical time range
- Detect Schedule Conflicts	Prevent overlapping sessions
View Timetable - Student	Students see their class schedule
- Show timetables for enrolled Courses	Display relevant schedules only
View Timetable - Teacher	Teachers see their teaching schedule
- Show timetables for assigned Subjects	Display relevant schedules only
Manage Attendance
Feature	Justification
Record Attendance - Teacher	Track student attendance
- Validate Teacher is assigned to Subject	Ensure proper authorization
- Validate Date format	Ensure valid date entry
- Validate Students are enrolled	Ensure valid attendance marking
- Use updateOrCreate to prevent duplicates	Prevent duplicate records
View Attendance - Student	Students see their attendance record
- Show overall statistics	Display attendance performance
- Show breakdown by Course/Subject	Detailed attendance view
Should
Manage User
Feature	Justification
Register User	
- Password Confirmation check	Ensure password is entered correctly
- Ensure Password Strength	Keep passwords secure
- Send Email Verification	Verify user email addresses
Update User	Change user information
- Check for valid updates	Allow only correct changes
- Validate photo upload	Ensure valid profile photos
Delete User	Remove users if needed
- Confirm Deletion	Prevent accidental removal
- Cascade delete related records	Maintain data integrity
Password Management	
- Forgot Password functionality	Help users recover access
- Reset Password with Token	Secure password recovery
Manage Student
Feature	Justification
Register Student	
- Validate Date of Birth	Ensure valid birth date
- Validate Gender enum	Ensure valid gender selection
- Validate Status enum	Ensure valid student status
- Validate Photo/Document Upload	Ensure valid file uploads
Delete Student	Remove student records if needed
- Confirm Deletion	Prevent accidental removal
- Cascade delete related records	Maintain data integrity
Search Student	Find students quickly
- Search by Student Number, Name, Email	Easy student lookup
- Filter by Programme, Intake Year, Status	Narrow down search results
- Show results in paginated format	Handle large result sets
Student Self-Profile Management	Students manage their own profile
- Update Phone Number, Address, Photo	Allow self-service updates
- Restrict editing of Student Number, Email	Protect critical data
Manage Course
Feature	Justification
Delete Course	Remove courses no longer offered
- Confirm Deletion	Prevent accidental removal
- Prevent deletion if enrolled students exist	Protect active enrollments
Search Course	Find courses quickly
- Search by Course Code, Title, Semester	Easy course lookup
- Filter by Semester, Enrollment Status	Narrow down results
Manage Subject
Feature	Justification
Update Subject	Keep subject information current
- Validate Subject Code uniqueness	Prevent duplicate codes
Delete Subject	Remove subjects if needed
- Confirm Deletion	Prevent accidental removal
Assign Teacher to Subject	Link teachers to subjects
- Assign multiple teachers	Allow team teaching
Manage Course Registration
Feature	Justification
Reject Enrollment	Staff denies enrollment request
- Change status to rejected	Record rejection
- Allow student to re-apply	Give second chance
Approve Withdrawal	Staff confirms withdrawal
- Delete enrollment record	Remove from course
Reject Withdrawal	Staff denies withdrawal
- Revert status to approved	Keep student enrolled
View My Courses - Student	Students see enrolled courses
- Include course subjects	Show course details
- Display enrollment date and status	Show enrollment info
Manage Grade
Feature	Justification
Reject Grade - Staff	Staff rejects submitted grade
- Record rejection_reason	Explain rejection to teacher
- Create Review Log entry	Audit trail
View Grade - Teacher	Teachers see grading interface
- Show enrolled Students	Display who to grade
- Show graded/ungraded count	Track grading progress
Search Grade	Find grades quickly
- Search by Course, Subject, Student	Easy grade lookup
- Filter by Semester	Narrow down results
Calculate Letter Grade	Convert numeric to letter
- A: 80-100, B: 70-79, C: 60-69, D: 50-59, E: 40-49, F: 1-39	Standard grading scale
Manage Fee
Feature	Justification
Update Fee - Staff	Modify fee records
- Validate all fields	Ensure data integrity
- Auto-set Paid Date if status changed to paid	Automate date tracking
- Notify Student if status changed	Keep student informed
Delete Fee - Staff	Remove fee records if needed
- Confirm Deletion	Prevent accidental removal
Search Fee - Staff	Find fees quickly
- Search by Description, Amount, Student	Easy fee lookup
- Filter by Status	Narrow down results
- Display statistics	Show fee summaries
Track Late Payment	Identify overdue fees
- Calculate days overdue	Track payment delays
- Display in separate Late Payments section	Highlight overdue fees
Manage Payment
Feature	Justification
Reject Payment - Staff	Staff rejects payment confirmation
- Update status back to pending	Allow re-submission
- Notify Student	Inform about rejection
Generate Receipt - Staff	Create payment receipts
- Generate PDF Receipt	Official payment record
- Include Student and Fee details	Complete receipt information
Manage Timetable
Feature	Justification
Update Timetable - Staff	Modify class schedules
- Detect Schedule Conflicts	Prevent overlapping sessions
- Notify Students and Teachers	Inform about changes
Delete Timetable - Staff	Remove schedules if needed
- Confirm Deletion	Prevent accidental removal
Manage Attendance
Feature	Justification
Report Attendance - Staff	View attendance reports
- Show overall statistics	System-wide attendance view
- Identify low attendance Students	Find at-risk students
- Show top 20 low attendance Students	Priority attention list
Manage Announcement
Feature	Justification
Register Announcement - Staff	Create system announcements
- Validate Title, Body, Priority	Ensure complete announcements
- Validate Audience Roles	Target specific users
- Set Publish At and Expires At	Schedule announcements
Register Announcement - Teacher	Teachers create announcements
- Set default audience to Students	Target student audience
Update Announcement	Modify announcements
- Validate ownership for Teachers	Teachers edit only their own
Delete Announcement	Remove announcements
- Validate ownership for Teachers	Teachers delete only their own
View Announcement - All Users	Users see announcements
- Filter by visibility and role	Show relevant announcements
- Order by Pinned, Priority	Important announcements first
Mark Announcement as Read	Track read status
- Set read_at timestamp	Record when read
Manage Message
Feature	Justification
Send Message	Users communicate with each other
- Validate Receiver exists	Ensure valid recipient
- Prevent sending to self	Logical restriction
View Message - Inbox	Users see their messages
- Show sent and received	Complete message history
- Include read status	Track message status
Mark Message as Read	Track read status
- Update read status	Record when read
Manage Notification
Feature	Justification
View Notification	Users see system notifications
- Show last 50 notifications	Recent notifications
- Include read status	Track notification status
Mark Notification as Read	Track read status
- Set read_at timestamp	Record when read
Mark All as Read	Bulk action
- Batch update	Clear all notifications
Could
Manage User
Feature	Justification
Register User	
- reCAPTCHA Verification	Prevent bot registrations
User Login	
- Rate Limiting (5 attempts)	Improve security
- Remember Me functionality	Improve user experience
- reCAPTCHA Verification	Prevent brute force attacks
Manage Student
Feature	Justification
View Profile with GPA calculation	Show academic performance on profile
Manage Course Registration
Feature	Justification
Check Schedule Conflicts	Prevent overlapping course times
- Compare timetables with enrolled courses	Detect time conflicts
- Return error with conflicting course details	Inform student of conflict
Use Database Transaction	Prevent race conditions
- Handle concurrent enrollment attempts	Ensure data integrity
Manage Grade
Feature	Justification
Log Grade Review Actions	Audit trail for grades
- Track all actions in grade_review_logs	Complete history
- Record performed_by, reason, meta	Detailed audit data
Notify Staff on Grade Submission	Alert staff of pending grades
- GradeReviewRequested notification	Prompt staff review
Notify Student on Grade Approval	Inform student of new grade
- GradePublished notification	Keep student informed
Manage Payment
Feature	Justification
Process Stripe Payment	Online payment option
- Create Stripe Checkout Session	Enable card payments
- Handle Success/Cancel redirects	Complete payment flow
Handle Stripe Webhook	Automatic payment processing
- Update fee status automatically	Reduce manual work
- Record payment method and timestamp	Complete payment record
Manage Timetable
Feature	Justification
Notify on Timetable Changes	Keep users informed
- TimetableUpdated notification	Alert affected users
Display in Week Grid format	Visual timetable view
Manage Attendance
Feature	Justification
Low Attendance Alert	Proactive intervention
- Configure threshold (default 75%)	Customizable alert level
- Configure cooldown (default 7 days)	Prevent alert spam
- Notify via database and email	Multi-channel alerts
Notify Student on Attendance	Keep student informed
- AttendanceAlert notification	Immediate feedback
Manage Announcement
Feature	Justification
Acknowledge Announcement	Track important announcements
- Set acknowledged_at timestamp	Record acknowledgment
- Require acknowledgment option	Ensure message is seen
Manage Contact Message
Feature	Justification
View Contact Message - Staff	Manage contact form submissions
- Search across multiple fields	Find specific messages
- Show unread count	Track pending messages
Mark Contact Message as Read	Track message status
Manage Feedback Message
Feature	Justification
View Feedback Message - Staff	Manage feedback submissions
- Search across multiple fields	Find specific feedback
- Show unread count	Track pending feedback
Mark Feedback Message as Read	Track feedback status
Would
Manage User
Feature	Justification
Search User	
- Filter by Role tabs	Quick role-based filtering
Manage Fee
Feature	Justification
View Fee - Student	
- Toggle between Cards and Table view	User preference for display
Manage Attendance
Feature	Justification
View Attendance - Student	
- Show recent records (last 30 days)	Quick recent history view
Manage Announcement
Feature	Justification
Scheduling Announcements	
- Publish At date	Schedule future announcements
- Expires At date	Auto-expire old announcements
- Visible From/Until	Fine-grained visibility control
