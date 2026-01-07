<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\MyCoursesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffCourseController;
use App\Http\Controllers\StaffCourseTeacherController;
use App\Http\Controllers\StaffFeeController;
use App\Http\Controllers\StaffUserController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentTimetableController;
use App\Http\Controllers\StudentFeeController;
use App\Http\Controllers\StudentGradesController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherCoursesController;
use App\Http\Controllers\TeacherGradesController;
use App\Http\Controllers\TeacherTimetableController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
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

    // Timebox 2: Staff Admin Features (staff only)
    Route::middleware('role:staff')->group(function () {
        // Student Management
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');

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

        // User Management (role assignment)
        Route::get('/admin/users', [StaffUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/{user}/edit', [StaffUserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [StaffUserController::class, 'update'])->name('admin.users.update');

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
    });

    // Timebox 3: Teacher Features (teacher only)
    Route::middleware('role:teacher')->group(function () {
        Route::get('/teacher/courses', [TeacherCoursesController::class, 'index'])->name('teacher.courses.index');
        
        // Attendance Management
        Route::get('/teacher/attendance', [TeacherAttendanceController::class, 'index'])->name('teacher.attendance.index');
        Route::get('/teacher/attendance/{course}', [TeacherAttendanceController::class, 'show'])->name('teacher.attendance.show');
        Route::post('/teacher/attendance/{course}', [TeacherAttendanceController::class, 'store'])->name('teacher.attendance.store');

        // Grades Management
        Route::get('/teacher/grades', [TeacherGradesController::class, 'index'])->name('teacher.grades.index');
        Route::get('/teacher/grades/{course}', [TeacherGradesController::class, 'show'])->name('teacher.grades.show');
        Route::post('/teacher/grades/{course}', [TeacherGradesController::class, 'store'])->name('teacher.grades.store');

        // Timetable View
        Route::get('/teacher/timetable', [TeacherTimetableController::class, 'index'])->name('teacher.timetable.index');
    });
});

require __DIR__.'/auth.php';
