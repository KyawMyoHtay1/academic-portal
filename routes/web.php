<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\MyCoursesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffCourseController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentProfileController;
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
    });
});

require __DIR__.'/auth.php';
