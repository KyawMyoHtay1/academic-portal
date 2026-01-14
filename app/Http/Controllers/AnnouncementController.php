<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    /**
     * Display all announcements for authenticated users.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $announcements = Announcement::query()
            ->with(['author', 'reads' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->currentlyVisible()
            ->visibleToUser($user)
            ->orderByDesc('pinned')
            ->orderByRaw("FIELD(priority,'urgent','important','info')")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($a) {
                $read = $a->reads->first();
                $roles = $a->audience['roles'] ?? ['all'];

                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'body' => $a->body,
                    'priority' => $a->priority ?? 'info',
                    'pinned' => (bool) $a->pinned,
                    'require_ack' => (bool) $a->require_ack,
                    'publish_at' => $a->publish_at?->toISOString(),
                    'expires_at' => $a->expires_at?->toISOString(),
                    'audience' => [
                        'roles' => $roles,
                    ],
                    'author' => $a->author?->name ?? 'Staff',
                    'author_photo' => $a->author?->photo,
                    'created_at' => $a->created_at->format('Y-m-d'),
                    'read_at' => $read?->read_at?->toISOString(),
                    'acknowledged_at' => $read?->acknowledged_at?->toISOString(),
                ];
            });

        return Inertia::render('Announcements/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function markAsRead(Announcement $announcement): RedirectResponse
    {
        $user = Auth::user();

        // Basic visibility guard (prevents probing)
        $isVisible = Announcement::query()
            ->whereKey($announcement->id)
            ->currentlyVisible()
            ->visibleToUser($user)
            ->exists();

        abort_unless($isVisible, 403);

        AnnouncementRead::updateOrCreate(
            [
                'announcement_id' => $announcement->id,
                'user_id' => $user->id,
            ],
            [
                'read_at' => now(),
            ]
        );

        return back();
    }

    public function acknowledge(Announcement $announcement): RedirectResponse
    {
        $user = Auth::user();

        $isVisible = Announcement::query()
            ->whereKey($announcement->id)
            ->currentlyVisible()
            ->visibleToUser($user)
            ->exists();

        abort_unless($isVisible, 403);
        abort_unless((bool) $announcement->require_ack, 400);

        AnnouncementRead::updateOrCreate(
            [
                'announcement_id' => $announcement->id,
                'user_id' => $user->id,
            ],
            [
                'read_at' => now(),
                'acknowledged_at' => now(),
            ]
        );

        return back();
    }
}
