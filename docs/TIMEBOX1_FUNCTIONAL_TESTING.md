Timebox 1 Test Plan
Test Script 1 – User Registration (Public)
Test Case Description Input Expected Result
1.1 Required fields validation Leave name, email, or password blank Validation message displayed; submission blocked
1.2 Email format validation Enter invalid email format Invalid email message displayed
1.3 Password confirmation Password and confirm password do not match Password mismatch message displayed
1.4 Password strength Enter weak password Password strength message displayed
1.5 Duplicate email Enter existing email Email already taken message displayed
1.6 Successful registration Enter valid registration data User created and redirected to dashboard

Test Script 2 – User Registration (Admin)
Test Case Description Input Expected Result
2.1 Required fields and role Leave required fields blank Validation message displayed
2.2 Email validation Enter invalid or duplicate email Validation error displayed
2.3 Photo upload validation Upload invalid file type or size File rejected with error message
2.4 Create user Enter valid data and submit User created successfully
2.5 Update user Edit user with valid data User updated successfully
2.6 Delete user Confirm deletion User deleted successfully
2.7 Search and filter Enter keyword or select role filter Filtered results displayed

Test Script 3 – User Login
Test Case Description Input Expected Result
3.1 Required fields Leave email or password blank Validation message displayed
3.2 Invalid credentials Enter incorrect password Error message displayed
3.3 Rate limiting Attempt login 5+ times with wrong password Too many attempts message displayed
3.4 Remember Me Select Remember Me and login Session remains active
3.5 Successful login Enter valid credentials Redirected to dashboard
3.6 reCAPTCHA validation Submit without completing reCAPTCHA Verification required message displayed

Test Script 4 – User Update, Delete, Search
Test Case Description Input Expected Result
4.1 Update validation Enter duplicate email Validation message displayed
4.2 Photo validation Upload invalid photo Validation message displayed
4.3 Delete user Confirm delete action User removed successfully
4.4 Search function Enter search keyword Matching records displayed
4.5 Role filter Select role tab Filtered list displayed

Test Script 5 – Password Management
Test Case Description Input Expected Result
5.1 Forgot password Enter registered email Reset email sent
5.2 Reset password Enter valid reset token and new password Password updated successfully
5.3 Change password Change password while logged in Password changed successfully

Test Script 6 – User Settings
Test Case Description Input Expected Result
6.1 Display default settings Open settings page Default preferences displayed
6.2 Update preferences Change settings and save Preferences saved successfully

Test Script 7 – Global Search
Test Case Description Input Expected Result
7.1 Minimum search length Enter less than 2 characters No results displayed
7.2 Role-based search Search using different user roles Results limited by role

Test Script 8 – Student Registration
Test Case Description Input Expected Result
8.1 Link student to user Select valid student user User dropdown populated correctly
8.2 Auto student number Leave student number blank System auto-generates student number
8.3 Validation checks Leave required fields blank Validation messages displayed
8.4 File upload validation Upload valid/invalid files Files validated and stored correctly
8.5 Successful registration Enter valid student data Student created successfully

Test Script 9 – Student Update, Delete, Search
Test Case Description Input Expected Result
9.1 Update validation Enter duplicate student number Validation message displayed
9.2 Replace files Upload new photo or document Old file replaced successfully
9.3 Delete student Confirm delete action Student removed successfully
9.4 Search and filter Apply filters Filtered results displayed
9.5 Pagination Navigate to next page Correct records displayed

Test Script 10 – Student Self Profile
Test Case Description Input Expected Result
10.1 View profile Open profile page Student information displayed
10.2 Update allowed fields Edit phone/address Changes saved successfully
10.3 Restricted fields Attempt to edit restricted fields Fields not editable

Test Script 11 – Course Management
Test Case Description Input Expected Result
11.1 Required fields validation Leave required fields blank Validation message displayed
11.2 Unique course code Enter duplicate course code Error message displayed
11.3 Photo validation Upload invalid image Validation message displayed
11.4 Delete with enrolments Delete course with enrolments Error displayed
11.5 Search/filter/sort Apply search and filters Correct results displayed
11.6 Successful CRUD Enter valid data Success message displayed

Test Script 12 – Subject Management
Test Case Description Input Expected Result
12.1 Required fields validation Leave required fields blank Validation message displayed
12.2 Unique subject code Enter duplicate subject code Error message displayed
12.3 Assign teacher Assign valid teacher Assignment saved successfully
12.4 Invalid teacher assignment Assign non-teacher user Validation error displayed
12.5 Delete subject Confirm delete action Subject removed successfully

