<?php

namespace App\Http\Controllers;

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

        $messages = ContactMessage::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q) . '%';

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

        return Inertia::render('Admin/ContactMessages/Index', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'filters' => [
                'q' => $q,
            ],
        ]);
    }

    /**
     * Mark a contact message as read.
     */
    public function markRead(ContactMessage $contactMessage): RedirectResponse
    {
        if (!$contactMessage->is_read) {
            $contactMessage->forceFill(['is_read' => true])->save();
        }

        return back();
    }
}

