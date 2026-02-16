<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StaffAnnouncementController extends Controller
{
    public function index(): Response
    {
        $announcements = Announcement::with('author')
            ->orderByDesc('pinned')
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 WHEN 'info' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($a) {
                $roles = $a->audience['roles'] ?? ['all'];

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

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'priority' => ['required', 'in:info,important,urgent'],
            'pinned' => ['nullable', 'boolean'],
            'require_ack' => ['nullable', 'boolean'],
            'audience' => ['nullable', 'array'],
            'audience.roles' => ['nullable', 'array'],
            'audience.roles.*' => ['in:all,student,teacher,staff'],
            'publish_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:publish_at'],
        ]);

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

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'priority' => ['required', 'in:info,important,urgent'],
            'pinned' => ['nullable', 'boolean'],
            'require_ack' => ['nullable', 'boolean'],
            'audience' => ['nullable', 'array'],
            'audience.roles' => ['nullable', 'array'],
            'audience.roles.*' => ['in:all,student,teacher,staff'],
            'publish_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:publish_at'],
        ]);

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
