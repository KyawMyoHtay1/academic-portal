<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Fee;
use App\Models\Grade;
use App\Policies\CoursePolicy;
use App\Policies\FeePolicy;
use App\Policies\GradePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Course::class => CoursePolicy::class,
        Fee::class => FeePolicy::class,
        Grade::class => GradePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
