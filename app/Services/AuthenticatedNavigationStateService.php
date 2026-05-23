<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AuthenticatedNavigationStateService
{
    /**
     * Build cached shared navigation state for authenticated layouts.
     *
     * @return array{
     *   unread: array{messages:int,notifications:int,announcements:int},
     *   notificationsPreview: array{items: array<int, array<string, string|null>>}
     * }
     */
    public function buildForUser(?User $user): array
    {
        if (! $user) {
            return $this->defaults();
        }

        return Cache::remember(
            $this->cacheKey($user->id),
            now()->addSeconds(15),
            function () use ($user) {
                $unreadNotifications = $user->unreadNotifications();
                $unreadNotificationCount = (int) $unreadNotifications->count();

                return [
                    'unread' => [
                        'messages' => (int) $user->receivedMessages()
                            ->where('read', false)
                            ->count(),
                        'notifications' => $unreadNotificationCount,
                        'announcements' => (int) Announcement::query()
                            ->currentlyVisible()
                            ->visibleToUser($user)
                            ->whereDoesntHave('reads', function ($query) use ($user) {
                                $query->where('user_id', $user->id)
                                    ->whereNotNull('read_at');
                            })
                            ->count(),
                    ],
                    'notificationsPreview' => [
                        'items' => $unreadNotificationCount > 0
                            ? $user->unreadNotifications()
                                ->orderByDesc('created_at')
                                ->limit(6)
                                ->get()
                                ->map(function ($notification) {
                                    return [
                                        'id' => $notification->id,
                                        'title' => (string) ($notification->data['title'] ?? 'Notification'),
                                        'message' => (string) ($notification->data['message'] ?? ''),
                                        'read_at' => $notification->read_at?->toIso8601String(),
                                        'created_at' => $notification->created_at?->toIso8601String(),
                                        'created_label' => $notification->created_at?->diffForHumans(),
                                        'url' => isset($notification->data['url'])
                                            ? (string) $notification->data['url']
                                            : '',
                                    ];
                                })
                                ->all()
                            : [],
                    ],
                ];
            }
        );
    }

    public function clearForUser(User|int|null $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        if (! $userId) {
            return;
        }

        Cache::forget($this->cacheKey($userId));
    }

    /**
     * @param  iterable<int|User|null>  $users
     */
    public function clearForUsers(iterable $users): void
    {
        foreach ($users as $user) {
            $this->clearForUser($user);
        }
    }

    /**
     * @return array{
     *   unread: array{messages:int,notifications:int,announcements:int},
     *   notificationsPreview: array{items: array<int, array<string, string|null>>}
     * }
     */
    private function defaults(): array
    {
        return [
            'unread' => [
                'messages' => 0,
                'notifications' => 0,
                'announcements' => 0,
            ],
            'notificationsPreview' => [
                'items' => [],
            ],
        ];
    }

    private function cacheKey(int $userId): string
    {
        return "shared-nav-state:user:{$userId}";
    }
}