Test Script 13 – Course Enrolment & Withdrawal
Test Case Description Input Expected Result
13.1 Request enrolment Student submits enrolment Pending enrolment created
13.2 Duplicate enrolment prevention Attempt duplicate enrolment Enrolment blocked
13.3 Schedule conflict Enrol in conflicting course Conflict error displayed
13.4 Approve/reject enrolment Staff approves or rejects Status updated correctly
13.5 Request withdrawal Student requests withdrawal Status set to pending
13.6 Approve withdrawal Staff approves withdrawal Enrolment removed
13.7 Reject withdrawal Staff rejects withdrawal Status reverted
13.8 View My Courses Open My Courses page Correct courses displayed
13.9 Manage enrolments Staff manages enrolments Correct records displayed

Timebox 1 Test Log
Test Script (1) User Registration (Public Site)

Test Case Expected Result Actual Result Action Taken Evidence
1.1 Validation messages displayed; submission blocked Validation messages were displayed as specified. No corrective action required Fig.1.1 & 1.2

Before Testing – Fig.1.1

After Testing – Fig.1.2

 
Test Case Expected Result Actual Result Action Taken Evidence
1.2 Invalid email format message displayed Email validation functioned correctly No defects identified Fig.1.3 & 1.4

Before Testing – Fig.1.3

After Testing – Fig.1.4

Test Case Expected Result Actual Result Action Taken Evidence
1.3 Password mismatch message displayed Password mismatch detected correctly Behaviour matched specification Fig.1.5 & 1.6

Before Testing – Fig.1.5

After Testing – Fig.1.6

Test Case Expected Result Actual Result Action Taken Evidence
1.4 Password strength requirement displayed Password strength rule enforced No corrective action required Fig.1.7 & 1.8

Before Testing – Fig.1.7

After Testing – Fig.1.8

Test Case Expected Result Actual Result Action Taken Evidence
1.5 Duplicate email message displayed Duplicate email prevented successfully Behaviour matched system specification. Fig.1.9 & 1.10

Before Testing – Fig.1.9

After Testing – Fig.1.10

Test Case Expected Result Actual Result Action Taken Evidence
1.6 User created and redirected to dashboard Registration completed successfully Functioned according to specification Fig.1.11 & 1.12

Before Testing – Fig.1.11

After Testing – Fig.1.12

Test Script (2) User Registration (Admin Site)
Test Case Expected Result Actual Result Action Taken Evidence
2.1 Validation message displayed when name/email/role blank; submission blocked Validation displayed correctly; record not created No corrective action required Fig.2.1 & 2.2

Before Testing – Fig.2.1

After Testing – Fig.2.2

Test Case Expected Result Actual Result Action Taken Evidence
2.2 Validation/duplicate message displayed for invalid or duplicate email Email validation worked; duplicate prevented No corrective action required Fig.2.3 & 2.4

 
Before Testing – Fig.2.3

After Testing – Fig.2.4

Test Case Expected Result Actual Result Action Taken Evidence
2.3 Invalid file type/size message displayed; upload rejected File validation worked correctly; invalid files rejected No corrective action required Fig.2.5 & 2.6

Before Testing – Fig.2.5

After Testing – Fig.2.6

Test Case Expected Result Actual Result Action Taken Evidence
2.4 User created successfully; success message displayed User created successfully; success message shown No corrective action required Fig.2.7, Fig.2.8 & Fig.2.9

Before Testing – Fig.2.7

After Testing – Fig.2.8

After Testing – Fig.2.9

Test Case Expected Result Actual Result Action Taken Evidence
2.5 User updated successfully; success message displayed User updated successfully; changes saved No corrective action required Fig.2.10 & 2.11

Before Testing – Fig.2.10

After Testing – Fig.2.11

Test Case Expected Result Actual Result Action Taken Evidence
2.6 User deleted successfully; related records cascade (if applicable) User deleted successfully; deletion confirmed No corrective action required Fig.2.12, Fig.2.13 & Fig.2.14
Before Testing – Fig.2.12

Before Testing – Fig.2.13

After Testing – Fig.2.14

Test Case Expected Result Actual Result Action Taken Evidence
2.7 Search and role filters return correct results with pagination Search and filters worked correctly; correct results shown No corrective action required Fig.2.15 & 2.16

Before Testing – Fig.2.15

After Testing – Fig.2.16

Test Script (3) User Login
Test Case Expected Result Actual Result Action Taken Evidence
3.1 Validation message displayed when email/password blank Validation displayed correctly; login blocked No corrective action required Fig.3.1 & 3.2

Before Testing – Fig.3.1

After Testing – Fig.3.2

