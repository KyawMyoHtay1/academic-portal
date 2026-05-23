<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AnnouncementWidgetService
{
    /**
     * Build the dashboard announcement widget payload for an authenticated user.
     *
     * @return array{latest: array<int, array<string, bool|int|string>>}
     */
    public function buildForUser(?User $user): array
    {
        if (! $user) {
            return [
                'latest' => [],
            ];
        }

        $cacheTtl = now()->addSeconds(30);

        $latest = Cache::remember(
            "dashboard:announcements:{$user->id}:latest",
            $cacheTtl,
            function () use ($user) {
                return Announcement::query()
                    ->with('author:id,name')
                    ->currentlyVisible()
                    ->visibleToUser($user)
                    ->orderByDesc('pinned')
                    ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 WHEN 'info' THEN 3 ELSE 4 END")
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get([
                        'id',
                        'user_id',
                        'title',
                        'priority',
                        'pinned',
                        'created_at',
                    ])
                    ->map(function (Announcement $announcement) {
                        return [
                            'id' => $announcement->id,
                            'title' => $announcement->title,
                            'priority' => $announcement->priority ?? 'info',
                            'pinned' => (bool) $announcement->pinned,
                            'created_at' => $announcement->created_at->format('Y-m-d'),
                            'author' => $announcement->author?->name ?? 'Staff',
                        ];
                    })
                    ->all();
            }
        );

        return [
            'latest' => $latest,
        ];
    }
}
