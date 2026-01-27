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

        $messages = FeedbackMessage::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q) . '%';

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

        return Inertia::render('Admin/FeedbackMessages/Index', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'filters' => [
                'q' => $q,
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
}