Test Case Expected Result Actual Result Action Taken Evidence
3.2 Incorrect credentials message displayed Incorrect credentials message displayed correctly No corrective action required Fig.3.3 & 3.4

Before Testing – Fig.3.3

After Testing – Fig.3.4

Test Case Expected Result Actual Result Action Taken Evidence
3.3 Throttle/too many attempts message displayed after 5+ failures Rate limiting triggered correctly No corrective action required Fig.3.5 & 3.6

Before Testing – Fig.3.5

After Testing – Fig.3.6

Test Case Expected Result Actual Result Action Taken Evidence
3.4 Session persists after browser close when Remember Me checked Session persisted as expected No corrective action required Fig.3.7 & 3.8

Before Testing – Fig.3.7

After Testing – Fig.3.8

Test Case Expected Result Actual Result Action Taken Evidence
3.5 Redirect to role-based dashboard after valid login Redirected correctly to dashboard No corrective action required Fig.3.9 & 3.10

Before Testing – Fig.3.9

After Testing – Fig.3.10

Test Case Expected Result Actual Result Action Taken Evidence
3.6 reCAPTCHA required message displayed if not completed (if enabled) reCAPTCHA message displayed correctly No corrective action required Fig.3.11 & 3.12

Before Testing – Fig.3.11

After Testing – Fig.3.12

Test Script (4) User Update, Delete, Search
Test Case Expected Result Actual Result Action Taken Evidence
4.1 Validation message displayed when updating with invalid/duplicate email Validation displayed correctly; update blocked No corrective action required Fig.4.1 & 4.2

Before Testing – Fig.4.1

After Testing – Fig.4.2

Test Case Expected Result Actual Result Action Taken Evidence
4.2 Validation message displayed for invalid photo upload Photo validation worked correctly No corrective action required Fig.4.3 & 4.4

 
Before Testing – Fig.4.3

After Testing – Fig.4.4

Test Case Expected Result Actual Result Action Taken Evidence
4.3 User deleted successfully after confirmation; related records handled Deletion performed correctly; confirmation shown No corrective action required Fig.4.5 & 4.6

Before Testing – Fig.4.5

After Testing – Fig.4.6

Test Case Expected Result Actual Result Action Taken Evidence
4.4 Search returns matching records based on keyword/filter Correct matching results displayed No corrective action required Fig.4.7 & 4.8

Before Testing – Fig.4.7

After Testing – Fig.4.8

Test Case Expected Result Actual Result Action Taken Evidence
4.5 Role tabs filter list correctly with pagination Filter worked correctly; pagination correct No corrective action required Fig.4.9 & 4.10

Before Testing – Fig.4.9

After Testing – Fig.4.10

Test Script (5) Password Management

Test Case Expected Result Actual Result Action Taken Evidence
5.1 Reset email sent / success message shown after request Reset flow worked; success shown No corrective action required Fig.5.1 & 5.2

Before Testing – Fig.5.1

After Testing – Fig.5.2

 
Test Case Expected Result Actual Result Action Taken Evidence
5.2 Password updated successfully using reset token Password reset successful No corrective action required Fig.5.3 & 5.4

Before Testing – Fig.5.3

After Testing – Fig.5.4

Test Case Expected Result Actual Result Action Taken Evidence
5.3 Password updated successfully for logged-in user Password update successful; confirmation shown No corrective action required Fig.5.5 & 5.6

Before Testing – Fig.5.5

After Testing – Fig.5.6

Test Script (6) User Settings / Preferences
Test Case Expected Result Actual Result Action Taken Evidence
6.1 Default settings displayed correctly Defaults displayed correctly No corrective action required Fig.6.1, Fig.6.2, Fig.6.3 & Fig.6.4

Before Testing – Fig.6.1

After Testing – Fig.6.2

After Testing – Fig.6.3

After Testing – Fig.6.4

 
Test Case Expected Result Actual Result Action Taken Evidence
6.2 Preferences saved successfully; success message shown Preferences saved successfully No corrective action required Fig.6.5 & 6.6

Before Testing – Fig.6.5

After Testing – Fig.6.6

Test Script (7) Global Search
Test Case Expected Result Actual Result Action Taken Evidence
7.1 Query < 2 characters returns empty/no-search result Behaviour corrects; no results displayed No corrective action required Fig.7.1 & 7.2

Before Testing – Fig.7.1

After Testing – Fig.7.2

Test Case Expected Result Actual Result Action Taken Evidence
7.2 Results scoped correctly by role Role-based results displayed correctly No corrective action required Fig.7.3 & 7.4

Before Testing – Fig.7.3

After Testing – Fig.7.4

