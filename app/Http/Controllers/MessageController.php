<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\Messages\StoreMessageRequest;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use App\Services\AuthenticatedNavigationStateService;
use App\Services\ImageService;
use App\Services\MessageConversationSummaryService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class MessageController extends Controller
{
    public function __construct(
        private readonly AuthenticatedNavigationStateService $navigationStateService,
        private readonly MessageConversationSummaryService $conversationSummaryService,
    ) {}

    /**
     * Display the current user's inbox (both received and sent messages).
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();
        $activeConversation = $this->resolveActiveConversation($request);
        $conversationUserId = $activeConversation ? (int) $activeConversation->id : null;

        // Opening a conversation marks unread incoming messages in that thread as read.
        if ($conversationUserId !== null) {
            $messagesMarkedAsRead = Message::query()
                ->where('sender_id', $conversationUserId)
                ->where('receiver_id', $user->id)
                ->where('read', false)
                ->update(['read' => true]);

            if ($messagesMarkedAsRead > 0) {
                $this->navigationStateService->clearForUser($user);
                $this->conversationSummaryService->clearForUser($user);
            }
        }

        // Get both received and sent messages (paginated to avoid loading large inboxes).
        $messageQuery = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($user) {
                $query->where('receiver_id', $user->id)
                    ->orWhere('sender_id', $user->id);
            });

        if ($conversationUserId !== null) {
            $messageQuery->where(function ($query) use ($user, $conversationUserId) {
                $query->where(function ($pair) use ($user, $conversationUserId) {
                    $pair->where('sender_id', $user->id)
                        ->where('receiver_id', $conversationUserId);
                })->orWhere(function ($pair) use ($user, $conversationUserId) {
                    $pair->where('sender_id', $conversationUserId)
                        ->where('receiver_id', $user->id);
                });
            });
        }

        $messages = $messageQuery
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString()
            ->through(function ($m) use ($user) {
                $isSent = $m->sender_id === $user->id;

                return [
                    'id' => $m->id,
                    'body' => $m->body,
                    'sender' => $m->sender?->name ?? 'Unknown',
                    'sender_photo' => $m->sender?->photo,
                    'sender_photo_thumb' => ImageService::tablePath($m->sender?->photo),
                    'sender_role' => $m->sender?->role ?? 'unknown',
                    'receiver' => $m->receiver?->name ?? 'Unknown',
                    'receiver_photo' => $m->receiver?->photo,
                    'receiver_photo_thumb' => ImageService::tablePath($m->receiver?->photo),
                    'receiver_role' => $m->receiver_role ?? $m->receiver?->role ?? 'unknown',
                    'read' => $m->read,
                    'is_sent' => $isSent, // Flag to identify sent messages
                    'created_at' => $m->created_at->timestamp * 1000, // Unix timestamp in milliseconds (timezone-agnostic)
                ];
            });

        $conversations = $this->conversationSummaryService->buildForUser($user->id);

        return Inertia::render('Messages/Index', [
            'messages' => $messages,
            'conversations' => $conversations,
            'activeConversation' => $conversationUserId,
        ]);
    }

    /**
     * Show the form for composing a new message (all roles).
     */
    public function create(Request $request): Response
    {
        $user = Auth::user();

        $recipients = User::where('id', '!=', $user->id)
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'photo'])
            ->map(function (User $recipient) {
                return [
                    'id' => $recipient->id,
                    'name' => $recipient->name,
                    'email' => $recipient->email,
                    'role' => $recipient->role,
                    'photo' => $recipient->photo,
                    'photo_thumb' => ImageService::tablePath($recipient->photo),
                ];
            });
        $prefillRecipient = null;
        $requestedRecipient = (int) $request->input('to', 0);
        if ($requestedRecipient > 0 && $recipients->contains('id', $requestedRecipient)) {
            $prefillRecipient = (string) $requestedRecipient;
        }

        return Inertia::render('Messages/Create', [
            'recipients' => $recipients,
            'prefillRecipient' => $prefillRecipient,
        ]);
    }

    /**
     * Store a newly created message.
     */
    public function store(StoreMessageRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validated();

        // Prevent sending to self
        if ((int) $data['receiver_id'] === $user->id) {
            return redirect()
                ->back()
                ->withErrors(['receiver_id' => 'You cannot send a message to yourself.']);
        }

        $receiver = User::find($data['receiver_id']);

        $message = Message::create([
            'sender_id' => $user->id,
            'sender_role' => $user->role,
            'receiver_id' => $data['receiver_id'],
            'receiver_role' => $receiver?->role,
            'body' => $data['body'],
            'read' => false,
        ]);

        if ($receiver) {
            $receiver->notify(new NewMessageReceived(
                messageId: (int) $message->id,
                senderId: (int) $user->id,
                senderName: (string) $user->name,
                body: (string) $data['body'],
            ));

            $this->navigationStateService->clearForUser($receiver);
        }

        $this->conversationSummaryService->clearForUsers([
            $user,
            $receiver,
        ]);

        try {
            broadcast(new MessageSent($message, [
                (int) $user->id,
                (int) $data['receiver_id'],
            ]));
        } catch (Throwable $exception) {
            // Realtime delivery is best-effort. Message persistence must still succeed.
            Log::warning('message.broadcast_failed', [
                'message_id' => (int) $message->id,
                'sender_id' => (int) $user->id,
                'receiver_id' => (int) $data['receiver_id'],
                'exception' => $exception->getMessage(),
            ]);
        }

        Log::info('message.sent', [
            'sender_id' => $user->id,
            'receiver_id' => (int) $data['receiver_id'],
            'sender_role' => $user->role,
        ]);

        return redirect()
            ->route('messages.index', ['with_user' => (int) $data['receiver_id']])
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
            $this->navigationStateService->clearForUser($user);
            $this->conversationSummaryService->clearForUser($user);

            Log::info('message.read', [
                'message_id' => $message->id,
                'reader_id' => $user->id,
            ]);
        }

        return back();
    }

    private function resolveActiveConversation(Request $request): ?User
    {
        $withUser = (int) $request->input('with_user', 0);
        if ($withUser <= 0) {
            return null;
        }

        $currentUserId = Auth::id();
        if ($withUser === $currentUserId) {
            return null;
        }

        return User::query()
            ->whereKey($withUser)
            ->first();
    }
}
