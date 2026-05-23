<?php

namespace App\Http\Controllers;

use App\Services\AnnouncementWidgetService;
use App\Services\DashboardStatsService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardStatsService $dashboardStatsService,
        private readonly AnnouncementWidgetService $announcementWidgetService,
    ) {}

    public function __invoke(): Response
    {
        $user = Auth::user();
        $payload = $this->dashboardStatsService->build($user);
        $payload['announcementsWidget'] = $this->announcementWidgetService->buildForUser($user);

        return Inertia::render('Dashboard', $payload);
    }
}
