<?php

namespace App\Http\Controllers;

use App\Services\DashboardStatsService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardStatsService $dashboardStatsService) {}

    public function __invoke(): Response
    {
        $payload = $this->dashboardStatsService->build(Auth::user());

        return Inertia::render('Dashboard', $payload);
    }
}
