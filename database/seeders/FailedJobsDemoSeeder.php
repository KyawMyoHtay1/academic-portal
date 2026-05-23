<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Inserts sample rows into failed_jobs for testing the admin Failed Jobs UI.
 * Not included in DatabaseSeeder — run explicitly:
 *
 *   php artisan db:seed --class=FailedJobsDemoSeeder
 */
class FailedJobsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'displayName' => 'App\\Jobs\\SendLowAttendanceAlertsJob',
                'exception' => 'RuntimeException: SMTP connection timed out while sending attendance digest.',
                'queue' => 'default',
            ],
            [
                'displayName' => 'App\\Notifications\\InvoiceIssuedNotification',
                'exception' => 'Illuminate\\Queue\\MaxAttemptsExceededException: Job has been attempted too many times.',
                'queue' => 'notifications',
            ],
            [
                'displayName' => 'App\\Jobs\\ProcessEnrollmentImportJob',
                'exception' => 'InvalidArgumentException: Row 42: missing student_id column.',
                'queue' => 'imports',
            ],
        ];

        foreach ($samples as $sample) {
            $uuid = (string) Str::uuid();

            DB::table('failed_jobs')->insert([
                'uuid' => $uuid,
                'connection' => 'database',
                'queue' => $sample['queue'],
                'payload' => json_encode([
                    'uuid' => $uuid,
                    'displayName' => $sample['displayName'],
                    'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
                    'data' => [
                        'commandName' => $sample['displayName'],
                        'command' => 'O:8:"stdClass":0:{}',
                    ],
                ], JSON_THROW_ON_ERROR),
                'exception' => $sample['exception'],
                'failed_at' => now()->subMinutes(random_int(5, 1440)),
            ]);
        }
    }
}
