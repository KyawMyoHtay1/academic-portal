<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    /**
     * Display all announcements for authenticated users.
     */
    public function index(): Response
    {
        $announcements = Announcement::with('author')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'body' => $a->body,
                    'author' => $a->author?->name ?? 'Staff',
                    'created_at' => $a->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('Announcements/Index', [
            'announcements' => $announcements,
        ]);
    }
}
