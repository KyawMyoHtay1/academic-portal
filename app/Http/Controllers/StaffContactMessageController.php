<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\ContactMessages\ReplyContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StaffContactMessageController extends Controller
{
    /**
     * Display contact messages (staff inbox).
     */
    public function index(): Response
    {
        $q = trim((string) request('q', ''));
        $status = trim((string) request('status', 'all'));
        if (! in_array($status, ['all', 'unread', 'read', 'replied'], true)) {
            $status = 'all';
        }

        $messages = ContactMessage::query()
            ->when($status === 'unread', function ($query) {
                $query->where('is_read', false)->whereNull('replied_at');
            })
            ->when($status === 'read', function ($query) {
                $query->where('is_read', true)->whereNull('replied_at');
            })
            ->when($status === 'replied', function ($query) {
                $query->whereNotNull('replied_at');
            })
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q).'%';

                $query->where(function ($sub) use ($like) {
                    $sub->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('subject', 'like', $like)
                        ->orWhere('message', 'like', $like);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $unreadCount = ContactMessage::query()
            ->where('is_read', false)
            ->count();
        $readCount = ContactMessage::query()
            ->where('is_read', true)
            ->whereNull('replied_at')
            ->count();
        $repliedCount = ContactMessage::query()
            ->whereNotNull('replied_at')
            ->count();

        return Inertia::render('Admin/ContactMessages/Index', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'statusCounts' => [
                'unread' => $unreadCount,
                'read' => $readCount,
                'replied' => $repliedCount,
            ],
            'filters' => [
                'q' => $q,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Mark a contact message as read.
     */
    public function markRead(ContactMessage $contactMessage): RedirectResponse
    {
        if (! $contactMessage->is_read) {
            $contactMessage->forceFill(['is_read' => true])->save();
        }

        return back();
    }

    /**
     * Reply to a contact message (store reply text and mark as replied).
     */
    public function reply(ReplyContactMessageRequest $request, ContactMessage $contactMessage): RedirectResponse
    {
        $data = $request->validated();

        $contactMessage->update([
            'reply' => $data['reply'],
            'replied_at' => now(),
            'is_read' => true,
        ]);

        return back()->with('success', 'Reply saved.');
    }
}
