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
        $messages = FeedbackMessage::query()
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $unreadCount = FeedbackMessage::query()
            ->where('is_read', false)
            ->count();

        return Inertia::render('Admin/FeedbackMessages/Index', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
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

