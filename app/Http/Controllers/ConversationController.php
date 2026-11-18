<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ServiceOffer;
use App\Models\ServiceRequest;
use App\Services\ConversationStarter;
use App\Services\MessageSender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function __construct(
        protected ConversationStarter $starter,
        protected MessageSender $sender
    ) {}

    /**
     * Display a listing of user conversations (Inbox).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $conversations = Conversation::query()
            ->where(function ($query) use ($user) {
                $query->where('customer_id', $user->id)
                    ->orWhere('provider_id', $user->id);
            })
            ->with([
                'customer',
                'provider',
                'serviceRequest',
                'serviceOffer',
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                }
            ])
            ->latest('updated_at')
            ->paginate(15);

        return view('chat.index', compact('conversations'));
    }

    /**
     * Start a new conversation or open existing one from listing.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_request_id' => 'nullable|exists:service_requests,id',
            'service_offer_id' => 'nullable|exists:service_offers,id',
        ]);

        try {
            $conversation = $this->starter->start(Auth::user(), $validated);
            return redirect()->route('chat.show', $conversation);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->with('error', $e->getMessage());
        }
    }

    /**
     * Show a conversation room with message history.
     */
    public function show(Conversation $conversation): View
    {
        Gate::authorize('view', $conversation);

        $user = Auth::user();

        // Mark unread messages sent by the other participant as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Mark associated unread notifications as read
        $user->unreadNotifications()
            ->where('data->conversation_id', $conversation->id)
            ->update(['read_at' => now()]);

        $conversation->load([
            'customer',
            'provider',
            'serviceRequest',
            'serviceOffer',
            'messages.sender',
            'reviews',
        ]);

        return view('chat.show', compact('conversation'));
    }

    /**
     * Send a message within the conversation.
     */
    public function sendMessage(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        Gate::authorize('sendMessage', $conversation);

        $user = Auth::user();

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        try {
            $message = $this->sender->send($user, $conversation, $validated['body']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => [
                        'id' => $message->id,
                        'conversation_id' => $message->conversation_id,
                        'sender_id' => $message->sender_id,
                        'sender_name' => $user->name,
                        'body' => $message->body,
                        'created_at' => $message->created_at->format('H:i'),
                        'is_sender' => true,
                    ],
                ]);
            }

            return redirect()->route('chat.show', $conversation);
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Poll new messages after a given message ID.
     */
    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $user = Auth::user();

        if (!$conversation->hasParticipant($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $afterId = (int) $request->query('after', 0);

        $query = $conversation->messages()->with('sender');

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->oldest()->get();

        // Mark incoming messages as read
        $incomingMessageIds = $messages
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->pluck('id');

        if ($incomingMessageIds->isNotEmpty()) {
            $conversation->messages()
                ->whereIn('id', $incomingMessageIds)
                ->update(['read_at' => now()]);
        }

        return response()->json([
            'messages' => $messages->map(function ($msg) use ($user) {
                return [
                    'id' => $msg->id,
                    'conversation_id' => $msg->conversation_id,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $msg->sender ? $msg->sender->name : 'Pengguna',
                    'body' => $msg->body,
                    'created_at' => $msg->created_at?->format('H:i'),
                    'is_sender' => $msg->sender_id === $user->id,
                ];
            }),
        ]);
    }

    /**
     * Mark the conversation / transaction as completed (Customer only).
     */
    public function complete(Conversation $conversation): RedirectResponse
    {
        Gate::authorize('complete', $conversation);

        $user = Auth::user();

        if ($conversation->isCompleted()) {
            return back()->with('info', 'Percakapan ini sudah ditandai selesai sebelumnya.');
        }

        DB::transaction(function () use ($conversation) {
            $conversation->update([
                'completed_at' => now(),
                'status' => 'completed',
            ]);

            // Jika percakapan terhubung dengan ServiceRequest, ubah statusnya menjadi 'closed'
            if ($conversation->service_request_id) {
                ServiceRequest::where('id', $conversation->service_request_id)->update(['status' => 'closed']);
            }
        });

        return redirect()->route('chat.show', $conversation)
            ->with('success', 'Percakapan telah ditandai selesai. Anda sekarang dapat memberikan ulasan.');
    }
}