Test Script (8) Student Registration
Test Case Expected Result Actual Result Action Taken Evidence
8.1 User dropdown shows only Student-role users without student profile Dropdown populated correctly with eligible users No corrective action required Fig.8.1 & 8.2

Before Testing – Fig.8.1

After Testing – Fig.8.2

 

Test Case Expected Result Actual Result Action Taken Evidence
8.2 Student number auto-generated when left blank (e.g., STU0001…) Student number generated correctly No corrective action required Fig.8.3, Fig.8.4, Fig.8.5 & Fig.8.6

Before Testing – Fig.8.3

Before Testing – Fig.8.4
After Testing – Fig.8.5

After Testing – Fig.8.6

Test Case Expected Result Actual Result Action Taken Evidence
8.3 Validation messages displayed for missing/invalid required fields Validation displayed correctly; submission blocked No corrective action required Fig.8.7, Fig.8.8, Fig.8.9 & Fig.8.10

Before Testing – Fig.8.7

Before Testing – Fig.8.8

After Testing – Fig.8.9

After Testing – Fig.8.10

Test Case Expected Result Actual Result Action Taken Evidence
8.4 File upload validates type/size and stores valid files Upload validation worked; valid files stored No corrective action required Fig.8.11 & 8.12

Before Testing – Fig.8.11

After Testing – Fig.8.12

Test Case Expected Result Actual Result Action Taken Evidence
8.5 Student record created and linked to selected user Student created successfully and linked No corrective action required Fig.8.13, Fig.8.14, Fig.8.15 & Fig.8.16

Before Testing – Fig.8.13

Before Testing – Fig.8.14

After Testing – Fig.8.15

After Testing – Fig.8.16

Test Script (9) Student Update, Delete, Search

Test Case Expected Result Actual Result Action Taken Evidence
9.1 Validation message shown for duplicate student_no/email Validation displayed correctly; update blocked No corrective action required Fig.9.1 & 9.2

Before Testing – Fig.9.1

After Testing – Fig.9.2

 
Test Case Expected Result Actual Result Action Taken Evidence
9.2 New photo/document replaces old; new file stored correctly Replacement worked; old file replaced No corrective action required Fig.9.3 & 9.4

Before Testing – Fig.9.3

After Testing – Fig.9.4

Test Case Expected Result Actual Result Action Taken Evidence
9.3 Student deleted after confirmation; related records cascaded Deletion successful; cascade executed No corrective action required Fig.9.5, Fig.9.6 & Fig.9.7

Before Testing – Fig.9.5

After Testing – Fig.9.6

After Testing – Fig.9.7

Test Case Expected Result Actual Result Action Taken Evidence
9.4 Search/filters return correct results (10 per page) Results filtered correctly; pagination correct No corrective action required Fig.9.8 & 9.9

Before Testing – Fig.9.8

After Testing – Fig.9.9

Test Case Expected Result Actual Result Action Taken Evidence
9.5 Pagination navigates correct records per page (10 items) Correct page data displayed No corrective action required Fig.9.10 & 9.11

Before Testing – Fig.9.10

After Testing – Fig.9.11

Test Script (10) Student Self Profile

Test Case Expected Result Actual Result Action Taken Evidence
10.1 Profile page displays student & academic information Information displayed correctly No corrective action required Fig.10.1, Fig.10.2 & Fig.10.3

Before Testing – Fig.10.1

After Testing – Fig.10.2

After Testing – Fig.10.3

Test Case Expected Result Actual Result Action Taken Evidence
10.2 Allowed fields update successfully (phone/address/photo) Changes saved successfully No corrective action required Fig.10.4 & 10.5
Before Testing – Fig.10.4

After Testing – Fig.10.5

Test Case Expected Result Actual Result Action Taken Evidence
10.3 Restricted fields are read-only/not editable Restricted fields not editable No corrective action required Fig.10.6

Fig.10.6

Test Script (11) Course Management
Test Case Expected Result Actual Result Action Taken Evidence
11.1 Validation shown for missing fields/invalid credits Validation worked correctly No corrective action required Fig.11.1 & 11.2

Before Testing – Fig.11.1

After Testing – Fig.11.2

 
Test Case Expected Result Actual Result Action Taken Evidence
11.2 Duplicate course code rejected with error message Duplicate blocked successfully No corrective action required Fig.11.4 & 11.5

Before Testing – Fig.11.4

After Testing – Fig.11.5

Test Case Expected Result Actual Result Action Taken Evidence
11.3 invalid photo type/size rejected with validation message Validation worked correctly No corrective action required Fig.11.6 & 11.7

Before Testing – Fig.11.6

