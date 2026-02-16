<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Queue\Failed\FailedJobProviderInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class StaffFailedJobController extends Controller
{
    private const PER_PAGE = 20;

    public function index(Request $request): Response
    {
        $q = trim((string) $request->input('q', ''));

        $allJobs = $this->listFailedJobs();
        $filteredJobs = $this->filterJobs($allJobs, $q);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $filteredJobs
            ->slice(($page - 1) * self::PER_PAGE, self::PER_PAGE)
            ->values();

        $jobs = new LengthAwarePaginator(
            $items,
            $filteredJobs->count(),
            self::PER_PAGE,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ],
        );

        return Inertia::render('Admin/FailedJobs/Index', [
            'jobs' => $jobs,
            'totalFailedCount' => $allJobs->count(),
            'filters' => [
                'q' => $q,
            ],
        ]);
    }

    public function retry(string $failedJobId): RedirectResponse
    {
        $failer = $this->failer();

        if (! $failer->find($failedJobId)) {
            return back()->with('error', 'Failed job was not found.');
        }

        try {
            Artisan::call('queue:retry', ['id' => [$failedJobId]]);
        } catch (Throwable $e) {
            return back()->with('error', 'Unable to retry the failed job right now.');
        }

        return back()->with('success', "Failed job #{$failedJobId} has been queued for retry.");
    }

    public function retryAll(): RedirectResponse
    {
        $failer = $this->failer();
        $failedIds = $failer->ids();

        if (count($failedIds) === 0) {
            return back()->with('success', 'There are no failed jobs to retry.');
        }

        try {
            Artisan::call('queue:retry', ['id' => ['all']]);
        } catch (Throwable $e) {
            return back()->with('error', 'Unable to retry failed jobs right now.');
        }

        return back()->with('success', 'All failed jobs have been queued for retry.');
    }

    public function destroy(string $failedJobId): RedirectResponse
    {
        $failer = $this->failer();

        if (! $failer->forget($failedJobId)) {
            return back()->with('error', 'Failed job was not found.');
        }

        return back()->with('success', "Failed job #{$failedJobId} has been deleted.");
    }

    public function flush(): RedirectResponse
    {
        $failer = $this->failer();
        $failedIds = $failer->ids();

        if (count($failedIds) === 0) {
            return back()->with('success', 'There are no failed jobs to clear.');
        }

        try {
            Artisan::call('queue:flush');
        } catch (Throwable $e) {
            return back()->with('error', 'Unable to clear failed jobs right now.');
        }

        return back()->with('success', 'All failed jobs have been cleared.');
    }

    /**
     * @return Collection<int, array{
     *     id: string,
     *     uuid: string,
     *     connection: string,
     *     queue: string,
     *     failed_at: string,
     *     job_name: string,
     *     exception_preview: string
     * }>
     */
    private function listFailedJobs(): Collection
    {
        return collect($this->failer()->all())
            ->map(function ($job) {
                $item = (object) $job;

                return [
                    'id' => (string) ($item->id ?? $item->uuid ?? ''),
                    'uuid' => (string) ($item->uuid ?? ''),
                    'connection' => (string) ($item->connection ?? ''),
                    'queue' => (string) ($item->queue ?? ''),
                    'failed_at' => (string) ($item->failed_at ?? ''),
                    'job_name' => $this->extractJobName((string) ($item->payload ?? '')),
                    'exception_preview' => Str::of((string) ($item->exception ?? ''))
                        ->before("\n")
                        ->limit(180)
                        ->value(),
                ];
            })
            ->filter(fn (array $job) => $job['id'] !== '')
            ->sortByDesc('failed_at')
            ->values();
    }

    /**
     * @param  Collection<int, array{
     *     id: string,
     *     uuid: string,
     *     connection: string,
     *     queue: string,
     *     failed_at: string,
     *     job_name: string,
     *     exception_preview: string
     * }>  $jobs
     * @return Collection<int, array{
     *     id: string,
     *     uuid: string,
     *     connection: string,
     *     queue: string,
     *     failed_at: string,
     *     job_name: string,
     *     exception_preview: string
     * }>
     */
    private function filterJobs(Collection $jobs, string $q): Collection
    {
        if ($q === '') {
            return $jobs;
        }

        $needle = Str::lower($q);

        return $jobs->filter(function (array $job) use ($needle) {
            return Str::contains(Str::lower($job['id']), $needle)
                || Str::contains(Str::lower($job['uuid']), $needle)
                || Str::contains(Str::lower($job['connection']), $needle)
                || Str::contains(Str::lower($job['queue']), $needle)
                || Str::contains(Str::lower($job['job_name']), $needle)
                || Str::contains(Str::lower($job['exception_preview']), $needle);
        })->values();
    }

    private function extractJobName(string $payload): string
    {
        if ($payload === '') {
            return 'Unknown job';
        }

        $decoded = json_decode($payload, true);
        if (! is_array($decoded)) {
            return 'Unknown job';
        }

        return (string) (
            data_get($decoded, 'displayName')
            ?? data_get($decoded, 'data.commandName')
            ?? data_get($decoded, 'job')
            ?? 'Unknown job'
        );
    }

    private function failer(): FailedJobProviderInterface
    {
        return app('queue.failer');
    }
}
