<?php

namespace App\Http\Middleware;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $announcementsWidget = [
            'unreadCount' => 0,
            'latest' => [],
        ];

        if ($user) {
            // unread = visible announcements that don't have a read_at record for this user
            $unreadCount = Announcement::query()
                ->currentlyVisible()
                ->visibleToUser($user)
                ->whereDoesntHave('reads', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->whereNotNull('read_at');
                })
                ->count();

            $latest = Announcement::query()
                ->with('author')
                ->currentlyVisible()
                ->visibleToUser($user)
                ->orderByDesc('pinned')
                ->orderByRaw("FIELD(priority,'urgent','important','info')")
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'title' => $a->title,
                        'priority' => $a->priority ?? 'info',
                        'pinned' => (bool) $a->pinned,
                        'created_at' => $a->created_at->format('Y-m-d'),
                        'author' => $a->author?->name ?? 'Staff',
                    ];
                })
                ->all();

            $announcementsWidget = [
                'unreadCount' => $unreadCount,
                'latest' => $latest,
            ];
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'photo' => $user->photo,
                    'created_at' => $user->created_at?->toIso8601String(),
                    'last_login_at' => $user->last_login_at?->toIso8601String() ?? null,
                ] : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'unread' => [
                'messages' => $user
                    ? $user->receivedMessages()->where('read', false)->count()
                    : 0,
                'notifications' => $user
                    ? $user->unreadNotifications()->count()
                    : 0,
                'announcements' => $announcementsWidget['unreadCount'] ?? 0,
            ],
            'announcementsWidget' => $announcementsWidget,
            'recaptchaSiteKey' => config('recaptcha.site_key'),
        ];
    }
}
