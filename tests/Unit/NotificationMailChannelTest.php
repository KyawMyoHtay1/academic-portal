<?php

namespace Tests\Unit;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Subject;
use App\Notifications\AnnouncementPublished;
use App\Notifications\AnnouncementReminder;
use App\Notifications\AttendanceAlert;
use App\Notifications\LowAttendanceAlert;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use stdClass;
use Tests\TestCase;

class NotificationMailChannelTest extends TestCase
{
    public function test_announcement_published_adds_mail_channel_when_email_notifications_are_enabled(): void
    {
        $notification = new AnnouncementPublished(new Announcement([
            'title' => 'Semester timetable updated',
        ]));

        $notifiable = new stdClass;
        $notifiable->name = 'Student';
        $notifiable->preferences = [
            'notify_announcements' => true,
            'email_notifications' => true,
        ];

        $this->assertSame(['database', 'mail'], $notification->via($notifiable));

        $mail = $notification->toMail($notifiable);
        $this->assertSame('New announcement published', $mail->subject);
        $this->assertSame('View announcements', $mail->actionText);
        $this->assertInstanceOf(ShouldQueue::class, $notification);
        $this->assertSame(['mail' => 'database'], $notification->viaConnections());
    }

    public function test_attendance_alert_adds_mail_channel_when_email_notifications_are_enabled(): void
    {
        $course = new Course([
            'course_code' => 'CS101',
            'title' => 'Computer Science',
        ]);

        $subject = new Subject([
            'subject_code' => 'CS101-1',
            'title' => 'Intro to Programming',
        ]);
        $subject->setRelation('course', $course);

        $attendance = new Attendance([
            'date' => Carbon::parse('2026-04-03'),
            'status' => 'present',
        ]);
        $attendance->setRelation('subject', $subject);

        $notification = new AttendanceAlert($attendance);

        $notifiable = new stdClass;
        $notifiable->name = 'Student';
        $notifiable->role = 'student';
        $notifiable->preferences = [
            'notify_attendance' => true,
            'email_notifications' => true,
        ];

        $this->assertSame(['database', 'mail'], $notification->via($notifiable));

        $mail = $notification->toMail($notifiable);
        $this->assertSame('Attendance recorded', $mail->subject);
        $this->assertSame('View attendance', $mail->actionText);
        $this->assertInstanceOf(ShouldQueue::class, $notification);
        $this->assertSame(['mail' => 'database'], $notification->viaConnections());
    }

    public function test_reminder_notifications_queue_mail_on_the_database_connection(): void
    {
        $announcementReminder = new AnnouncementReminder(new Announcement([
            'title' => 'Final reminder',
        ]));

        $lowAttendanceAlert = new LowAttendanceAlert(
            student: new \App\Models\Student(['id' => 5]),
            rate: 63.5,
            threshold: 75.0,
        );

        $this->assertInstanceOf(ShouldQueue::class, $announcementReminder);
        $this->assertSame(['mail' => 'database'], $announcementReminder->viaConnections());
        $this->assertInstanceOf(ShouldQueue::class, $lowAttendanceAlert);
        $this->assertSame(['mail' => 'database'], $lowAttendanceAlert->viaConnections());
    }
}
