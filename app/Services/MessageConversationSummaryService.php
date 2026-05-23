<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MessageConversationSummaryService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildForUser(int $userId): array
    {
        return Cache::remember(
            $this->cacheKey($userId),
            now()->addSeconds(15),
            function () use ($userId) {
                $messages = Message::query()
                    ->with([
                        'sender:id,name,role,photo',
                        'receiver:id,name,role,photo',
                    ])
                    ->where(function ($query) use ($userId) {
                        $query->where('sender_id', $userId)
                            ->orWhere('receiver_id', $userId);
                    })
                    ->orderByDesc('created_at')
                    ->get();

                $summaries = [];

                foreach ($messages as $message) {
                    $otherUser = $message->sender_id === $userId
                        ? $message->receiver
                        : $message->sender;

                    if (! $otherUser) {
                        continue;
                    }

                    $otherUserId = (int) $otherUser->id;
                    if (! array_key_exists($otherUserId, $summaries)) {
                        $summaries[$otherUserId] = [
                            'user_id' => $otherUserId,
                            'name' => $otherUser->name,
                            'role' => $otherUser->role,
                            'photo' => $otherUser->photo,
                            'photo_thumb' => ImageService::tablePath($otherUser->photo),
                            'last_message' => $message->body,
                            'last_sender_id' => $message->sender_id,
                            'last_is_sent' => $message->sender_id === $userId,
                            'last_read' => (bool) $message->read,
                            'last_at' => $message->created_at?->timestamp ? $message->created_at->timestamp * 1000 : null,
                            'unread_count' => 0,
                        ];
                    }

                    if ($message->receiver_id === $userId && ! $message->read) {
                        $summaries[$otherUserId]['unread_count']++;
                    }
                }

                return collect($summaries)
                    ->sortByDesc(function (array $row) {
                        return (int) ($row['last_at'] ?? 0);
                    })
                    ->values()
                    ->all();
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

    private function cacheKey(int $userId): string
    {
        return "message-conversations:user:{$userId}";
    }
}
