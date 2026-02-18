<?php

namespace App\Http\Controllers;

use App\Models\FeedbackMessage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StaffFeedbackMessageController extends Controller
{
    /**
     * Display feedback messages (staff inbox).
     */
    public function index(): Response
    {
        $q = trim((string) request('q', ''));
        $status = trim((string) request('status', 'all'));
        $type = trim((string) request('type', 'all'));
        if (! in_array($status, ['all', 'unread', 'read', 'replied'], true)) {
            $status = 'all';
        }

        $messages = FeedbackMessage::query()
            ->when($status === 'unread', function ($query) {
                $query->where('is_read', false)->whereNull('replied_at');
            })
            ->when($status === 'read', function ($query) {
                $query->where('is_read', true)->whereNull('replied_at');
            })
            ->when($status === 'replied', function ($query) {
                $query->whereNotNull('replied_at');
            })
            ->when($type !== '' && $type !== 'all', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q).'%';

                $query->where(function ($sub) use ($like) {
                    $sub->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('type', 'like', $like)
                        ->orWhere('message', 'like', $like);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $unreadCount = FeedbackMessage::query()
            ->where('is_read', false)
            ->count();
        $readCount = FeedbackMessage::query()
            ->where('is_read', true)
            ->whereNull('replied_at')
            ->count();
        $repliedCount = FeedbackMessage::query()
            ->whereNotNull('replied_at')
            ->count();
        $typeOptions = FeedbackMessage::query()
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->values();

        return Inertia::render('Admin/FeedbackMessages/Index', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'statusCounts' => [
                'unread' => $unreadCount,
                'read' => $readCount,
                'replied' => $repliedCount,
            ],
            'typeOptions' => $typeOptions,
            'filters' => [
                'q' => $q,
                'status' => $status,
                'type' => $type,
            ],
        ]);
    }

    /**
     * Mark a feedback message as read.
     */
    public function markRead(FeedbackMessage $feedbackMessage): RedirectResponse
    {
        if (! $feedbackMessage->is_read) {
            $feedbackMessage->forceFill(['is_read' => true])->save();
        }

        return back();
    }

    /**
     * Mark a feedback message as replied.
     */
    public function markReplied(FeedbackMessage $feedbackMessage): RedirectResponse
    {
        if (! $feedbackMessage->replied_at) {
            $feedbackMessage->forceFill([
                'replied_at' => now(),
                'is_read' => true,
            ])->save();
        }

        return back()->with('success', 'Feedback marked as replied.');
    }
}
