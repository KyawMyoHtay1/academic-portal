<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MyCoursesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffAnnouncementController;
use App\Http\Controllers\StaffAttendanceAlertsController;
use App\Http\Controllers\StaffAttendanceReportController;
use App\Http\Controllers\StaffContactMessageController;
use App\Http\Controllers\StaffCourseController;
use App\Http\Controllers\StaffCourseTeacherController;
use App\Http\Controllers\StaffEnrollmentController;
use App\Http\Controllers\StaffFailedJobController;
use App\Http\Controllers\StaffFeeController;
use App\Http\Controllers\StaffFeedbackMessageController;
use App\Http\Controllers\StaffGradesController;
use App\Http\Controllers\StaffSubjectController;
use App\Http\Controllers\StaffSubjectTeacherController;
use App\Http\Controllers\StaffTimetableController;
use App\Http\Controllers\StaffUserController;
use App\Http\Controllers\StudentAssignmentController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentFeeController;
use App\Http\Controllers\StudentGradesController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentTimetableController;
use App\Http\Controllers\TeacherAnnouncementController;
use App\Http\Controllers\TeacherAssignmentController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherCoursesController;
use App\Http\Controllers\TeacherGradesController;
use App\Http\Controllers\TeacherTimetableController;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

// Resolve manual PDF path while supporting both underscore and space filenames.
$resolveUserManualPath = static function (): string {
    $candidates = [
        public_path('docs/University_Academic_Portal_User_Manual.pdf'),
        public_path('docs/University Academic Portal User Manual.pdf'),
    ];

    foreach ($candidates as $path) {
        if (is_file($path)) {
            return $path;
        }
    }

    abort(404, 'User manual PDF not found.');
};

// Guest-facing public pages (read-only)
Route::get('/guest/user-manual', function () use ($resolveUserManualPath) {
    $path = $resolveUserManualPath();

    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="University_Academic_Portal_User_Manual.pdf"',
        'X-Content-Type-Options' => 'nosniff',
    ]);
})->name('guest.user-manual.view');

Route::get('/guest/user-manual/download', function () use ($resolveUserManualPath) {
    $path = $resolveUserManualPath();

    return response()->download($path, 'University_Academic_Portal_User_Manual.pdf', [
        'Content-Type' => 'application/pdf',
        'X-Content-Type-Options' => 'nosniff',
    ]);
})->name('guest.user-manual.download');

Route::get('/', function () {
    // Dynamic Statistics
    $totalCourses = Course::count();
    $totalStudents = Student::count();
    $totalFaculty = User::where('role', 'teacher')->count();

    // Calculate success rate based on attendance (present rate)
    $totalAttendance = Attendance::count();
    $presentAttendance = Attendance::where('status', 'present')->count();
    $successRate = $totalAttendance > 0
        ? round(($presentAttendance / $totalAttendance) * 100, 0)
        : 95; // Default to 95% if no attendance data

    // Get total credits
    $totalCredits = Course::sum('credits');

    // Get unique semesters count
    $uniqueSemesters = Course::distinct('semester')->count('semester');

    return view('guest.home', [
        'publicCourses' => Course::orderBy('course_code')
            ->take(6)
            ->get(['id', 'course_code', 'title', 'credits', 'semester', 'photo']),
        'publicAnnouncements' => Announcement::query()
            ->currentlyVisible()
            ->visibleToUser(null)
            ->with(['author:id,name'])
            ->orderByDesc('pinned')
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 WHEN 'info' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'user_id', 'title', 'body', 'created_at']),
        'stats' => [
            'totalCourses' => $totalCourses,
            'totalStudents' => $totalStudents,
            'totalFaculty' => $totalFaculty,
            'successRate' => $successRate,
            'totalCredits' => $totalCredits,
            'uniqueSemesters' => $uniqueSemesters,
        ],
    ]);
})->name('guest.home');