After Testing – Fig.11.7

Test Case Expected Result Actual Result Action Taken Evidence
11.4 Course with enrolments cannot be deleted; error shown Deletion blocked; enrolment error shown No corrective action required Fig.11.8 & 11.9

Before Testing – Fig.11.8

After Testing – Fig.11.9

Test Case Expected Result Actual Result Action Taken Evidence
11.5 Search/filter/sort displays correct table results Correct results displayed No corrective action required Fig.11.10 & 11.11

Before Testing – Fig.11.10

After Testing – Fig.11.11

Test Case Expected Result Actual Result Action Taken Evidence
11.6 Valid CRUD actions succeed with success message CRUD performed successfully No corrective action required Fig.11.12 & 11.13

Before Testing – Fig.11.12

After Testing – Fig.11.13

Test Script (12) Subject Management
Test Case Expected Result Actual Result Action Taken Evidence
12.1 Validation shown for missing fields/invalid credits Validation displayed correctly No corrective action required Fig.12.1, Fig.12.2 & Fig.12.3

Before Testing – Fig.12.1

Before Testing – Fig.12.2

After Testing – Fig.12.3

 
Test Case Expected Result Actual Result Action Taken Evidence
12.2 Duplicate subject_code rejected with error message Duplicate blocked successfully No corrective action required Fig.12.4 & 12.5

Before Testing – Fig.12.4

After Testing – Fig.12.5

Test Case Expected Result Actual Result Action Taken Evidence
12.3 Assigning valid teacher(s) saves assignments Assignments saved correctly No corrective action required Fig.12.6, Fig.12.7 & Fig.12.8

Before Testing – Fig.12.6

Before Testing – Fig.12.7

After Testing – Fig.12.8

Test Case Expected Result Actual Result Action Taken Evidence
12.4 Non-teacher assignment rejected with validation Rejected as expected No corrective action required Fig.12.9 & 12.10

Before Testing – Fig.12.9

After Testing – Fig.12.10

Test Case Expected Result Actual Result Action Taken Evidence
12.5 Subject deleted after confirmation Deleted successfully No corrective action required Fig.12.11 & 12.12

Before Testing – Fig.12.11

After Testing – Fig.12.12

Test Script (13) Course Registration (Enrolment & Withdrawal)
Test Case Expected Result Actual Result Action Taken Evidence
13.1 Enrolment request creates pending record Pending enrolment created successfully No corrective action required Fig.13.1, Fig.13.2 & Fig.13.3

Before Testing – Fig.13.1

After Testing – Fig.13.2

 
Test Case Expected Result Actual Result Action Taken Evidence
13.2 Duplicate enrolment prevented (button hidden / notice shown) Duplicate prevented; Enrol button hidden No corrective action required Fig.13.4

Fig.13.4

Test Case Expected Result Actual Result Action Taken Evidence
13.3 Schedule conflict displays conflict error Conflict error displayed correctly No corrective action required Fig.13.5 & 13.6

Before Testing – Fig.13.5

After Testing – Fig.13.6

Test Case Expected Result Actual Result Action Taken Evidence
13.4 Staff approval/rejection updates status correctly Status updated correctly No corrective action required Fig.13.7 & 13.8

Before Testing – Fig.13.7

After Testing – Fig.13.8

Test Case Expected Result Actual Result Action Taken Evidence
13.5 Withdrawal request changes status to withdrawal_pending Status changed correctly No corrective action required Fig.13.9 & 13.10

Before Testing – Fig.13.9

After Testing – Fig.13.10

Test Case Expected Result Actual Result Action Taken Evidence
13.6 Approving withdrawal removes enrolment record Enrolment removed successfully No corrective action required Fig.13.11 & 13.12

Before Testing – Fig.13.11

Before Testing – Fig.13.12

Test Case Expected Result Actual Result Action Taken Evidence
13.7 Rejecting withdrawal restores approved status Status reverted correctly No corrective action required Fig.13.13, Fig.13.14 & Fig.13.15

Before Testing – Fig.13.13

After Testing – Fig.13.14

After Testing – Fig.13.15

Test Case Expected Result Actual Result Action Taken Evidence
13.8 My Courses shows only approved/withdrawal_pending correctly ordered Correct list displayed No corrective action required Fig.13.16 & 13.17

Before Testing – Fig.13.16

After Testing – Fig.13.17

Test Case Expected Result Actual Result Action Taken Evidence
13.9 Staff can view/manage pending lists with correct actions Lists/actions displayed correctly No corrective action required Fig.13.18 & 13.19

Before Testing – Fig.13.18

After Testing – Fig.13.19
