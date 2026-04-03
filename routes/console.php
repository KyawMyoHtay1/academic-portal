<?php

use App\Services\ImageService;
use App\Jobs\SendLowAttendanceAlertsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attendance:send-low-attendance-alerts', function () {
    SendLowAttendanceAlertsJob::dispatch();
    $this->info('Low attendance alerts job completed.');
})->purpose('Send automated low attendance alerts to students');

Artisan::command('images:backfill-table-thumbs {--force : Regenerate existing table thumbnails} {--folder=* : Limit to specific public disk folders}', function () {
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $disk = Storage::disk('public');
    $selectedFolders = collect($this->option('folder'))
        ->map(fn ($folder) => trim((string) $folder))
        ->filter()
        ->values();

    $folders = $selectedFolders->isNotEmpty()
        ? $selectedFolders->all()
        : ['users', 'students', 'courses', 'subjects'];

    $files = collect($folders)
        ->flatMap(function (string $folder) use ($disk) {
            return $disk->exists($folder) ? $disk->allFiles($folder) : [];
        })
        ->map(fn ($path) => ltrim(str_replace('\\', '/', (string) $path), '/'))
        ->filter(fn (string $path) => ! str_contains('/'.$path, '/table/'))
        ->filter(fn (string $path) => preg_match('/\.(jpe?g|png)$/i', $path) === 1)
        ->unique()
        ->values();

    if ($files->isEmpty()) {
        $this->warn('No source images found for the selected folders.');

        return 0;
    }

    $this->info(sprintf(
        'Scanning %d image(s) in: %s',
        $files->count(),
        implode(', ', $folders)
    ));

    $progress = $this->output->createProgressBar($files->count());
    $progress->start();

    $created = 0;
    $failed = 0;
    $failures = [];
    $force = (bool) $this->option('force');

    foreach ($files as $path) {
        try {
            $before = ImageService::tablePath($path);
            $result = ImageService::generateTableThumbnail($path, $force);
            $after = ImageService::tablePath($path);

            if ($result && $before !== $after) {
                $created++;
            }
        } catch (\Throwable $e) {
            $failed++;
            $failures[] = sprintf('%s (%s)', $path, $e->getMessage());
        }

        gc_collect_cycles();
        $progress->advance();
    }

    $progress->finish();
    $this->newLine(2);

    $this->info(sprintf('Created %d new table thumbnail(s).', $created));

    if ($failed > 0) {
        $this->warn(sprintf('%d image(s) failed.', $failed));
        foreach (array_slice($failures, 0, 20) as $failure) {
            $this->line(' - '.$failure);
        }
        if (count($failures) > 20) {
            $this->line(sprintf(' - ... and %d more', count($failures) - 20));
        }

        return 1;
    }

    $this->info('Backfill completed.');

    return 0;
})->purpose('Generate missing small table thumbnails for existing uploaded images');
