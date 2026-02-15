<?php

use App\Http\Controllers\CourseController;
use App\Models\Course;
use App\Models\Announcement;
use App\Models\Student;
use App\Models\User;
use App\Models\Attendance;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyCoursesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffCourseController;
use App\Http\Controllers\StaffCourseTeacherController;
use App\Http\Controllers\StaffEnrollmentController;
use App\Http\Controllers\StaffFeeController;
use App\Http\Controllers\StaffGradesController;
use App\Http\Controllers\StaffSubjectController;
use App\Http\Controllers\StaffSubjectTeacherController;
use App\Http\Controllers\StaffTimetableController;
use App\Http\Controllers\StaffUserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentTimetableController;
use App\Http\Controllers\StudentFeeController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentAssignmentController;
use App\Http\Controllers\StudentGradesController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\StaffAnnouncementController;
use App\Http\Controllers\StaffAttendanceAlertsController;
use App\Http\Controllers\StaffAttendanceReportController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherAssignmentController;
use App\Http\Controllers\TeacherCoursesController;
use App\Http\Controllers\TeacherGradesController;
use App\Http\Controllers\TeacherTimetableController;
use App\Http\Controllers\TeacherAnnouncementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\StaffContactMessageController;
use App\Http\Controllers\StaffFeedbackMessageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

// Guest-facing public pages (read-only)
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
            ->get(['id', 'course_code', 'title', 'credits', 'semester']),
        'publicAnnouncements' => Announcement::query()
            ->currentlyVisible()
            ->visibleToUser(null)
            ->orderByDesc('pinned')
            ->orderByRaw("FIELD(priority,'urgent','important','info')")
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'title', 'body', 'created_at']),
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

Route::get('/guest/news', function () {
    return view('guest.news', [
        'announcements' => Announcement::query()
            ->currentlyVisible()
            ->visibleToUser(null)
            ->orderByDesc('pinned')
            ->orderByRaw("FIELD(priority,'urgent','important','info')")
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
    $totalPrograms = max($uniqueProgrammes, $totalCourses); // Use courses as fallback
    
    return view('guest.about', [
        'stats' => [
            'yearsOfExcellence' => $yearsOfExcellence,
            'totalStudents' => $totalStudents,
            'totalFaculty' => $totalFaculty,
            'totalPrograms' => $totalPrograms,
        ],
    ]);
})->name('guest.about');
Route::get('/guest/vision', function () {
    // Dynamic Statistics for Vision Page
    $totalStudents = Student::count();
    $totalFaculty = User::where('role', 'teacher')->count();
    $totalUsers = User::count();
    $totalCourses = Course::count();
    $totalEnrollments = DB::table('course_student')
        ->where('status', 'approved')
        ->count();
    
    return view('guest.vision', [
        'stats' => [
            'totalStudents' => $totalStudents,
            'totalFaculty' => $totalFaculty,
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
    $totalEnrollments = DB::table('course_student')
        ->where('status', 'approved')
        ->count();
    
    // Assignment statistics
    $totalAssignments = DB::table('assignments')->count();
    $totalSubmissions = DB::table('assignment_submissions')->count();
    
    // Grade statistics
    $totalGrades = DB::table('grades')->count();
    
    // Fee statistics
    $totalFees = DB::table('fees')->count();
    $paidFees = DB::table('fees')->where('status', 'paid')->count();
    
    return view('guest.services', [
        'stats' => [
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'totalEnrollments' => $totalEnrollments,
            'totalAssignments' => $totalAssignments,
            'totalSubmissions' => $totalSubmissions,
            'totalGrades' => $totalGrades,
            'totalFees' => $totalFees,
            'paidFees' => $paidFees,
        ],
    ]);
})->name('guest.services');

Route::get('/guest/support', function () {
    // Dynamic Statistics for Support Page
    $totalStudents = Student::count();
    $totalFaculty = User::where('role', 'teacher')->count();
    $totalUsers = User::count();
    $totalAnnouncements = DB::table('announcements')
        ->where('visible_from', '<=', now())
        ->where(function($query) {
            $query->whereNull('visible_until')
                  ->orWhere('visible_until', '>=', now());
        })
        ->count();
    
    return view('guest.support', [
        'stats' => [
            'totalStudents' => $totalStudents,
            'totalFaculty' => $totalFaculty,
            'totalUsers' => $totalUsers,
            'totalAnnouncements' => $totalAnnouncements,
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
Route::post('/guest/contact', [ContactController::class, 'store'])->name('guest.contact.store');

Route::view('/guest/policies', 'guest.policies')->name('guest.policies');
Route::view('/guest/feedback', 'guest.feedback')->name('guest.feedback');

// Guest feedback form submission (stores into feedback_messages)
Route::post('/guest/feedback', [FeedbackController::class, 'store'])->name('guest.feedback.store');

// Global search (guest + authenticated): same URL, controller branches by auth
Route::get('/search', SearchController::class)->name('search');

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
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');

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

        // User Management (role assignment)
        Route::get('/admin/users', [StaffUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [StaffUserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [StaffUserController::class, 'store'])->name('admin.users.store');
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
        Route::get('/admin/fees/{fee}/receipt', [StaffFeeController::class, 'receipt'])->name('admin.fees.receipt');

        // Grades Review & Approval (staff only)
        Route::get('/admin/grades', [StaffGradesController::class, 'index'])->name('admin.grades.index');
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
        ]);

        // Announcements (staff only)
        Route::resource('admin/announcements', StaffAnnouncementController::class)->names([
            'index' => 'admin.announcements.index',
            'create' => 'admin.announcements.create',
            'store' => 'admin.announcements.store',
            'edit' => 'admin.announcements.edit',
            'update' => 'admin.announcements.update',
            'destroy' => 'admin.announcements.destroy',
        ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        // Attendance Reports (staff only)
        Route::get('/admin/attendance/report', [StaffAttendanceReportController::class, 'index'])->name('admin.attendance.report');

        // Attendance Alerts (staff only)
        Route::post('/admin/attendance/alerts/run', StaffAttendanceAlertsController::class)->name('admin.attendance.alerts.run');
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
        Route::post('/teacher/assignments/submissions/{submission}/grade', [TeacherAssignmentController::class, 'grade'])->name('teacher.assignments.grade');
        Route::get('/teacher/assignments/submissions/{submission}/download', [TeacherAssignmentController::class, 'downloadSubmission'])->name('teacher.assignments.download');

        // Timetable View
        Route::get('/teacher/timetable', [TeacherTimetableController::class, 'index'])->name('teacher.timetable.index');

        // Teacher Announcements (manage own announcements)
        Route::resource('teacher/announcements', TeacherAnnouncementController::class)->names([
            'index' => 'teacher.announcements.index',
            'create' => 'teacher.announcements.create',
            'store' => 'teacher.announcements.store',
            'edit' => 'teacher.announcements.edit',
            'update' => 'teacher.announcements.update',
            'destroy' => 'teacher.announcements.destroy',
        ])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });
});

require __DIR__.'/auth.php';

// Stripe Webhook (must be outside auth middleware)
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');
