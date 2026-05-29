<?php

namespace App\Http\Controllers;

use App\Services\GuestPageDataService;
use Illuminate\Contracts\View\View;
use Inertia\Inertia;
use Inertia\Response;

class GuestPageController extends Controller
{
    public function __construct(
        private readonly GuestPageDataService $guestPageDataService,
    ) {}

    public function userManualView()
    {
        $path = $this->resolveUserManualPath();

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="University_Academic_Portal_User_Manual.pdf"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function userManualDownload()
    {
        $path = $this->resolveUserManualPath();

        return response()->download($path, 'University_Academic_Portal_User_Manual.pdf', [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function home(): View
    {
        return view('guest.home', $this->guestPageDataService->homeData());
    }

    public function courses(): View
    {
        return view('guest.courses', $this->guestPageDataService->coursesData());
    }

    public function showCourse(int $course): View
    {
        return view('guest.course-show', $this->guestPageDataService->courseShowData($course));
    }

    public function news(): View
    {
        return view('guest.news', $this->guestPageDataService->newsData());
    }

    public function showNews(int $announcement): View
    {
        return view('guest.news-show', $this->guestPageDataService->newsShowData($announcement));
    }

    public function about(): View
    {
        return view('guest.about', $this->guestPageDataService->aboutData());
    }

    public function vision(): View
    {
        return view('guest.vision', $this->guestPageDataService->visionData());
    }

    public function services(): View
    {
        return view('guest.services', $this->guestPageDataService->servicesData());
    }

    public function support(): View
    {
        return view('guest.support', $this->guestPageDataService->supportData());
    }

    public function contact(): View
    {
        return view('guest.contact', $this->guestPageDataService->contactData());
    }

    public function privacyPolicy(): Response
    {
        return Inertia::render('PrivacyPolicy');
    }

    public function termsAndConditions(): Response
    {
        return Inertia::render('TermsAndConditions');
    }

    private function resolveUserManualPath(): string
    {
        $docsDirectory = public_path('docs');
        $candidates = [
            $docsDirectory.'/University_Academic_Portal_User_Manual.pdf',
            $docsDirectory.'/University Academic Portal User Manual.pdf',
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        if (is_dir($docsDirectory)) {
            $pdfFiles = glob($docsDirectory.'/*.pdf') ?: [];

            if ($pdfFiles !== []) {
                return $pdfFiles[0];
            }
        }

        abort(404, 'User manual PDF not found.');
    }
}
