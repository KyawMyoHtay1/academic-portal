<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Grade;
use App\Models\Message;
use App\Models\Student;
use App\Models\Subject;
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

        // Courses
        $courses = collect([
            ['course_code' => 'COMP101', 'title' => 'Introduction to Computing', 'credits' => 3, 'semester' => 'Semester 1'],
            ['course_code' => 'COMP102', 'title' => 'Database Systems', 'credits' => 4, 'semester' => 'Semester 1'],
            ['course_code' => 'COMP201', 'title' => 'Web Development', 'credits' => 4, 'semester' => 'Semester 2'],
            ['course_code' => 'COMP202', 'title' => 'Data Structures', 'credits' => 3, 'semester' => 'Semester 2'],
            ['course_code' => 'COMP203', 'title' => 'Software Engineering', 'credits' => 4, 'semester' => 'Semester 2'],
        ])->map(fn ($c) => Course::firstOrCreate(['course_code' => $c['course_code']], $c));

        // Create student profiles linked to users
        $programmes = ['BSc Computing', 'BBA', 'BEng Software'];
        $students = $studentUsers->map(function (User $user, $idx) use ($programmes, $courses) {
            $course = $courses[$idx % $courses->count()] ?? $courses->first();

            return Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'student_no' => sprintf('STU%03d', $idx + 1),
                    'dob' => Carbon::now()->subYears(18 + ($idx % 4))->subMonths($idx)->format('Y-m-d'),
                    'gender' => ['Male', 'Female', 'Other'][$idx % 3],
                    'nationality' => 'Nigerian',
                    'phone' => '555-010'.($idx + 1),
                    'address' => 'Demo Address '.($idx + 1),
                    'emergency_contact_name' => 'Guardian '.($idx + 1),
                    'emergency_contact_phone' => '555-090'.($idx + 1),
                    'programme' => $course?->course_code ?? $programmes[$idx % count($programmes)],
                    'intake_year' => 2024 - ($idx % 2),
                    'previous_institution' => 'Demo Secondary School',
                    'previous_qualification' => 'High School Diploma',
                    'status' => 'active',
                    'notes' => null,
                ]
            );
        });

        // Assign teachers to courses (round-robin)
        $teacherIds = $teacherUsers->pluck('id')->all();
        foreach ($courses as $index => $course) {
            $teacherId = $teacherIds[$index % count($teacherIds)];
            $course->teachers()->syncWithoutDetaching([$teacherId]);
        }

        // Subjects for each course
        $subjectsData = [
            'COMP101' => [
                ['subject_code' => 'COMP101-01', 'title' => 'Computer Fundamentals', 'credits' => 1, 'description' => 'Introduction to computer hardware and software basics.'],
                ['subject_code' => 'COMP101-02', 'title' => 'Operating Systems', 'credits' => 1, 'description' => 'Understanding operating system concepts and functions.'],
                ['subject_code' => 'COMP101-03', 'title' => 'Networking Basics', 'credits' => 1, 'description' => 'Introduction to computer networks and internet protocols.'],
            ],
            'COMP102' => [
                ['subject_code' => 'COMP102-01', 'title' => 'SQL Fundamentals', 'credits' => 2, 'description' => 'Learning SQL queries, joins, and database operations.'],
                ['subject_code' => 'COMP102-02', 'title' => 'Database Design', 'credits' => 1, 'description' => 'ER modeling, normalization, and database schema design.'],
                ['subject_code' => 'COMP102-03', 'title' => 'Database Administration', 'credits' => 1, 'description' => 'Database management, security, and optimization.'],
            ],
            'COMP201' => [
                ['subject_code' => 'COMP201-01', 'title' => 'HTML & CSS', 'credits' => 1, 'description' => 'Web page structure and styling fundamentals.'],
                ['subject_code' => 'COMP201-02', 'title' => 'JavaScript Programming', 'credits' => 2, 'description' => 'Client-side scripting and interactive web development.'],
                ['subject_code' => 'COMP201-03', 'title' => 'Backend Development', 'credits' => 1, 'description' => 'Server-side programming and API development.'],
            ],
            'COMP202' => [
                ['subject_code' => 'COMP202-01', 'title' => 'Arrays and Linked Lists', 'credits' => 1, 'description' => 'Fundamental linear data structures.'],
                ['subject_code' => 'COMP202-02', 'title' => 'Trees and Graphs', 'credits' => 1, 'description' => 'Non-linear data structures and their applications.'],
                ['subject_code' => 'COMP202-03', 'title' => 'Algorithm Analysis', 'credits' => 1, 'description' => 'Time and space complexity, sorting and searching algorithms.'],
            ],
            'COMP203' => [
                ['subject_code' => 'COMP203-01', 'title' => 'Software Design Patterns', 'credits' => 1, 'description' => 'Common design patterns in software development.'],
                ['subject_code' => 'COMP203-02', 'title' => 'Software Testing', 'credits' => 1, 'description' => 'Unit testing, integration testing, and test-driven development.'],
                ['subject_code' => 'COMP203-03', 'title' => 'Project Management', 'credits' => 2, 'description' => 'Agile methodologies, project planning, and team collaboration.'],
            ],
        ];

        foreach ($courses as $course) {
            if (isset($subjectsData[$course->course_code])) {
                foreach ($subjectsData[$course->course_code] as $subjectData) {
                    Subject::firstOrCreate(
                        ['subject_code' => $subjectData['subject_code']],
                        [
                            'course_id' => $course->id,
                            'title' => $subjectData['title'],
                            'credits' => $subjectData['credits'],
                            'description' => $subjectData['description'],
                        ]
                    );
                }
            }
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
            $subject = $course->subjects()->orderBy('subject_code')->first();

            Timetable::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'day_of_week' => $dayMap[$idx % count($dayMap)],
                    'start_time' => '09:00',
                    'end_time' => '11:00',
                ],
                [
                    'subject_id' => $subject?->id,
                    'location' => 'Room '.chr(65 + $idx).'10',
                ]
            );
        }

        // Fees for first 6 students
        foreach ($students->take(6) as $i => $student) {
            Fee::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'description' => 'Tuition '.(2024 + ($i % 2)),
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
            $subjectId = $course->subjects()->orderBy('subject_code')->value('id');

            if ($subjectId === null) {
                continue;
            }

            foreach ($students->take(6) as $i => $student) {
                Grade::updateOrCreate(
                    [
                        'subject_id' => $subjectId,
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
