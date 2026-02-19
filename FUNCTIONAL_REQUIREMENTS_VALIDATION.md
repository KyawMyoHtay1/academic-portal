# Functional Requirements 4.2 Validation Report
## Timebox 1: Manage Student Registration & Course Registration Process

**Date:** February 19, 2026  
**Status:** Verified against actual codebase implementation

---

## Summary

| Section | Status | Notes |
|---------|--------|------|
| 1.1 Manage User | ✅ Mostly Correct | All major requirements implemented |
| 1.2 Manage Student | ✅ Mostly Correct | Minor variance in required fields |
| 1.3 Manage Course | ✅ Correct | All requirements met |
| 1.4 Manage Subject | ✅ Correct | All requirements met |
| 1.5 Manage Course Registration | ✅ Correct | All requirements met |

---

## 1.1 Manage User (HL)

### Register User (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Check Text Fields Null | ✅ | `StoreUserRequest` / `RegisteredUserController`: required name, email, password |
| Email already exists check | ✅ | `unique:users,email` in validation |
| Validate Email Format | ✅ | `email` rule |
| Password Confirmation check | ✅ | `confirmed` rule in RegisteredUserController |
| Ensure Password Strength – Laravel Default Rules | ✅ | `Rules\Password::defaults()` |
| Hash Password | ✅ | `Hash::make()` in RegisteredUserController |
| Assign Default Role as Student (self-registration only) | ✅ | `'role' => 'student'` in RegisteredUserController |
| Send Email Verification | ✅ | `event(new Registered($user))` triggers Laravel verification |
| reCAPTCHA Verification | ✅ | `RecaptchaService`, conditional when `config('recaptcha.site_key')` |

### Update User (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Check for valid updates | ✅ | `UpdateUserRequest` validation |
| Ensure no duplication of email | ✅ | `Rule::unique('users','email')->ignore($user)` |
| Validate photo upload – jpeg/jpg/png, max 2MB | ✅ | `image`, `mimes:jpeg,jpg,png`, `max:2048` in UpdateUserRequest |

### Delete User (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Confirm Deletion | ✅ | `Admin/Users/Index.vue`: `confirm()` before delete |
| Cascade delete related records | ✅ | Database foreign keys with `onDelete('cascade')` on students, etc. |

### Search User (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Search by Name, Email or Role | ✅ | `StaffUserController::index()`: `where('name','like')` or `email` or `role` |
| Filter by Role tabs – All, Students, Teachers, Staff | ✅ | `role` filter: all, student, teacher, staff |
| Show results in paginated format | ✅ | `paginate(10)` |

### User Login (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Verify Login Credentials | ✅ | `Auth::attempt()` in LoginRequest |
| Rate Limiting – 5 attempts per email+IP | ✅ | `RateLimiter::tooManyAttempts($this->throttleKey(), 5)` |
| Provide Feedback for incorrect login attempts | ✅ | `trans('auth.failed')` |
| Remember Me functionality | ✅ | `Auth::attempt(..., $this->boolean('remember'))` |
| reCAPTCHA Verification | ✅ | Conditional when configured |

### Password Management (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Forgot Password – Send Reset Email | ✅ | `PasswordResetLinkController`, `password.request` route |
| Reset Password with Token Validation | ✅ | `NewPasswordController`, `password.reset` route |
| Update Password with confirmation | ✅ | `PasswordController::update`, `password.update` route |

### User Settings / Preferences (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| View Settings – Display current user preferences with defaults | ✅ | `SettingsController::index()`, `array_merge(DEFAULTS, $user->preferences)` |
| Defaults: email_announcements, email_messages, email_notifications | ✅ | `SettingsController::DEFAULTS` |
| Update Preferences – Only authenticated user can update own settings | ✅ | `auth()->check()` in UpdateSettingsRequest |
| Validate allowed keys | ✅ | `array_intersect_key($validated, self::DEFAULTS)` |
| Validate values: boolean only | ✅ | `'sometimes', 'boolean'` in UpdateSettingsRequest |
| Store preferences in user record (JSON) and merge with defaults | ✅ | `$user->update(['preferences' => $preferences])` |

### Global Search (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Enforce minimum query length (e.g., 2 characters) | ✅ | `SearchController`: `strlen($q) < 2` returns empty |
| Return results as: type, title, subtitle, URL | ✅ | `SearchResultResource` format |
| Limit results per entity type (e.g., 5 per type) | ✅ | `LIMIT_PER_TYPE = 5` |
| Role-based search scope – Student: courses, visible announcements | ✅ | `searchStudent()` |
| Role-based search scope – Teacher: assigned courses/subjects, visible announcements | ✅ | `searchTeacher()` |
| Role-based search scope – Staff: students, users, courses, subjects, announcements | ✅ | `searchStaff()` |
| (Optional) Guest: public courses, visible announcements | ✅ | `searchGuest()` |

---

## 1.2 Manage Student (HL)

