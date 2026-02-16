<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Queue::failing(function (JobFailed $event): void {
            Log::error('queue.job_failed', [
                'connection' => $event->connectionName,
                'queue' => $event->job?->getQueue(),
                'job' => $event->job?->resolveName(),
                'job_id' => $event->job?->getJobId(),
                'exception' => $event->exception->getMessage(),
            ]);
        });
    }
}
