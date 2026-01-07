<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    /**
     * Display the current user's inbox.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $messages = Message::with('sender')
            ->where('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'body' => $m->body,
                    'sender' => $m->sender?->name ?? 'Unknown',
                    'read' => $m->read,
                    'created_at' => $m->created_at->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Messages/Index', [
            'messages' => $messages,
        ]);
    }

    /**
     * Show the form for composing a new message (staff/teacher only).
     */
    public function create(): Response
    {
        // For simplicity, allow sending only to students
        $students = User::where('role', 'student')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('Messages/Create', [
            'students' => $students,
        ]);
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Only staff or teacher can send
        if (! $user->isStaff() && ! $user->isTeacher()) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'body' => ['required', 'string'],
        ]);

        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $data['receiver_id'],
            'body' => $data['body'],
            'read' => false,
        ]);

        return redirect()
            ->route('messages.index')
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message): RedirectResponse
    {
        $user = Auth::user();

        if ($message->receiver_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (! $message->read) {
            $message->update(['read' => true]);
        }

        return redirect()->route('messages.index');
    }
}