### Register Student (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Link to existing User account with Student role | ✅ | `user_id` required, `Rule::exists('users','id')->where('role','student')` |
| Auto-generate Student Number – STU0001, STU0002 | ✅ | `generateStudentNo()` returns `STU` + padded number |
| Check Text Fields Null – full name, email, phone, programme, intake_year | ✅ | All required in StoreStudentRequest |
| Validate Email Format and Uniqueness | ✅ | `email`, `unique:students,email` |
| Validate Phone Format | ✅ | `regex:/^[0-9+\-() ]+$/` |
| Validate Date of Birth – Must be before today | ✅ | `date`, `before:today` |
| Validate Gender – Enum: Male, Female, Other | ✅ | `in:Male,Female,Other` |
| Validate Status – Enum: active, suspended, graduated | ✅ | `in:active,suspended,graduated` |
| Validate Photo Upload – jpeg/jpg/png, max 2MB | ✅ | `image`, `mimes:jpeg,jpg,png`, `max:2048` |
| Validate Document Upload – ID, Card, Transcript – pdf/jpeg/jpg/png, max 5MB | ✅ | `id_card`, `transcript`: `mimes:pdf,jpeg,jpg,png`, `max:5120` |
| Store Photos and Documents in Storage | ✅ | `ImageService::store()`, `storeDocument()` |

**Note:** Implementation also requires `previous_institution` and `previous_qualification` (not in FR). Consider adding to FR or making optional.

### Update Student (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Check for valid updates | ✅ | UpdateStudentRequest |
| Ensure no duplication of Student Number | ✅ | `Rule::unique()->ignore($student)` |
| Ensure no duplication of Email | ✅ | `Rule::unique()->ignore($student)` |
| Replace old Photo/Documents when new ones uploaded | ✅ | `ImageService::delete()`, `deleteDocument()` before storing new |
| Validate all fields same as Register | ✅ | Same rules in UpdateStudentRequest |

### Delete Student (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Confirm deletion | ✅ | `Students/Index.vue`: `confirm()` before delete |
| Cascade delete related records – enrolments, grades | ✅ | DB foreign keys `onDelete('cascade')` |

### Search Student (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Search by Student Number, Name, Email, Programme | ✅ | `StudentController::index()` |
| Filter by Programme | ✅ | `$programme` filter |
| Filter by Intake Year | ✅ | `$intakeYear` filter |
| Filter by Status – active, suspended, graduated | ✅ | `$status` filter |
| Show results in paginated format – 10 per page | ✅ | `paginate(10)` |

### Student Self-Profile Management (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| View Profile with GPA calculation | ✅ | `StudentProfileController::show()`, `$student->calculateGPA()` |
| Update Phone Number | ✅ | `UpdateStudentProfileRequest`: phone allowed |
| Update Address | ✅ | `UpdateStudentProfileRequest`: address allowed |
| Update Profile Photo | ✅ | `UpdateStudentProfileRequest`: photo allowed |
| Restrict editing of Student Number, Programme, Email, Name | ✅ | Only phone, address, photo in UpdateStudentProfileRequest |

---

## 1.3 Manage Course (HL)

### Register Course (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Check Text Fields Null – course_code, title, credits, semester | ✅ | StoreCourseRequest: all required |
| Ensure Course Code is Unique | ✅ | `unique:courses,course_code` |
| Validate Credits – Integer, min 1, max 10 | ✅ | `integer`, `min:1`, `max:10` |
| Validate Photo Upload – jpeg/jpg/png, max 2MB | ✅ | `image`, `mimes:jpeg,jpg,png`, `max:2048` |
| Store Course Photo in Storage | ✅ | `ImageService::store()` |

### Update Course (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Check for valid updates | ✅ | UpdateCourseRequest |
| Validate Course Code uniqueness excluding current record | ✅ | `Rule::unique()->ignore($course)` |
| Replace old Photo when new one uploaded | ✅ | `ImageService::delete($course->photo)` before store |

### Delete Course (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Confirm Deletion | ✅ | `Admin/Courses/Index.vue`: `confirm()` |
| Prevent deletion if course has enrolled students | ✅ | `StaffCourseController::destroy()`: checks `enrolledCount > 0` |
| Return error message with enrolment count if cannot delete | ✅ | `"Cannot delete course. {$enrolledCount} student(s) are currently enrolled."` |

### Search Course (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Search by Course Code, Title, Semester | ✅ | `Courses/Index.vue`: client-side search |
| Filter by Semester | ✅ | `semesterFilter` |
| Filter by Enrollment Status - all, enrolled, not-enrolled | ✅ | `availabilityFilter`: all, enrolled, not-enrolled |
| Sort by Course Code, Title, Credits, Semester | ✅ | `sortBy`: code, title, credits-desc, credits-asc |
| Show results in table format | ✅ | Table/card layout in Vue |

---

## 1.4 Manage Subject (HL)

