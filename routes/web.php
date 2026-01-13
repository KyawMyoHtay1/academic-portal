<?php

use App\Http\Controllers\CourseController;
use App\Models\Course;
use App\Models\Announcement;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyCoursesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffCourseController;
use App\Http\Controllers\StaffCourseTeacherController;
use App\Http\Controllers\StaffFeeController;
use App\Http\Controllers\StaffSubjectController;
use App\Http\Controllers\StaffTimetableController;
use App\Http\Controllers\StaffUserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentTimetableController;
use App\Http\Controllers\StudentFeeController;
use App\Http\Controllers\StudentGradesController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\StaffAnnouncementController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherCoursesController;
use App\Http\Controllers\TeacherGradesController;
use App\Http\Controllers\TeacherTimetableController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Guest-facing public pages (read-only)
Route::get('/', function () {
    return view('guest.home', [
        'publicCourses' => Course::orderBy('course_code')
            ->take(6)
            ->get(['id', 'course_code', 'title', 'credits', 'semester']),
        'publicAnnouncements' => Announcement::orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'title', 'body', 'created_at']),
    ]);
})->name('guest.home');

Route::get('/guest/courses', function () {
    return view('guest.courses', [
        'courses' => Course::orderBy('course_code')->get([
            'id',
            'course_code',
            'title',
            'credits',
            'semester',
        ]),
    ]);
})->name('guest.courses');

Route::get('/guest/news', function () {
    return view('guest.news', [
        'announcements' => Announcement::orderBy('created_at', 'desc')->get([
            'id',
            'title',
            'body',
            'created_at',
        ]),
    ]);
})->name('guest.news');

Route::view('/guest/about', 'guest.about')->name('guest.about');
Route::view('/guest/contact', 'guest.contact')->name('guest.contact');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified', 'nocache'])
    ->name('dashboard');

Route::middleware(['auth', 'nocache'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Timebox 1: Student Profile (student self-view)
    Route::get('/student/profile', [StudentProfileController::class, 'show'])->name('student.profile.show');
    Route::patch('/student/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');

    // Timebox 1: Courses (with enrollment)
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

    // Timebox 1: Course Registration
    Route::post('/courses/{course}/enroll', [CourseRegistrationController::class, 'enroll'])->name('courses.enroll');
    Route::delete('/courses/{course}/unenroll', [CourseRegistrationController::class, 'unenroll'])->name('courses.unenroll');

    // Timebox 1: My Courses (student's enrolled courses)
    Route::get('/my-courses', [MyCoursesController::class, 'index'])->name('my-courses.index');

    // Student Grades (read-only)
    Route::get('/student/grades', [StudentGradesController::class, 'index'])->name('student.grades.index');

    // Student Fees (view only; payment status managed by staff)
    Route::get('/student/fees', [StudentFeeController::class, 'index'])->name('student.fees.index');
    // Student Timetable (read-only)
    Route::get('/student/timetable', [StudentTimetableController::class, 'index'])->name('student.timetable.index');

    // Announcements (all authenticated users)
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
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
        // Student Management
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

        // Course Management
        Route::resource('admin/courses', StaffCourseController::class)->names([
            'index' => 'admin.courses.index',
            'create' => 'admin.courses.create',
            'store' => 'admin.courses.store',
            'edit' => 'admin.courses.edit',
            'update' => 'admin.courses.update',
            'destroy' => 'admin.courses.destroy',
        ]);

        // Teacher Assignment to Courses (staff only)
        Route::get('/admin/courses/{course}/assign-teachers', [StaffCourseTeacherController::class, 'edit'])->name('admin.courses.assign-teachers');
        Route::put('/admin/courses/{course}/assign-teachers', [StaffCourseTeacherController::class, 'update'])->name('admin.courses.assign-teachers.update');

        // Subject Management
        Route::resource('admin/subjects', StaffSubjectController::class)->names([
            'index' => 'admin.subjects.index',
            'create' => 'admin.subjects.create',
            'store' => 'admin.subjects.store',
            'edit' => 'admin.subjects.edit',
            'update' => 'admin.subjects.update',
            'destroy' => 'admin.subjects.destroy',
        ]);

        // User Management (role assignment)
        Route::get('/admin/users', [StaffUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [StaffUserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [StaffUserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [StaffUserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [StaffUserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [StaffUserController::class, 'destroy'])->name('admin.users.destroy');

        // Fee Management (staff only)
        Route::resource('admin/fees', StaffFeeController::class)->names([
            'index' => 'admin.fees.index',
            'create' => 'admin.fees.create',
            'store' => 'admin.fees.store',
            'edit' => 'admin.fees.edit',
            'update' => 'admin.fees.update',
            'destroy' => 'admin.fees.destroy',
        ]);

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

        // Timetable View
        Route::get('/teacher/timetable', [TeacherTimetableController::class, 'index'])->name('teacher.timetable.index');
    });
});

require __DIR__.'/auth.php';
