<?php

namespace App\Http\Controllers;

use App\Http\Requests\Announcements\UpsertAnnouncementRequest;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StaffAnnouncementController extends Controller
{
    public function index(): Response
    {
        $totalUsers = User::count();
        $usersByRole = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $announcements = Announcement::with('author')
            ->withCount([
                'reads as read_count' => function ($query) {
                    $query->whereNotNull('read_at');
                },
                'reads as ack_count' => function ($query) {
                    $query->whereNotNull('acknowledged_at');
                },
            ])
            ->orderByDesc('pinned')
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 WHEN 'info' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($a) use ($totalUsers, $usersByRole) {
                $roles = $a->audience['roles'] ?? ['all'];
                $recipientCount = in_array('all', $roles, true)
                    ? $totalUsers
                    : collect($roles)->sum(function ($role) use ($usersByRole) {
                        return (int) ($usersByRole[$role] ?? 0);
                    });
                $readCount = (int) ($a->read_count ?? 0);
                $ackCount = (int) ($a->ack_count ?? 0);
                $readRate = $recipientCount > 0
                    ? round(($readCount / $recipientCount) * 100, 1)
                    : 0;
                $ackRate = $recipientCount > 0
                    ? round(($ackCount / $recipientCount) * 100, 1)
                    : 0;

                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'body' => $a->body,
                    'priority' => $a->priority ?? 'info',
                    'pinned' => (bool) $a->pinned,
                    'require_ack' => (bool) $a->require_ack,
                    'audience' => [
                        'roles' => $roles,
                    ],
                    'publish_at' => $a->publish_at?->toISOString(),
                    'expires_at' => $a->expires_at?->toISOString(),
                    'author' => $a->author?->name ?? 'Staff',
                    'author_photo' => $a->author?->photo,
                    'created_at' => $a->created_at->format('Y-m-d'),
                    'created_at_iso' => $a->created_at->toDateString(),
                    'analytics' => [
                        'recipient_count' => $recipientCount,
                        'read_count' => $readCount,
                        'ack_count' => $ackCount,
                        'read_rate' => $readRate,
                        'ack_rate' => $ackRate,
                    ],
                ];
            });

        return Inertia::render('Admin/Announcements/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Announcements/Create');
    }

    public function store(UpsertAnnouncementRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Announcement::create([
            ...$data,
            'pinned' => (bool) ($data['pinned'] ?? false),
            'require_ack' => (bool) ($data['require_ack'] ?? false),
            'audience' => $data['audience'] ?? ['roles' => ['all']],
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement): Response
    {
        return Inertia::render('Admin/Announcements/Edit', [
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'body' => $announcement->body,
                'priority' => $announcement->priority ?? 'info',
                'pinned' => (bool) $announcement->pinned,
                'require_ack' => (bool) $announcement->require_ack,
                'audience' => $announcement->audience ?? ['roles' => ['all']],
                'publish_at' => $announcement->publish_at?->format('Y-m-d\TH:i'),
                'expires_at' => $announcement->expires_at?->format('Y-m-d\TH:i'),
            ],
        ]);
    }

    public function update(UpsertAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $data = $request->validated();

        $announcement->update([
            ...$data,
            'pinned' => (bool) ($data['pinned'] ?? false),
            'require_ack' => (bool) ($data['require_ack'] ?? false),
            'audience' => $data['audience'] ?? ['roles' => ['all']],
        ]);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