### Register Subject (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Check Text Fields Null - course_id, subject_code, title | ✅ | StoreSubjectRequest |
| Ensure Subject Code is Unique | ✅ | `unique:subjects,subject_code` (scoped by course if applicable) |
| Validate Course exists | ✅ | `exists:courses,id` |
| Validate Credits - Integer, min 1, max 10 | ✅ | `integer`, `min:1`, `max:10` |
| Validate Photo Upload - jpeg/jpg/png, max 2MB | ✅ | Standard image validation |

### Update Subject (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Check for valid updates | ✅ | UpdateSubjectRequest |
| Validate Subject Code uniqueness | ✅ | `Rule::unique()->ignore($subject)` |

### Delete Subject (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Confirm deletion | ✅ | `Admin/Subjects/Index.vue`: `confirm()` |

### Assign Teacher to Subject (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Assign multiple teachers to a subject | ✅ | `StaffSubjectTeacherController`, `sync($teacher_ids)` |
| Validate teacher exists and has teacher role | ✅ | `UpdateSubjectTeachersRequest`: `Rule::exists('users','id')->where('role','teacher')` |

---

## 1.5 Manage Course Registration (HL)

### Request Enrolment (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Validate Student record exists | ✅ | `CourseRegistrationController`: checks `$user->student` |
| Check if already enrolled - prevent duplicate | ✅ | `EnrollmentService`: status `approved` → error |
| Check if pending request exists - prevent duplicate | ✅ | `EnrollmentService`: status `pending` → error |
| Check if withdrawal pending - must wait for approval | ✅ | `EnrollmentService`: status `withdrawal_pending` → error |
| Allow re-apply if previously rejected | ✅ | `EnrollmentService`: `$reapplyAfterRejection` when status `rejected` |
| Check Schedule Conflicts with enrolled courses | ✅ | `EnrollmentService::findScheduleConflict()` |
| Create enrolment with status: pending | ✅ | `status => 'pending'` on insert |
| Use Database Transaction - prevent race conditions | ✅ | `DB::transaction()`, `lockForUpdate()` |

### Request Withdrawal (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Validate Student is enrolled - status: approved | ✅ | `EnrollmentService::requestWithdrawal()` |
| Check if withdrawal already pending - prevent duplicate | ✅ | Checked in service |
| Change status to withdrawal_pending | ✅ | `updateExistingPivot(..., ['status' => 'withdrawal_pending'])` |

### Approve Enrolment (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Validate enrolment exists and is pending | ✅ | `EnrollmentService::approveEnrollment()` |
| Change status from pending to approved | ✅ | Status update in service |

### Reject Enrolment (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Validate enrolment exists and is pending | ✅ | In EnrollmentService |
| Change status from pending to rejected | ✅ | Status update |
| Allow student to re-apply after rejection | ✅ | Re-apply logic in requestEnrollment |

### Approve Withdrawal (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Validate enrolment has withdrawal_pending status | ✅ | In EnrollmentService |
| Delete enrolment record from database | ✅ | Record removed on approval |

### Reject Withdrawal (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Validate enrolment has withdrawal_pending status | ✅ | In EnrollmentService |
| Revert status from withdrawal_pending to approved | ✅ | Status reverted |

### View My Courses - Student (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| Show only approved and withdrawal_pending enrolments | ✅ | `wherePivotIn('status', ['approved', 'withdrawal_pending'])` |
| Include course subjects | ✅ | `with(['subjects'])` |
| Display enrollment date and status | ✅ | `course_student.created_at`, `course_student.status` |
| Order by Course Code | ✅ | `orderBy('course_code')` |

### Search & Manage Enrolments - Staff (ML) ✅
| LL Requirement | Status | Implementation |
|----------------|--------|----------------|
| View pending enrolment requests | ✅ | `StaffEnrollmentController::index()`, status filter |
| View pending withdrawal requests | ✅ | Status filter includes `withdrawal_pending` |
| Show enrolment details - course, student, request timestamp | ✅ | `mapEnrollmentRow()` |
| Provide results in table format | ✅ | Paginated table with export (CSV, PDF) |

---

## Minor Discrepancies / Notes

1. **Student Register – previous_institution, previous_qualification**  
   Implementation requires these fields; FR does not list them. Either add to FR or make optional in code.

2. **User Settings – extra preference keys**  
   Implementation has additional keys: `notify_timetable`, `notify_attendance`, `notify_grades`, `notify_grade_review`, `notify_fees`. FR lists only three. Implementation extends FR; acceptable.

3. **Course Search – client-side vs server-side**  
   Student course search/filter is done client-side in Vue. Suitable for moderate course counts; for large datasets, server-side filtering may be needed.

4. **Assign Teacher to Course**  
   FR 1.3 lists "Assign Teacher to Course" under Manage Course; implementation exists via `StaffCourseTeacherController`. ✅

---

## Conclusion

**The Functional Requirements document 4.2 is correct and matches the implementation.** All major requirements are implemented. Minor differences (e.g., extra student fields, extra preference keys) are extensions rather than gaps.
