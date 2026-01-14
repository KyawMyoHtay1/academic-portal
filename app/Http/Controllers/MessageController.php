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
     * Display the current user's inbox (both received and sent messages).
     */
    public function index(): Response
    {
        $user = Auth::user();

        // Get both received and sent messages
        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($user) {
                $query->where('receiver_id', $user->id)
                    ->orWhere('sender_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($m) use ($user) {
                $isSent = $m->sender_id === $user->id;
                
                return [
                    'id' => $m->id,
                    'body' => $m->body,
                    'sender' => $m->sender?->name ?? 'Unknown',
                    'sender_photo' => $m->sender?->photo,
                    'sender_role' => $m->sender?->role ?? 'unknown',
                    'receiver' => $m->receiver?->name ?? 'Unknown',
                    'receiver_photo' => $m->receiver?->photo,
                    'receiver_role' => $m->receiver_role ?? $m->receiver?->role ?? 'unknown',
                    'read' => $m->read,
                    'is_sent' => $isSent, // Flag to identify sent messages
                    'created_at' => $m->created_at->timestamp * 1000, // Unix timestamp in milliseconds (timezone-agnostic)
                ];
            });

        return Inertia::render('Messages/Index', [
            'messages' => $messages,
        ]);
    }

    /**
     * Show the form for composing a new message (all roles).
     */
    public function create(): Response
    {
        $user = Auth::user();

        $recipients = User::where('id', '!=', $user->id)
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'photo']);

        return Inertia::render('Messages/Create', [
            'recipients' => $recipients,
        ]);
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'body' => ['required', 'string'],
        ]);

        // Prevent sending to self
        if ((int) $data['receiver_id'] === $user->id) {
            return redirect()
                ->back()
                ->withErrors(['receiver_id' => 'You cannot send a message to yourself.']);
        }

        $receiver = User::find($data['receiver_id']);

        Message::create([
            'sender_id' => $user->id,
            'sender_role' => $user->role,
            'receiver_id' => $data['receiver_id'],
            'receiver_role' => $receiver?->role,
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

        return back();
    }
}