Route::get('/guest/courses', function () {
    $courses = Course::orderBy('course_code')->get([
        'id',
        'course_code',
        'title',
        'credits',
        'semester',
        'photo',
    ]);

    // Dynamic Statistics
    $totalCourses = $courses->count();
    $uniqueSemesters = $courses->unique('semester')->count();
    $totalCredits = $courses->sum('credits');
    $averageCredits = $totalCourses > 0 ? round($totalCredits / $totalCourses, 1) : 0;

    // Get total enrollments (approved enrollments)
    $totalEnrollments = DB::table('course_student')
        ->where('status', 'approved')
        ->count();

    // Calculate availability percentage (courses with at least one enrollment)
    $coursesWithEnrollments = DB::table('course_student')
        ->where('status', 'approved')
        ->distinct('course_id')
        ->count('course_id');
    $availabilityRate = $totalCourses > 0
        ? round(($coursesWithEnrollments / $totalCourses) * 100, 0)
        : 100;

    // Get most popular course (by enrollment count)
    $popularCourse = DB::table('course_student')
        ->where('status', 'approved')
        ->select('course_id', DB::raw('count(*) as enrollment_count'))
        ->groupBy('course_id')
        ->orderByDesc('enrollment_count')
        ->first();

    return view('guest.courses', [
        'courses' => $courses,
        'stats' => [
            'totalCourses' => $totalCourses,
            'uniqueSemesters' => $uniqueSemesters,
            'totalCredits' => $totalCredits,
            'averageCredits' => $averageCredits,
            'totalEnrollments' => $totalEnrollments,
            'availabilityRate' => $availabilityRate,
            'coursesWithEnrollments' => $coursesWithEnrollments,
        ],
    ]);
})->name('guest.courses');

Route::get('/guest/courses/{course}', function (Course $course) {
    $course->loadCount(['subjects', 'teachers']);

    $approvedEnrollments = DB::table('course_student')
        ->where('course_id', $course->id)
        ->where('status', 'approved')
        ->count();

    $relatedCourses = Course::query()
        ->whereKeyNot($course->id)
        ->where('semester', $course->semester)
        ->orderBy('course_code')
        ->take(4)
        ->get(['id', 'course_code', 'title', 'credits', 'semester', 'photo']);

    if ($relatedCourses->count() < 4) {
        $needed = 4 - $relatedCourses->count();
        $extra = Course::query()
            ->whereKeyNot($course->id)
            ->whereNotIn('id', $relatedCourses->pluck('id'))
            ->orderBy('course_code')
            ->take($needed)
            ->get(['id', 'course_code', 'title', 'credits', 'semester', 'photo']);
        $relatedCourses = $relatedCourses->concat($extra);
    }

    return view('guest.course-show', [
        'course' => $course,
        'approvedEnrollments' => $approvedEnrollments,
        'relatedCourses' => $relatedCourses,
    ]);
})->whereNumber('course')->name('guest.courses.show');

Route::get('/guest/news', function () {
    return view('guest.news', [
        'announcements' => Announcement::query()
            ->currentlyVisible()
            ->visibleToUser(null)
            ->orderByDesc('pinned')
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 WHEN 'info' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'title',
                'body',
                'priority',
                'pinned',
                'created_at',
            ]),
    ]);
})->name('guest.news');

Route::get('/guest/news/{announcement}', function (int $announcement) {
    $selected = Announcement::query()
        ->currentlyVisible()
        ->visibleToUser(null)
        ->whereKey($announcement)
        ->firstOrFail([
            'id',
            'title',
            'body',
            'priority',
            'pinned',
            'created_at',
        ]);

    $relatedAnnouncements = Announcement::query()
        ->currentlyVisible()
        ->visibleToUser(null)
        ->whereKeyNot($selected->id)
        ->orderByDesc('pinned')
        ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 WHEN 'info' THEN 3 ELSE 4 END")
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get([
            'id',
            'title',
            'body',
            'priority',
            'pinned',
            'created_at',
        ]);

    return view('guest.news-show', [
        'announcement' => $selected,
        'relatedAnnouncements' => $relatedAnnouncements,
    ]);
})->whereNumber('announcement')->name('guest.news.show');

