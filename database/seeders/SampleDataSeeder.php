<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Message;
use App\Models\Student;
use App\Models\Timetable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
    * Seed a realistic demo dataset: staff, teachers, students, courses,
    * enrollments, timetables, grades, fees, attendance, announcements, messages.
    */
    public function run(): void
    {
        // Staff users
        $staffUsers = collect([
            ['name' => 'Alice Admin', 'email' => 'alice.admin@example.com'],
            ['name' => 'Bob Admin', 'email' => 'bob.admin@example.com'],
        ])->map(function ($data) {
            return User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('Password123!'),
                    'role' => 'staff',
                ]
            );
        });

        // Teacher users
        $teacherUsers = collect([
            ['name' => 'Dr. Carol Teacher', 'email' => 'carol.teacher@example.com'],
            ['name' => 'Dr. Dan Lecturer', 'email' => 'dan.lecturer@example.com'],
        ])->map(function ($data) {
            return User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('Password123!'),
                    'role' => 'teacher',
                ]
            );
        });

        // 12 student users
        $studentUsers = collect(range(1, 12))->map(function ($i) {
            return User::firstOrCreate(
                ['email' => sprintf('student%02d@example.com', $i)],
                [
                    'name' => sprintf('Student %02d', $i),
                    'password' => Hash::make('Password123!'),
                    'role' => 'student',
                ]
            );
        });

        // Create student profiles linked to users
        $programmes = ['BSc Computing', 'BBA', 'BEng Software'];
        $students = $studentUsers->map(function (User $user, $idx) use ($programmes) {
            return Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'student_no' => sprintf('STU%03d', $idx + 1),
                    'full_name' => $user->name,
                    'dob' => Carbon::now()->subYears(18 + ($idx % 4))->subMonths($idx)->format('Y-m-d'),
                    'email' => $user->email,
                    'phone' => '555-010' . ($idx + 1),
                    'programme' => $programmes[$idx % count($programmes)],
                    'intake_year' => 2024 - ($idx % 2),
                ]
            );
        });

        // Courses
        $courses = collect([
            ['course_code' => 'COMP101', 'title' => 'Introduction to Computing', 'credits' => 3, 'semester' => 'Semester 1'],
            ['course_code' => 'COMP102', 'title' => 'Database Systems', 'credits' => 4, 'semester' => 'Semester 1'],
            ['course_code' => 'COMP201', 'title' => 'Web Development', 'credits' => 4, 'semester' => 'Semester 2'],
            ['course_code' => 'COMP202', 'title' => 'Data Structures', 'credits' => 3, 'semester' => 'Semester 2'],
            ['course_code' => 'COMP203', 'title' => 'Software Engineering', 'credits' => 4, 'semester' => 'Semester 2'],
        ])->map(fn ($c) => Course::firstOrCreate(['course_code' => $c['course_code']], $c));

        // Assign teachers to courses (round-robin)
        $teacherIds = $teacherUsers->pluck('id')->all();
        foreach ($courses as $index => $course) {
            $teacherId = $teacherIds[$index % count($teacherIds)];
            $course->teachers()->syncWithoutDetaching([$teacherId]);
        }

        // Enroll students to courses (simple distribution)
        $courseIds = $courses->pluck('id')->all();
        foreach ($students as $idx => $student) {
            // each student in 3 courses
            $enroll = [
                $courseIds[$idx % count($courseIds)],
                $courseIds[($idx + 1) % count($courseIds)],
                $courseIds[($idx + 2) % count($courseIds)],
            ];
            $student->courses()->syncWithoutDetaching($enroll);
        }

        // Timetables (one per course)
        $dayMap = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        foreach ($courses as $idx => $course) {
            Timetable::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'day_of_week' => $dayMap[$idx % count($dayMap)],
                    'start_time' => '09:00',
                    'end_time' => '11:00',
                ],
                [
                    'location' => 'Room ' . chr(65 + $idx) . '10',
                ]
            );
        }

        // Fees for first 6 students
        foreach ($students->take(6) as $i => $student) {
            Fee::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'description' => 'Tuition ' . (2024 + ($i % 2)),
                    'due_date' => Carbon::now()->addDays(30 + $i)->format('Y-m-d'),
                    'amount' => 1500 + ($i * 100),
                ],
                [
                    'status' => $i % 2 === 0 ? 'paid' : 'pending',
                    'paid_date' => $i % 2 === 0 ? Carbon::now()->addDays(5 + $i)->format('Y-m-d') : null,
                ]
            );
        }

        // Grades for a subset
        foreach ($courses as $course) {
            foreach ($students->take(6) as $i => $student) {
                Grade::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'graded_by' => $teacherIds[$course->id % count($teacherIds)] ?? null,
                        'score' => 65 + ($i * 3),
                    ]
                );
            }
        }

        // Attendance for today for first course
        $today = Carbon::now()->format('Y-m-d');
        $firstCourse = $courses->first();
        foreach ($students->take(8) as $i => $student) {
            Attendance::updateOrCreate(
                [
                    'course_id' => $firstCourse->id,
                    'student_id' => $student->id,
                    'date' => $today,
                ],
                [
                    'status' => $i % 5 === 0 ? 'absent' : 'present',
                ]
            );
        }

        // Announcements
        $staffAuthor = $staffUsers->first();
        Announcement::firstOrCreate(
            ['title' => 'Welcome to the University Academic Portal'],
            [
                'body' => 'This is a seeded announcement to demonstrate the portal UI.',
                'user_id' => $staffAuthor?->id,
            ]
        );
        Announcement::firstOrCreate(
            ['title' => 'Exam Schedule Reminder'],
            [
                'body' => 'Mid-term exams start next month. Check your timetable for details.',
                'user_id' => $staffAuthor?->id,
            ]
        );

        // Messages: staff to first two students
        $recipientUsers = $studentUsers->take(2);
        foreach ($recipientUsers as $i => $receiver) {
            Message::create([
                'sender_id' => $staffAuthor?->id ?? $staffUsers->first()->id,
                'sender_role' => $staffAuthor?->role ?? $staffUsers->first()->role,
                'receiver_id' => $receiver->id,
                'receiver_role' => $receiver->role,
                'body' => $i === 0
                    ? 'Welcome to the portal. Please review your timetable.'
                    : 'Your fee status has been updated. Check the Fees page.',
            ]);
        }
    }
}


