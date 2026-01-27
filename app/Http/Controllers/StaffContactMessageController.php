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
        $messages = ContactMessage::query()
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $unreadCount = ContactMessage::query()
            ->where('is_read', false)
            ->count();

        return Inertia::render('Admin/ContactMessages/Index', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
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