Route::get('/guest/about', function () {
    // Dynamic Statistics
    $totalStudents = Student::count();
    $totalFaculty = User::where('role', 'teacher')->count();
    $totalCourses = Course::count();

    // Calculate years of excellence (from oldest student enrollment or use a base year)
    $oldestEnrollment = Student::whereNotNull('enrollment_date')
        ->orderBy('enrollment_date', 'asc')
        ->value('enrollment_date');

    $yearsOfExcellence = 50; // Default
    if ($oldestEnrollment) {
        $yearsOfExcellence = max(50, now()->year - $oldestEnrollment->year);
    }

    // Get unique programs (based on unique course codes or could be based on student programmes)
    $uniqueProgrammes = Student::distinct('programme')
        ->whereNotNull('programme')
        ->count('programme');
    $totalOfferings = $uniqueProgrammes > 0 ? $uniqueProgrammes : $totalCourses;

    return view('guest.about', [
        'stats' => [
            'yearsOfExcellence' => $yearsOfExcellence,
            'totalStudents' => $totalStudents,
            'totalFaculty' => $totalFaculty,
            'totalOfferings' => $totalOfferings,
        ],
    ]);
})->name('guest.about');
Route::get('/guest/vision', function () {
    // Dynamic Statistics for Vision Page
    $totalUsers = User::count();
    $totalCourses = Course::count();
    $totalEnrollments = DB::table('course_student')
        ->where('status', 'approved')
        ->count();

    return view('guest.vision', [
        'stats' => [
            'totalUsers' => $totalUsers,
            'totalCourses' => $totalCourses,
            'totalEnrollments' => $totalEnrollments,
        ],
    ]);
})->name('guest.vision');

Route::get('/guest/services', function () {
    // Dynamic Statistics for Services Page
    $totalStudents = Student::count();
    $totalCourses = Course::count();
    $approvedEnrollments = DB::table('course_student')
        ->where('status', 'approved')
        ->count();

    // Grade statistics
    $totalGrades = DB::table('grades')->count();

    return view('guest.services', [
        'stats' => [
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'approvedEnrollments' => $approvedEnrollments,
            'totalGrades' => $totalGrades,
        ],
    ]);
})->name('guest.services');

Route::get('/guest/support', function () {
    // Dynamic Statistics for Support Page
    $totalStudents = Student::count();
    $totalUsers = User::count();
    $supportTeamCount = User::query()
        ->whereIn('role', ['staff', 'admin'])
        ->count();

    return view('guest.support', [
        'stats' => [
            'totalStudents' => $totalStudents,
            'totalUsers' => $totalUsers,
            'supportTeamCount' => $supportTeamCount,
        ],
    ]);
})->name('guest.support');

Route::get('/guest/contact', function () {
    // Dynamic Statistics for Contact Page
    $totalStudents = Student::count();
    $totalFaculty = User::where('role', 'teacher')->count();
    $totalCourses = Course::count();

    // Get department/role breakdown
    $adminCount = User::where('role', 'admin')->count();
    $teacherCount = User::where('role', 'teacher')->count();
    $studentCount = Student::count();

    return view('guest.contact', [
        'stats' => [
            'totalStudents' => $totalStudents,
            'totalFaculty' => $totalFaculty,
            'totalCourses' => $totalCourses,
            'adminCount' => $adminCount,
            'teacherCount' => $teacherCount,
            'studentCount' => $studentCount,
        ],
    ]);
})->name('guest.contact');

