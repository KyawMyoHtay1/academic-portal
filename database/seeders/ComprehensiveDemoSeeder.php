<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Fee;
use App\Models\FeedbackMessage;
use App\Models\Grade;
use App\Models\GradeReviewLog;
use App\Models\LowAttendanceAlertState;
use App\Models\Message;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ComprehensiveDemoSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'Password123!';

    /**
     * Seed a broad demo dataset for end-to-end feature testing.
     */
    public function run(): void
    {
        [$staffUsers, $teacherUsers, $studentUsers] = $this->seedUsers();

        $courses = $this->seedCourses();
        $subjectsByCourseId = $this->seedSubjects($courses);

        $this->seedTeachingAssignments($courses, $subjectsByCourseId, $teacherUsers);

        $students = $this->seedStudents($studentUsers);
        $this->seedEnrollments($students, $courses);

        $this->seedTimetables($subjectsByCourseId, $staffUsers);
        $this->seedAttendance($courses, $subjectsByCourseId);
        $this->seedLowAttendanceStates();

        $assignments = $this->seedAssignments($courses, $subjectsByCourseId, $teacherUsers);
        $this->seedAssignmentSubmissions($assignments, $teacherUsers);

        $this->seedFinalGradesAndReviewLogs($subjectsByCourseId, $teacherUsers, $staffUsers);
        $this->seedStudentGradeDetailsBreakdownSample($teacherUsers, $staffUsers);
        $this->seedFees($students, $staffUsers);

        $announcements = $this->seedAnnouncements($staffUsers);
        $this->seedAnnouncementReads($announcements, $staffUsers, $teacherUsers, $studentUsers);

        $this->seedMessages($staffUsers, $teacherUsers, $studentUsers);
        $this->seedPublicInboxSamples();

        if ($this->command) {
            $this->command->info('Comprehensive demo data seeded successfully.');
            $this->command->line('Password for all seeded users: '.self::DEMO_PASSWORD);
            $this->command->line('Staff: alice.staff@example.com, brian.staff@example.com');
            $this->command->line('Teacher: amelia.teacher@example.com, ben.teacher@example.com, chloe.teacher@example.com');
            $this->command->line('Students: student01@example.com ... student18@example.com');
        }
    }

    /**
     * @return array{0: Collection<int, User>, 1: Collection<int, User>, 2: Collection<int, User>}
     */
    private function seedUsers(): array
    {
        $staffUsers = collect([
            ['name' => 'Alice Staff', 'email' => 'alice.staff@example.com'],
            ['name' => 'Brian Staff', 'email' => 'brian.staff@example.com'],
        ])->map(fn (array $data) => $this->upsertUser($data['name'], $data['email'], 'staff'));

        $teacherUsers = collect([
            ['name' => 'Dr. Amelia Teacher', 'email' => 'amelia.teacher@example.com'],
            ['name' => 'Dr. Ben Teacher', 'email' => 'ben.teacher@example.com'],
            ['name' => 'Dr. Chloe Teacher', 'email' => 'chloe.teacher@example.com'],
        ])->map(fn (array $data) => $this->upsertUser($data['name'], $data['email'], 'teacher'));

        $studentUsers = collect(range(1, 18))->map(function (int $i) {
            return $this->upsertUser(
                sprintf('Student %02d', $i),
                sprintf('student%02d@example.com', $i),
                'student'
            );
        });

        return [$staffUsers, $teacherUsers, $studentUsers];
    }

    private function upsertUser(string $name, string $email, string $role): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::DEMO_PASSWORD),
                'role' => $role,
            ]
        );

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return $user->fresh();
    }

    /**
     * @return Collection<int, Course>
     */
    private function seedCourses(): Collection
    {
        return collect([
            ['course_code' => 'CSE101', 'title' => 'Introduction to Computing', 'credits' => 20, 'semester' => 'Semester 1'],
            ['course_code' => 'CSE102', 'title' => 'Database Systems', 'credits' => 20, 'semester' => 'Semester 1'],
            ['course_code' => 'CSE201', 'title' => 'Web Engineering', 'credits' => 20, 'semester' => 'Semester 2'],
            ['course_code' => 'CSE202', 'title' => 'Data Structures and Algorithms', 'credits' => 20, 'semester' => 'Semester 2'],
            ['course_code' => 'CSE301', 'title' => 'Software Engineering', 'credits' => 20, 'semester' => 'Semester 3'],
        ])->map(function (array $courseData) {
            return Course::updateOrCreate(
                ['course_code' => $courseData['course_code']],
                [
                    'title' => $courseData['title'],
                    'credits' => $courseData['credits'],
                    'semester' => $courseData['semester'],
                ]
            );
        })->values();
    }

    /**
     * @param  Collection<int, Course>  $courses
     * @return Collection<int, Collection<int, Subject>>
     */
    private function seedSubjects(Collection $courses): Collection
    {
        $subjectsTemplate = [
            ['suffix' => '01', 'title' => 'Fundamentals', 'credits' => 6],
            ['suffix' => '02', 'title' => 'Applied Practice', 'credits' => 7],
            ['suffix' => '03', 'title' => 'Assessment and Project', 'credits' => 7],
        ];

        $subjects = collect();

        foreach ($courses as $course) {
            foreach ($subjectsTemplate as $template) {
                $subjectCode = sprintf('%s-%s', $course->course_code, $template['suffix']);

                $subject = Subject::updateOrCreate(
                    ['subject_code' => $subjectCode],
                    [
                        'course_id' => $course->id,
                        'title' => $course->title.' '.$template['title'],
                        'credits' => $template['credits'],
                        'description' => 'Seeded subject for demo/testing purposes.',
                    ]
                );

                $subjects->push($subject);
            }
        }

        return $subjects->groupBy('course_id');
    }

    /**
     * @param  Collection<int, Course>  $courses
     * @param  Collection<int, Collection<int, Subject>>  $subjectsByCourseId
     * @param  Collection<int, User>  $teacherUsers
     */
    private function seedTeachingAssignments(Collection $courses, Collection $subjectsByCourseId, Collection $teacherUsers): void
    {
        $teacherIds = $teacherUsers->pluck('id')->values();
        $teacherCount = $teacherIds->count();
        $courseOrderById = $courses->values()->pluck('id')->flip();

        foreach ($courses->values() as $courseIndex => $course) {
            $primaryTeacherId = $teacherIds[$courseIndex % $teacherCount];
            $secondaryTeacherId = $teacherIds[($courseIndex + 1) % $teacherCount];

            $course->teachers()->syncWithoutDetaching([$primaryTeacherId, $secondaryTeacherId]);

            $subjects = $subjectsByCourseId->get($course->id, collect());
            foreach ($subjects->values() as $subjectIndex => $subject) {
                $baseIndex = (int) ($courseOrderById->get($course->id) ?? 0);
                $subjectTeacherId = $teacherIds[($baseIndex + $subjectIndex) % $teacherCount];
                $subject->teachers()->syncWithoutDetaching([$subjectTeacherId]);

                if ($subjectIndex % 2 === 0) {
                    $subject->teachers()->syncWithoutDetaching([$teacherIds[($baseIndex + $subjectIndex + 1) % $teacherCount]]);
                }
            }
        }
    }

    /**
     * @param  Collection<int, User>  $studentUsers
     * @return Collection<int, Student>
     */
    private function seedStudents(Collection $studentUsers): Collection
    {
        $programmes = ['BSc Computer Science', 'BSc Software Engineering', 'BSc Information Systems'];
        $nationalities = ['Kenyan', 'Nigerian', 'Ghanaian', 'Ugandan'];

        return $studentUsers->values()->map(function (User $user, int $index) use ($programmes, $nationalities) {
            return Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'student_no' => sprintf('DEMO%05d', $user->id),
                    'full_name' => $user->name,
                    'dob' => Carbon::now()->subYears(18 + ($index % 4))->subDays($index)->toDateString(),
                    'gender' => ['Male', 'Female', 'Other'][$index % 3],
                    'nationality' => $nationalities[$index % count($nationalities)],
                    'email' => $user->email,
                    'phone' => sprintf('+254700%04d', 1000 + $index),
                    'address' => sprintf('Demo Street %d, Nairobi', $index + 1),
                    'emergency_contact_name' => sprintf('Guardian %02d', $index + 1),
                    'emergency_contact_phone' => sprintf('+254711%04d', 2000 + $index),
                    'programme' => $programmes[$index % count($programmes)],
                    'intake_year' => (string) (2024 - ($index % 2)),
                    'previous_institution' => 'Demo High School',
                    'previous_qualification' => 'High School Diploma',
                    'status' => $index % 10 === 0 ? 'inactive' : 'active',
                    'notes' => null,
                    'enrollment_date' => Carbon::now()->subMonths(3 + $index)->toDateString(),
                ]
            );
        })->values();
    }

    /**
     * @param  Collection<int, Student>  $students
     * @param  Collection<int, Course>  $courses
     */
    private function seedEnrollments(Collection $students, Collection $courses): void
    {
        $courseIds = $courses->pluck('id')->values();
        $courseCount = $courseIds->count();

        foreach ($students->values() as $index => $student) {
            $primaryCourseId = $courseIds[$index % $courseCount];
            $this->upsertEnrollment($student->id, $primaryCourseId, 'approved', Carbon::now()->subDays(40 - ($index % 10)));

            $secondaryCourseId = $courseIds[($index + 1) % $courseCount];
            $secondaryStatus = match (true) {
                $index % 8 === 0 => 'withdrawal_pending',
                $index % 6 === 0 => 'pending',
                $index % 11 === 0 => 'rejected',
                default => 'approved',
            };
            $this->upsertEnrollment($student->id, $secondaryCourseId, $secondaryStatus, Carbon::now()->subDays(25 - ($index % 7)));

            if ($index % 3 === 0) {
                $thirdCourseId = $courseIds[($index + 2) % $courseCount];
                $thirdStatus = $index % 2 === 0 ? 'pending' : 'approved';
                $this->upsertEnrollment($student->id, $thirdCourseId, $thirdStatus, Carbon::now()->subDays(14 - ($index % 5)));
            }
        }
    }

    private function upsertEnrollment(int $studentId, int $courseId, string $status, Carbon $at): void
    {
        DB::table('course_student')->updateOrInsert(
            [
                'student_id' => $studentId,
                'course_id' => $courseId,
            ],
            [
                'status' => $status,
                'created_at' => $at,
                'updated_at' => $at,
            ]
        );
    }

    /**
     * @param  Collection<int, Collection<int, Subject>>  $subjectsByCourseId
     * @param  Collection<int, User>  $staffUsers
     */
    private function seedTimetables(Collection $subjectsByCourseId, Collection $staffUsers): void
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $slots = [
            ['start' => '08:00:00', 'end' => '10:00:00'],
            ['start' => '10:00:00', 'end' => '12:00:00'],
            ['start' => '13:00:00', 'end' => '15:00:00'],
            ['start' => '15:00:00', 'end' => '17:00:00'],
        ];

        $allSubjects = $subjectsByCourseId->flatten()->values();
        foreach ($allSubjects as $index => $subject) {
            $day = $days[$index % count($days)];
            $slot = $slots[$index % count($slots)];
            $creatorId = $staffUsers[$index % $staffUsers->count()]->id;

            Timetable::updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'day_of_week' => $day,
                    'start_time' => $slot['start'],
                ],
                [
                    'end_time' => $slot['end'],
                    'location' => sprintf('Block %s - Room %d', chr(65 + ($index % 5)), 100 + $index),
                    'created_by' => $creatorId,
                ]
            );

            if ($index % 2 === 0) {
                Timetable::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'day_of_week' => $days[($index + 2) % count($days)],
                        'start_time' => '17:00:00',
                    ],
                    [
                        'end_time' => '18:30:00',
                        'location' => sprintf('Lab %d', 1 + ($index % 3)),
                        'created_by' => $creatorId,
                    ]
                );
            }
        }
    }

    /**
     * @param  Collection<int, Course>  $courses
     * @param  Collection<int, Collection<int, Subject>>  $subjectsByCourseId
     */
    private function seedAttendance(Collection $courses, Collection $subjectsByCourseId): void
    {
        foreach ($courses as $course) {
            $subject = $subjectsByCourseId->get($course->id, collect())->first();
            if (! $subject) {
                continue;
            }

            $studentIds = DB::table('course_student')
                ->where('course_id', $course->id)
                ->whereIn('status', ['approved', 'withdrawal_pending'])
                ->orderBy('student_id')
                ->limit(10)
                ->pluck('student_id');

            foreach ($studentIds as $studentId) {
                for ($offset = 0; $offset < 10; $offset++) {
                    $date = Carbon::today()->subDays(9 - $offset)->toDateString();

                    $status = (($studentId + $offset + $course->id) % 7 === 0) ? 'absent' : 'present';
                    if ($studentId % 5 === 0 && $offset % 2 === 0) {
                        $status = 'absent';
                    }

                    Attendance::updateOrCreate(
                        [
                            'subject_id' => $subject->id,
                            'student_id' => $studentId,
                            'date' => $date,
                        ],
                        [
                            'status' => $status,
                        ]
                    );
                }
            }
        }
    }

    private function seedLowAttendanceStates(): void
    {
        $rows = DB::table('attendances')
            ->selectRaw("student_id, COUNT(*) as total, SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present")
            ->groupBy('student_id')
            ->get();

        foreach ($rows as $row) {
            $total = (int) ($row->total ?? 0);
            $present = (int) ($row->present ?? 0);
            $rate = $total > 0 ? round(($present / $total) * 100, 2) : null;

            if ($rate === null) {
                continue;
            }

            $isBelow = $rate < 75;

            LowAttendanceAlertState::updateOrCreate(
                ['student_id' => (int) $row->student_id],
                [
                    'last_rate' => $rate,
                    'is_below_threshold' => $isBelow,
                    'last_alert_sent_at' => $isBelow ? Carbon::now()->subDays(2) : null,
                ]
            );
        }
    }

    /**
     * @param  Collection<int, Course>  $courses
     * @param  Collection<int, Collection<int, Subject>>  $subjectsByCourseId
     * @param  Collection<int, User>  $teacherUsers
     * @return Collection<int, Assignment>
     */
    private function seedAssignments(Collection $courses, Collection $subjectsByCourseId, Collection $teacherUsers): Collection
    {
        $assignments = collect();

        foreach ($courses as $course) {
            $subjects = $subjectsByCourseId->get($course->id, collect())->take(2)->values();
            foreach ($subjects as $subjectIndex => $subject) {
                $teacherId = $this->resolveTeacherIdForSubject($subject->id, $teacherUsers);

                $published = Assignment::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'title' => 'Weekly Quiz - '.$subject->subject_code,
                    ],
                    [
                        'course_id' => $course->id,
                        'created_by' => $teacherId,
                        'description' => 'Weekly assessment quiz.',
                        'due_date' => Carbon::now()->addDays(7 + $subjectIndex)->toDateString(),
                        'due_time' => '23:59:00',
                        'max_score' => 100,
                        'status' => Assignment::STATUS_PUBLISHED,
                        'allowed_file_types' => ['pdf', 'docx', 'txt'],
                        'max_file_size' => 5120,
                    ]
                );
                $assignments->push($published);

                $closed = Assignment::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'title' => 'Mini Project - '.$subject->subject_code,
                    ],
                    [
                        'course_id' => $course->id,
                        'created_by' => $teacherId,
                        'description' => 'Practical mini project.',
                        'due_date' => Carbon::now()->subDays(5 + $subjectIndex)->toDateString(),
                        'due_time' => '18:00:00',
                        'max_score' => 50,
                        'status' => Assignment::STATUS_CLOSED,
                        'allowed_file_types' => ['zip', 'pdf', 'docx'],
                        'max_file_size' => 10240,
                    ]
                );
                $assignments->push($closed);

                if ($subjectIndex === 0) {
                    $draft = Assignment::updateOrCreate(
                        [
                            'subject_id' => $subject->id,
                            'title' => 'Draft Final Report - '.$subject->subject_code,
                        ],
                        [
                            'course_id' => $course->id,
                            'created_by' => $teacherId,
                            'description' => 'Draft assignment not yet published.',
                            'due_date' => Carbon::now()->addDays(25)->toDateString(),
                            'due_time' => '23:59:00',
                            'max_score' => 100,
                            'status' => Assignment::STATUS_DRAFT,
                            'allowed_file_types' => ['pdf', 'docx'],
                            'max_file_size' => 5120,
                        ]
                    );
                    $assignments->push($draft);
                }
            }
        }

        return $assignments->values();
    }

    /**
     * @param  Collection<int, Assignment>  $assignments
     * @param  Collection<int, User>  $teacherUsers
     */
    private function seedAssignmentSubmissions(Collection $assignments, Collection $teacherUsers): void
    {
        foreach ($assignments as $assignment) {
            if ($assignment->status === Assignment::STATUS_DRAFT) {
                continue;
            }

            $studentIds = DB::table('course_student')
                ->where('course_id', $assignment->course_id)
                ->whereIn('status', ['approved', 'withdrawal_pending'])
                ->orderBy('student_id')
                ->limit(10)
                ->pluck('student_id')
                ->values();

            $teacherId = $this->resolveTeacherIdForSubject((int) $assignment->subject_id, $teacherUsers);

            foreach ($studentIds as $index => $studentId) {
                if ($index % 4 === 0) {
                    continue;
                }

                $filePath = sprintf('assignments/seed/assignment-%d-student-%d.txt', $assignment->id, $studentId);
                Storage::disk('public')->put(
                    $filePath,
                    sprintf('Seeded submission for assignment %d by student %d', $assignment->id, $studentId)
                );

                $status = AssignmentSubmission::STATUS_SUBMITTED;
                $score = null;
                $feedback = null;
                $gradedAt = null;
                $gradedBy = null;

                if ($index % 4 === 2) {
                    $status = AssignmentSubmission::STATUS_GRADED;
                    $score = round($assignment->max_score * 0.82, 2);
                    $feedback = 'Good work. Improve code structure and comments.';
                    $gradedAt = Carbon::now()->subDays(2);
                    $gradedBy = $teacherId;
                } elseif ($index % 4 === 3) {
                    $status = AssignmentSubmission::STATUS_RETURNED;
                    $score = round($assignment->max_score * 0.64, 2);
                    $feedback = 'Returned with revision notes. Please improve your analysis section.';
                    $gradedAt = Carbon::now()->subDay();
                    $gradedBy = $teacherId;
                }

                AssignmentSubmission::updateOrCreate(
                    [
                        'assignment_id' => $assignment->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'file_path' => $filePath,
                        'original_filename' => basename($filePath),
                        'comments' => 'Seeded by demo seeder for testing.',
                        'score' => $score,
                        'feedback' => $feedback,
                        'graded_by' => $gradedBy,
                        'graded_at' => $gradedAt,
                        'status' => $status,
                    ]
                );
            }
        }
    }

    /**
     * @param  Collection<int, Collection<int, Subject>>  $subjectsByCourseId
     * @param  Collection<int, User>  $teacherUsers
     * @param  Collection<int, User>  $staffUsers
     */
    private function seedFinalGradesAndReviewLogs(Collection $subjectsByCourseId, Collection $teacherUsers, Collection $staffUsers): void
    {
        $statusPattern = [
            Grade::STATUS_PENDING,
            Grade::STATUS_APPROVED,
            Grade::STATUS_REJECTED,
            Grade::STATUS_APPROVED,
            Grade::STATUS_PENDING,
            Grade::STATUS_APPROVED,
        ];

        $scorePattern = [92.5, 81.0, 73.5, 64.0, 49.5, 36.0, 0.0];

        foreach ($subjectsByCourseId->flatten() as $subject) {
            $studentIds = DB::table('course_student')
                ->where('course_id', $subject->course_id)
                ->whereIn('status', ['approved', 'withdrawal_pending'])
                ->orderBy('student_id')
                ->limit(6)
                ->pluck('student_id')
                ->values();

            $teacherId = $this->resolveTeacherIdForSubject((int) $subject->id, $teacherUsers);

            foreach ($studentIds as $index => $studentId) {
                $status = $statusPattern[$index % count($statusPattern)];
                $score = $scorePattern[$index % count($scorePattern)];

                $reviewedBy = null;
                $reviewedAt = null;
                $rejectionReason = null;

                if ($status === Grade::STATUS_APPROVED) {
                    $reviewedBy = $staffUsers[0]->id;
                    $reviewedAt = Carbon::now()->subDays(1);
                }

                if ($status === Grade::STATUS_REJECTED) {
                    $reviewedBy = $staffUsers[1]->id;
                    $reviewedAt = Carbon::now()->subHours(20);
                    $rejectionReason = 'Please align with rubric criteria before re-submitting.';
                }

                $grade = Grade::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'course_id' => $subject->course_id,
                        'graded_by' => $teacherId,
                        'score' => $score,
                        'status' => $status,
                        'reviewed_by' => $reviewedBy,
                        'reviewed_at' => $reviewedAt,
                        'rejection_reason' => $rejectionReason,
                    ]
                );

                GradeReviewLog::firstOrCreate(
                    [
                        'grade_id' => $grade->id,
                        'performed_by' => $teacherId,
                        'action' => 'submitted',
                    ],
                    [
                        'meta' => [
                            'seeded' => true,
                            'subject_id' => $subject->id,
                            'course_id' => $subject->course_id,
                        ],
                    ]
                );

                if ($status === Grade::STATUS_APPROVED) {
                    GradeReviewLog::firstOrCreate([
                        'grade_id' => $grade->id,
                        'performed_by' => $reviewedBy,
                        'action' => 'approved',
                    ]);
                }

                if ($status === Grade::STATUS_REJECTED) {
                    GradeReviewLog::firstOrCreate(
                        [
                            'grade_id' => $grade->id,
                            'performed_by' => $reviewedBy,
                            'action' => 'rejected',
                        ],
                        [
                            'reason' => $rejectionReason,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Ensure the student Grade Details & Breakdown page has rich sample data.
     *
     * This seeds one deterministic scenario for student01@example.com including:
     * - Published assignments in one enrolled subject
     * - Graded, submitted, and not-submitted assignment states
     * - Approved final grade plus review logs
     *
     * @param  Collection<int, User>  $teacherUsers
     * @param  Collection<int, User>  $staffUsers
     */
    private function seedStudentGradeDetailsBreakdownSample(Collection $teacherUsers, Collection $staffUsers): void
    {
        $studentUser = User::query()->where('email', 'student01@example.com')->first();
        $student = $studentUser?->student;
        if (! $student) {
            return;
        }

        $courseId = (int) DB::table('course_student')
            ->where('student_id', $student->id)
            ->whereIn('status', ['approved', 'withdrawal_pending'])
            ->orderBy('course_id')
            ->value('course_id');

        if ($courseId <= 0) {
            return;
        }

        $subject = Subject::query()
            ->where('course_id', $courseId)
            ->orderBy('subject_code')
            ->first(['id', 'course_id', 'subject_code', 'title']);

        if (! $subject) {
            return;
        }

        $teacherId = $this->resolveTeacherIdForSubject((int) $subject->id, $teacherUsers);
        $reviewerId = (int) ($staffUsers->first()?->id ?? $teacherId);

        $assignmentDefinitions = [
            [
                'title_prefix' => 'Breakdown Quiz 1',
                'description' => 'Sample quiz for Grade Details breakdown view.',
                'due_date' => Carbon::now()->subDays(18)->toDateString(),
                'max_score' => 20,
            ],
            [
                'title_prefix' => 'Breakdown Lab 1',
                'description' => 'Sample lab task for Grade Details breakdown view.',
                'due_date' => Carbon::now()->subDays(12)->toDateString(),
                'max_score' => 30,
            ],
            [
                'title_prefix' => 'Breakdown Case Study',
                'description' => 'Sample case study for Grade Details breakdown view.',
                'due_date' => Carbon::now()->subDays(6)->toDateString(),
                'max_score' => 25,
            ],
            [
                'title_prefix' => 'Breakdown Reflection',
                'description' => 'Sample reflection task to demonstrate not-submitted state.',
                'due_date' => Carbon::now()->addDays(3)->toDateString(),
                'max_score' => 25,
            ],
        ];

        $assignments = collect($assignmentDefinitions)
            ->map(function (array $definition) use ($subject, $courseId, $teacherId) {
                return Assignment::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'title' => $definition['title_prefix'].' - '.$subject->subject_code,
                    ],
                    [
                        'course_id' => $courseId,
                        'created_by' => $teacherId,
                        'description' => $definition['description'],
                        'due_date' => $definition['due_date'],
                        'due_time' => '23:59:00',
                        'max_score' => $definition['max_score'],
                        'status' => Assignment::STATUS_PUBLISHED,
                        'allowed_file_types' => ['pdf', 'docx', 'txt'],
                        'max_file_size' => 5120,
                    ]
                );
            })
            ->values();

        if ($assignments->count() < 4) {
            return;
        }

        $gradedSubmissionSpecs = [
            [
                'assignment' => $assignments[0],
                'score' => 17.5,
                'status' => AssignmentSubmission::STATUS_GRADED,
                'feedback' => 'Strong work with complete answers.',
                'graded_at' => Carbon::now()->subDays(5),
            ],
            [
                'assignment' => $assignments[1],
                'score' => 19.5,
                'status' => AssignmentSubmission::STATUS_RETURNED,
                'feedback' => 'Good attempt. Recheck the final optimization step.',
                'graded_at' => Carbon::now()->subDays(3),
            ],
        ];

        foreach ($gradedSubmissionSpecs as $spec) {
            /** @var Assignment $assignment */
            $assignment = $spec['assignment'];
            $filePath = sprintf(
                'assignments/seed/grade-details-assignment-%d-student-%d.txt',
                $assignment->id,
                $student->id
            );

            Storage::disk('public')->put(
                $filePath,
                sprintf('Grade details sample submission for assignment %d and student %d.', $assignment->id, $student->id)
            );

            AssignmentSubmission::updateOrCreate(
                [
                    'assignment_id' => $assignment->id,
                    'student_id' => $student->id,
                ],
                [
                    'file_path' => $filePath,
                    'original_filename' => basename($filePath),
                    'comments' => 'Seeded for Grade Details & Breakdown page sample data.',
                    'score' => $spec['score'],
                    'feedback' => $spec['feedback'],
                    'graded_by' => $teacherId,
                    'graded_at' => $spec['graded_at'],
                    'status' => $spec['status'],
                ]
            );
        }

        $submittedAssignment = $assignments[2];
        $submittedFilePath = sprintf(
            'assignments/seed/grade-details-assignment-%d-student-%d.txt',
            $submittedAssignment->id,
            $student->id
        );
        Storage::disk('public')->put(
            $submittedFilePath,
            sprintf(
                'Ungraded sample submission for assignment %d and student %d.',
                $submittedAssignment->id,
                $student->id
            )
        );

        AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $submittedAssignment->id,
                'student_id' => $student->id,
            ],
            [
                'file_path' => $submittedFilePath,
                'original_filename' => basename($submittedFilePath),
                'comments' => 'Submitted, awaiting grading.',
                'score' => null,
                'feedback' => null,
                'graded_by' => null,
                'graded_at' => null,
                'status' => AssignmentSubmission::STATUS_SUBMITTED,
            ]
        );

        AssignmentSubmission::query()
            ->where('assignment_id', $assignments[3]->id)
            ->where('student_id', $student->id)
            ->delete();

        $grade = Grade::updateOrCreate(
            [
                'subject_id' => $subject->id,
                'student_id' => $student->id,
            ],
            [
                'course_id' => $courseId,
                'graded_by' => $teacherId,
                'score' => 78.0,
                'status' => Grade::STATUS_APPROVED,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => Carbon::now()->subDays(1),
                'rejection_reason' => null,
            ]
        );

        GradeReviewLog::updateOrCreate(
            [
                'grade_id' => $grade->id,
                'action' => 'submitted',
            ],
            [
                'performed_by' => $teacherId,
                'reason' => null,
                'meta' => [
                    'seeded' => true,
                    'source' => 'grade_details_breakdown_sample',
                    'subject_id' => $subject->id,
                    'course_id' => $courseId,
                ],
            ]
        );

        GradeReviewLog::updateOrCreate(
            [
                'grade_id' => $grade->id,
                'action' => 'approved',
            ],
            [
                'performed_by' => $reviewerId,
                'reason' => null,
                'meta' => [
                    'seeded' => true,
                    'source' => 'grade_details_breakdown_sample',
                ],
            ]
        );

        if ($this->command) {
            $this->command->line(
                sprintf(
                    'Grade breakdown sample ensured for student01@example.com (%s).',
                    $subject->subject_code
                )
            );
        }
    }

    /**
     * @param  Collection<int, Student>  $students
     * @param  Collection<int, User>  $staffUsers
     */
    private function seedFees(Collection $students, Collection $staffUsers): void
    {
        foreach ($students->values() as $index => $student) {
            $firstStatus = match ($index % 3) {
                0 => Fee::STATUS_PAID,
                1 => Fee::STATUS_PAYMENT_PENDING,
                default => Fee::STATUS_PENDING,
            };

            $secondStatus = $index % 2 === 0 ? Fee::STATUS_PENDING : Fee::STATUS_PAID;

            $this->upsertFee(
                $student,
                'Tuition Fee - Semester 1',
                1500 + ($index * 20),
                Carbon::today()->addDays(10 + ($index % 5)),
                $firstStatus,
                $staffUsers
            );

            $this->upsertFee(
                $student,
                'Library and Resources Fee',
                250 + ($index * 5),
                Carbon::today()->addDays(20 + ($index % 7)),
                $secondStatus,
                $staffUsers
            );
        }
    }

    /**
     * @param  Collection<int, User>  $staffUsers
     */
    private function upsertFee(Student $student, string $description, float $amount, Carbon $dueDate, string $status, Collection $staffUsers): void
    {
        $isPaid = $status === Fee::STATUS_PAID;
        $isPaymentPending = $status === Fee::STATUS_PAYMENT_PENDING;

        Fee::updateOrCreate(
            [
                'student_id' => $student->id,
                'description' => $description,
                'due_date' => $dueDate->toDateString(),
            ],
            [
                'amount' => round($amount, 2),
                'status' => $status,
                'paid_date' => $isPaid ? Carbon::today()->subDays(1)->toDateString() : null,
                'payment_intent_id' => $isPaymentPending ? 'pi_seed_'.md5($student->id.$description) : null,
                'payment_method' => $isPaid ? 'manual_approval' : null,
                'payment_processed_at' => $isPaid ? Carbon::now()->subDay() : null,
                'processed_by' => $isPaid ? $staffUsers->first()->id : null,
            ]
        );
    }

    /**
     * @param  Collection<int, User>  $staffUsers
     * @return Collection<int, Announcement>
     */
    private function seedAnnouncements(Collection $staffUsers): Collection
    {
        $authorId = $staffUsers->first()->id;

        $definitions = [
            [
                'title' => 'Welcome to the Academic Portal',
                'body' => 'This is seeded demo content. Explore each module using the sample accounts.',
                'priority' => 'info',
                'pinned' => true,
                'require_ack' => false,
                'audience' => ['roles' => ['all']],
                'publish_offset_days' => -5,
                'expire_offset_days' => null,
            ],
            [
                'title' => 'Assignment Submission Reminder',
                'body' => 'Students should submit this week\'s assignments before Friday 23:59.',
                'priority' => 'important',
                'pinned' => false,
                'require_ack' => true,
                'audience' => ['roles' => ['student']],
                'publish_offset_days' => -2,
                'expire_offset_days' => 7,
            ],
            [
                'title' => 'Teacher Coordination Meeting',
                'body' => 'Teachers meeting scheduled on Wednesday at 10:00 in Conference Room B.',
                'priority' => 'important',
                'pinned' => false,
                'require_ack' => false,
                'audience' => ['roles' => ['teacher']],
                'publish_offset_days' => -1,
                'expire_offset_days' => 5,
            ],
            [
                'title' => 'Finance Deadline Notice',
                'body' => 'Payment pending fees will be reviewed by staff every Monday at 09:00.',
                'priority' => 'urgent',
                'pinned' => true,
                'require_ack' => true,
                'audience' => ['roles' => ['staff', 'student']],
                'publish_offset_days' => -3,
                'expire_offset_days' => 10,
            ],
            [
                'title' => 'Expired Demo Announcement',
                'body' => 'This announcement is intentionally expired to test filtering.',
                'priority' => 'info',
                'pinned' => false,
                'require_ack' => false,
                'audience' => ['roles' => ['all']],
                'publish_offset_days' => -20,
                'expire_offset_days' => -2,
            ],
            [
                'title' => 'Upcoming Future Announcement',
                'body' => 'This one is scheduled for future visibility testing.',
                'priority' => 'info',
                'pinned' => false,
                'require_ack' => false,
                'audience' => ['roles' => ['all']],
                'publish_offset_days' => 3,
                'expire_offset_days' => 20,
            ],
        ];

        $announcements = collect();

        foreach ($definitions as $definition) {
            $publishAt = Carbon::now()->addDays($definition['publish_offset_days']);
            $expireAt = $definition['expire_offset_days'] !== null
                ? Carbon::now()->addDays($definition['expire_offset_days'])
                : null;

            $announcement = Announcement::updateOrCreate(
                ['title' => $definition['title']],
                [
                    'body' => $definition['body'],
                    'priority' => $definition['priority'],
                    'pinned' => $definition['pinned'],
                    'require_ack' => $definition['require_ack'],
                    'audience' => $definition['audience'],
                    'publish_at' => $publishAt,
                    'expires_at' => $expireAt,
                    'user_id' => $authorId,
                ]
            );

            if (Schema::hasColumn('announcements', 'visible_from')) {
                $announcement->visible_from = $publishAt;
            }
            if (Schema::hasColumn('announcements', 'visible_until')) {
                $announcement->visible_until = $expireAt;
            }
            $announcement->save();

            $announcements->push($announcement->fresh());
        }

        return $announcements;
    }

    /**
     * @param  Collection<int, Announcement>  $announcements
     * @param  Collection<int, User>  $staffUsers
     * @param  Collection<int, User>  $teacherUsers
     * @param  Collection<int, User>  $studentUsers
     */
    private function seedAnnouncementReads(Collection $announcements, Collection $staffUsers, Collection $teacherUsers, Collection $studentUsers): void
    {
        $users = $staffUsers
            ->concat($teacherUsers)
            ->concat($studentUsers->take(8))
            ->values();

        foreach ($announcements->take(4)->values() as $announcementIndex => $announcement) {
            foreach ($users as $userIndex => $user) {
                if (($announcementIndex + $userIndex) % 3 !== 0) {
                    continue;
                }

                AnnouncementRead::updateOrCreate(
                    [
                        'announcement_id' => $announcement->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'read_at' => Carbon::now()->subHours(2 + $userIndex),
                        'acknowledged_at' => $announcement->require_ack && $userIndex % 2 === 0
                            ? Carbon::now()->subHours(1)
                            : null,
                    ]
                );
            }
        }
    }

    /**
     * @param  Collection<int, User>  $staffUsers
     * @param  Collection<int, User>  $teacherUsers
     * @param  Collection<int, User>  $studentUsers
     */
    private function seedMessages(Collection $staffUsers, Collection $teacherUsers, Collection $studentUsers): void
    {
        $seedMessages = [
            [
                'sender' => $staffUsers[0],
                'receiver' => $teacherUsers[0],
                'body' => 'Please review pending grades before Friday.',
                'read' => false,
            ],
            [
                'sender' => $teacherUsers[0],
                'receiver' => $staffUsers[0],
                'body' => 'Pending grades are ready for review.',
                'read' => true,
            ],
            [
                'sender' => $teacherUsers[1],
                'receiver' => $studentUsers[0],
                'body' => 'Your assignment feedback has been posted.',
                'read' => false,
            ],
            [
                'sender' => $studentUsers[0],
                'receiver' => $teacherUsers[1],
                'body' => 'Thank you, I will revise and resubmit.',
                'read' => true,
            ],
            [
                'sender' => $staffUsers[1],
                'receiver' => $studentUsers[3],
                'body' => 'Your fee payment is now under confirmation review.',
                'read' => false,
            ],
            [
                'sender' => $studentUsers[5],
                'receiver' => $staffUsers[1],
                'body' => 'Can you confirm if my enrollment request was approved?',
                'read' => false,
            ],
        ];

        foreach ($seedMessages as $message) {
            Message::firstOrCreate(
                [
                    'sender_id' => $message['sender']->id,
                    'receiver_id' => $message['receiver']->id,
                    'body' => $message['body'],
                ],
                [
                    'sender_role' => $message['sender']->role,
                    'receiver_role' => $message['receiver']->role,
                    'read' => $message['read'],
                ]
            );
        }
    }

    private function seedPublicInboxSamples(): void
    {
        ContactMessage::updateOrCreate(
            [
                'email' => 'parent.one@example.com',
                'subject' => 'Request for enrollment support',
            ],
            [
                'first_name' => 'Parent',
                'last_name' => 'One',
                'phone' => '+254700111222',
                'message' => 'Please assist with enrollment steps for the next intake.',
                'reply' => 'Thanks for reaching out. Please check your email for guidance.',
                'is_read' => true,
                'replied_at' => Carbon::now()->subDay(),
            ]
        );

        ContactMessage::updateOrCreate(
            [
                'email' => 'guest.two@example.com',
                'subject' => 'Issue with portal login',
            ],
            [
                'first_name' => 'Guest',
                'last_name' => 'Two',
                'phone' => null,
                'message' => 'I cannot log in after registration. Please help.',
                'reply' => null,
                'is_read' => false,
                'replied_at' => null,
            ]
        );

        FeedbackMessage::updateOrCreate(
            [
                'email' => 'student.feedback@example.com',
                'message' => 'The assignment module is very clear and easy to use.',
            ],
            [
                'name' => 'Feedback Student',
                'type' => 'compliment',
                'is_read' => true,
                'replied_at' => Carbon::now()->subDays(2),
            ]
        );

        FeedbackMessage::updateOrCreate(
            [
                'email' => 'teacher.feedback@example.com',
                'message' => 'Please add a bulk export option for grade reports.',
            ],
            [
                'name' => 'Feedback Teacher',
                'type' => 'suggestion',
                'is_read' => false,
                'replied_at' => null,
            ]
        );
    }

    /**
     * @param  Collection<int, User>  $teacherUsers
     */
    private function resolveTeacherIdForSubject(int $subjectId, Collection $teacherUsers): int
    {
        $teacherId = DB::table('subject_teacher')
            ->where('subject_id', $subjectId)
            ->orderBy('user_id')
            ->value('user_id');

        if ($teacherId) {
            return (int) $teacherId;
        }

        return (int) $teacherUsers->first()->id;
    }
}