// Guest contact form submission (stores into contact_messages)
Route::post('/guest/contact', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('guest.contact.store');

Route::view('/guest/policies', 'guest.policies')->name('guest.policies');
Route::view('/guest/feedback', 'guest.feedback')->name('guest.feedback');
Route::view('/guest/user-manuals', 'guest.user-manual')->name('guest.user-manual.page');

// Guest feedback form submission (stores into feedback_messages)
Route::post('/guest/feedback', [FeedbackController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('guest.feedback.store');

// Global search (guest + authenticated): same URL, controller branches by auth
Route::get('/search', SearchController::class)
    ->middleware('throttle:90,1')
    ->name('search');

Route::get('/privacy-policy', function () {
    return Inertia::render('PrivacyPolicy');
})->name('privacy-policy');

Route::get('/terms-and-conditions', function () {
    return Inertia::render('TermsAndConditions');
})->name('terms-and-conditions');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified', 'nocache'])
    ->name('dashboard');

Route::middleware(['auth', 'nocache'])->group(function () {
    // Testing only (local): one-click email verification – visit this URL to verify without opening email
    if (app()->environment('local')) {
        Route::get('/dev/verify-email-now', function () {
            $user = auth()->user();
            if (! $user) {
                return redirect()->route('login');
            }
            if ($user->hasVerifiedEmail()) {
                return redirect()->route('dashboard');
            }
            $url = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            );

            return redirect($url);
        })->name('dev.verify-email-now');
    }

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings (all roles: Staff, Teacher, Student)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::middleware('role:student')->group(function () {
        // Timebox 1: Student Profile (student self-view)
        Route::get('/student/profile', [StudentProfileController::class, 'show'])->name('student.profile.show');
        Route::patch('/student/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');
        Route::delete('/student/profile/photo', [StudentProfileController::class, 'removePhoto'])->name('student.profile.remove-photo');

        // Timebox 1: Courses (with enrollment)
        Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

        // Timebox 1: Course Registration
        Route::post('/courses/{course}/enroll', [CourseRegistrationController::class, 'enroll'])->name('courses.enroll');
        Route::delete('/courses/{course}/unenroll', [CourseRegistrationController::class, 'unenroll'])->name('courses.unenroll');

        // Timebox 1: My Courses (student's enrolled courses)
        Route::get('/my-courses', [MyCoursesController::class, 'index'])->name('my-courses.index');

        // Student Grades (read-only)
        Route::get('/student/grades', [StudentGradesController::class, 'index'])->name('student.grades.index');

        // Student Attendance Report
        Route::get('/student/attendance', [StudentAttendanceController::class, 'index'])->name('student.attendance.index');
        Route::get('/student/attendance/export/{format}', [StudentAttendanceController::class, 'export'])->name('student.attendance.export');

        // Student Assignments
        Route::get('/student/assignments', [StudentAssignmentController::class, 'index'])->name('student.assignments.index');
        Route::get('/student/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('student.assignments.show');
        Route::post('/student/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('student.assignments.submit');
        Route::get('/student/assignments/submissions/{submission}/download', [StudentAssignmentController::class, 'download'])->name('student.assignments.download');

        // Student Fees
        Route::get('/student/fees', [StudentFeeController::class, 'index'])->name('student.fees.index');
        Route::post('/student/fees/{fee}/submit-payment', [StudentFeeController::class, 'submitPayment'])->name('student.fees.submit-payment');

        // Payment Gateway (Stripe)
        Route::post('/payment/{fee}/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
        Route::get('/payment/{fee}/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('/payment/{fee}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

        // Student Timetable (read-only)
        Route::get('/student/timetable', [StudentTimetableController::class, 'index'])->name('student.timetable.index');
        Route::get('/student/timetable/export/{format}', [StudentTimetableController::class, 'export'])->name('student.timetable.export');
    });

    // Announcements (all authenticated users)
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements/{announcement}/read', [AnnouncementController::class, 'markAsRead'])->name('announcements.read');
    Route::post('/announcements/{announcement}/ack', [AnnouncementController::class, 'acknowledge'])->name('announcements.ack');
    // Notifications (all authenticated users)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Messaging (all roles can send)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('messages.store');
    Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead'])
        ->middleware('throttle:120,1')
        ->name('messages.read');

    // Timebox 2: Staff Admin Features (staff only)
    Route::middleware('role:staff')->group(function () {
        // Contact Messages (staff inbox)
        Route::get('/admin/contact-messages', [StaffContactMessageController::class, 'index'])->name('admin.contact-messages.index');
        Route::post('/admin/contact-messages/{contactMessage}/read', [StaffContactMessageController::class, 'markRead'])->name('admin.contact-messages.read');
        Route::post('/admin/contact-messages/{contactMessage}/reply', [StaffContactMessageController::class, 'reply'])->name('admin.contact-messages.reply');

        // Feedback Messages (staff inbox)
        Route::get('/admin/feedback-messages', [StaffFeedbackMessageController::class, 'index'])->name('admin.feedback-messages.index');
        Route::post('/admin/feedback-messages/{feedbackMessage}/read', [StaffFeedbackMessageController::class, 'markRead'])->name('admin.feedback-messages.read');
        Route::post('/admin/feedback-messages/{feedbackMessage}/mark-replied', [StaffFeedbackMessageController::class, 'markReplied'])->name('admin.feedback-messages.mark-replied');

        // Student Management
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::delete('/students/bulk', [StudentController::class, 'bulkDestroy'])->name('students.bulk-destroy');
        Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
        Route::delete('/students/{student}/photo', [StudentController::class, 'removePhoto'])->name('students.remove-photo');

        // Course Management
        Route::resource('admin/courses', StaffCourseController::class)->names([
            'index' => 'admin.courses.index',
            'create' => 'admin.courses.create',
            'store' => 'admin.courses.store',
            'edit' => 'admin.courses.edit',
            'update' => 'admin.courses.update',
            'destroy' => 'admin.courses.destroy',
        ]);
        Route::delete('/admin/courses/{course}/photo', [StaffCourseController::class, 'removePhoto'])->name('admin.courses.remove-photo');

        // Teacher Assignment to Courses (staff only)
        Route::get('/admin/courses/{course}/assign-teachers', [StaffCourseTeacherController::class, 'edit'])->name('admin.courses.assign-teachers');
        Route::put('/admin/courses/{course}/assign-teachers', [StaffCourseTeacherController::class, 'update'])->name('admin.courses.assign-teachers.update');

        // Enrollment Management (staff only)
        Route::get('/admin/enrollments', [StaffEnrollmentController::class, 'index'])->name('admin.enrollments.index');
        Route::get('/admin/enrollments/export/{format}', [StaffEnrollmentController::class, 'export'])->name('admin.enrollments.export');
        Route::post('/admin/enrollments/{enrollment}/approve', [StaffEnrollmentController::class, 'approve'])->name('admin.enrollments.approve');
        Route::post('/admin/enrollments/{enrollment}/reject', [StaffEnrollmentController::class, 'reject'])->name('admin.enrollments.reject');
        Route::post('/admin/enrollments/{enrollment}/approve-withdrawal', [StaffEnrollmentController::class, 'approveWithdrawal'])->name('admin.enrollments.approve-withdrawal');
        Route::post('/admin/enrollments/{enrollment}/reject-withdrawal', [StaffEnrollmentController::class, 'rejectWithdrawal'])->name('admin.enrollments.reject-withdrawal');

        // Subject Management
        Route::resource('admin/subjects', StaffSubjectController::class)->names([
            'index' => 'admin.subjects.index',
            'create' => 'admin.subjects.create',
            'store' => 'admin.subjects.store',
            'edit' => 'admin.subjects.edit',
            'update' => 'admin.subjects.update',
            'destroy' => 'admin.subjects.destroy',
        ]);
        Route::delete('/admin/subjects/{subject}/photo', [StaffSubjectController::class, 'removePhoto'])->name('admin.subjects.remove-photo');

        // Teacher Assignment to Subjects (staff only)
        Route::get('/admin/subjects/{subject}/assign-teachers', [StaffSubjectTeacherController::class, 'edit'])->name('admin.subjects.assign-teachers');
        Route::put('/admin/subjects/{subject}/assign-teachers', [StaffSubjectTeacherController::class, 'update'])->name('admin.subjects.assign-teachers.update');
        Route::post('/admin/subjects/assign-teachers/bulk', [StaffSubjectTeacherController::class, 'bulkAssign'])->name('admin.subjects.assign-teachers.bulk');

        // User Management (role assignment)
        Route::get('/admin/users', [StaffUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [StaffUserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [StaffUserController::class, 'store'])->name('admin.users.store');
        Route::delete('/admin/users/bulk', [StaffUserController::class, 'bulkDestroy'])->name('admin.users.bulk-destroy');
        Route::get('/admin/users/{user}/edit', [StaffUserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [StaffUserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [StaffUserController::class, 'destroy'])->name('admin.users.destroy');
        Route::delete('/admin/users/{user}/photo', [StaffUserController::class, 'removePhoto'])->name('admin.users.remove-photo');

        // Fee Management (staff only)
        Route::resource('admin/fees', StaffFeeController::class)->names([
            'index' => 'admin.fees.index',
            'create' => 'admin.fees.create',
            'store' => 'admin.fees.store',
            'edit' => 'admin.fees.edit',
            'update' => 'admin.fees.update',
            'destroy' => 'admin.fees.destroy',
        ]);
        Route::post('/admin/fees/{fee}/approve-payment', [StaffFeeController::class, 'approvePayment'])->name('admin.fees.approve-payment');
        Route::post('/admin/fees/{fee}/reject-payment', [StaffFeeController::class, 'rejectPayment'])->name('admin.fees.reject-payment');
        Route::post('/admin/fees/{fee}/send-reminder', [StaffFeeController::class, 'sendReminder'])->name('admin.fees.send-reminder');
        Route::post('/admin/fees/send-overdue-reminders', [StaffFeeController::class, 'sendOverdueReminders'])->name('admin.fees.send-overdue-reminders');
        Route::get('/admin/fees/{fee}/receipt', [StaffFeeController::class, 'receipt'])->name('admin.fees.receipt');
        Route::get('/admin/fees/export/{format}', [StaffFeeController::class, 'export'])->name('admin.fees.export');

        // Grades Review & Approval (staff only)
        Route::get('/admin/grades', [StaffGradesController::class, 'index'])->name('admin.grades.index');
        Route::post('/admin/grades/bulk-review', [StaffGradesController::class, 'bulkReview'])->name('admin.grades.bulk-review');
        Route::get('/admin/grades/{subject}/export/{format}', [StaffGradesController::class, 'export'])->name('admin.grades.export');
        Route::get('/admin/grades/{subject}', [StaffGradesController::class, 'show'])->name('admin.grades.show');
        Route::post('/admin/grades/{grade}/approve', [StaffGradesController::class, 'approve'])->name('admin.grades.approve');
        Route::post('/admin/grades/{grade}/reject', [StaffGradesController::class, 'reject'])->name('admin.grades.reject');

        // Timetable Management (staff only)
        Route::resource('admin/timetables', StaffTimetableController::class)->names([
            'index' => 'admin.timetables.index',
            'create' => 'admin.timetables.create',
            'store' => 'admin.timetables.store',
            'edit' => 'admin.timetables.edit',
            'update' => 'admin.timetables.update',
            'destroy' => 'admin.timetables.destroy',
        ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('/admin/timetables/export/{format}', [StaffTimetableController::class, 'export'])->name('admin.timetables.export');

        // Announcements (staff only)
        Route::resource('admin/announcements', StaffAnnouncementController::class)->names([
            'index' => 'admin.announcements.index',
            'create' => 'admin.announcements.create',
            'store' => 'admin.announcements.store',
            'edit' => 'admin.announcements.edit',
            'update' => 'admin.announcements.update',
            'destroy' => 'admin.announcements.destroy',
        ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('/admin/announcements/{announcement}/remind', [StaffAnnouncementController::class, 'sendReminder'])->name('admin.announcements.remind');

        // Attendance Reports (staff only)
        Route::get('/admin/attendance/report', [StaffAttendanceReportController::class, 'index'])->name('admin.attendance.report');
        Route::get('/admin/attendance/report/export/{format}', [StaffAttendanceReportController::class, 'export'])->name('admin.attendance.report.export');

        // Attendance Alerts (staff only)
        Route::post('/admin/attendance/alerts/run', StaffAttendanceAlertsController::class)->name('admin.attendance.alerts.run');

        // Queue Failed Jobs (staff only)
        Route::get('/admin/failed-jobs', [StaffFailedJobController::class, 'index'])->name('admin.failed-jobs.index');
        Route::post('/admin/failed-jobs/retry-all', [StaffFailedJobController::class, 'retryAll'])->name('admin.failed-jobs.retry-all');
        Route::post('/admin/failed-jobs/flush', [StaffFailedJobController::class, 'flush'])->name('admin.failed-jobs.flush');
        Route::post('/admin/failed-jobs/{failedJobId}/retry', [StaffFailedJobController::class, 'retry'])->name('admin.failed-jobs.retry');
        Route::delete('/admin/failed-jobs/{failedJobId}', [StaffFailedJobController::class, 'destroy'])->name('admin.failed-jobs.destroy');
    });

    // Timebox 3: Teacher Features (teacher only)
    Route::middleware('role:teacher')->group(function () {
        Route::get('/teacher/courses', [TeacherCoursesController::class, 'index'])->name('teacher.courses.index');

        // Attendance Management
        Route::get('/teacher/attendance', [TeacherAttendanceController::class, 'index'])->name('teacher.attendance.index');
        Route::get('/teacher/attendance/{subject}', [TeacherAttendanceController::class, 'show'])->name('teacher.attendance.show');
        Route::post('/teacher/attendance/{subject}', [TeacherAttendanceController::class, 'store'])->name('teacher.attendance.store');

        // Grades Management
        Route::get('/teacher/grades', [TeacherGradesController::class, 'index'])->name('teacher.grades.index');
        Route::get('/teacher/grades/{subject}', [TeacherGradesController::class, 'show'])->name('teacher.grades.show');
        Route::post('/teacher/grades/{subject}', [TeacherGradesController::class, 'store'])->name('teacher.grades.store');
        Route::post('/teacher/grades/{subject}/students/{student}/submit-final', [TeacherGradesController::class, 'submitFinalGrade'])->name('teacher.grades.submit-final');

        // Assignment Management
        Route::get('/teacher/assignments', [TeacherAssignmentController::class, 'index'])->name('teacher.assignments.index');
        Route::get('/teacher/assignments/{subject}', [TeacherAssignmentController::class, 'show'])->name('teacher.assignments.show');
        Route::get('/teacher/assignments/{subject}/create', [TeacherAssignmentController::class, 'create'])->name('teacher.assignments.create');
        Route::post('/teacher/assignments/{subject}', [TeacherAssignmentController::class, 'store'])->name('teacher.assignments.store');
        Route::get('/teacher/assignments/{assignment}/edit', [TeacherAssignmentController::class, 'edit'])->name('teacher.assignments.edit');
        Route::put('/teacher/assignments/{assignment}', [TeacherAssignmentController::class, 'update'])->name('teacher.assignments.update');
        Route::post('/teacher/assignments/{assignment}/publish', [TeacherAssignmentController::class, 'publish'])->name('teacher.assignments.publish');
        Route::delete('/teacher/assignments/{assignment}', [TeacherAssignmentController::class, 'destroy'])->name('teacher.assignments.destroy');
        Route::get('/teacher/assignments/{assignment}/submissions', [TeacherAssignmentController::class, 'submissions'])->name('teacher.assignments.submissions');
        Route::get('/teacher/assignments/{assignment}/submissions/download-all', [TeacherAssignmentController::class, 'downloadAllSubmissions'])->name('teacher.assignments.download-all');
        Route::post('/teacher/assignments/submissions/{submission}/grade', [TeacherAssignmentController::class, 'grade'])->name('teacher.assignments.grade');
        Route::get('/teacher/assignments/submissions/{submission}/download', [TeacherAssignmentController::class, 'downloadSubmission'])->name('teacher.assignments.download');

        // Timetable View
        Route::get('/teacher/timetable', [TeacherTimetableController::class, 'index'])->name('teacher.timetable.index');
        Route::get('/teacher/timetable/export/{format}', [TeacherTimetableController::class, 'export'])->name('teacher.timetable.export');

        // Teacher Announcements (manage own announcements)
        Route::resource('teacher/announcements', TeacherAnnouncementController::class)->names([
            'index' => 'teacher.announcements.index',
            'create' => 'teacher.announcements.create',
            'store' => 'teacher.announcements.store',
            'edit' => 'teacher.announcements.edit',
            'update' => 'teacher.announcements.update',
            'destroy' => 'teacher.announcements.destroy',
        ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('/teacher/announcements/{announcement}/remind', [TeacherAnnouncementController::class, 'sendReminder'])->name('teacher.announcements.remind');
    });
});

require __DIR__.'/auth.php';

// Stripe Webhook (must be outside auth middleware)
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');
