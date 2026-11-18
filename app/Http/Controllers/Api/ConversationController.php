<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Services\ConversationStarter;
use App\Services\MessageSender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ConversationController extends Controller
{
    public function __construct(
        protected ConversationStarter $starter,
        protected MessageSender $sender
    ) {}

    /**
     * List user conversations.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

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

        return ConversationResource::collection($conversations)->response();
    }

    /**
     * Start conversation via API.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_request_id' => 'nullable|exists:service_requests,id',
            'service_offer_id' => 'nullable|exists:service_offers,id',
        ]);

        try {
            $conversation = $this->starter->start($request->user(), $validated);
            $conversation->load(['customer', 'provider', 'serviceRequest', 'serviceOffer']);

            return (new ConversationResource($conversation))
                ->response()
                ->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Gagal memulai percakapan.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Show single conversation details.
     */
    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->hasParticipant($user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation->load(['customer', 'provider', 'serviceRequest', 'serviceOffer']);

        return (new ConversationResource($conversation))->response();
    }

    /**
     * List messages inside a conversation with polling support.
     */
    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->hasParticipant($user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $afterId = (int) $request->query('after', 0);

        $query = $conversation->messages()->with('sender');

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->oldest()->get();

        return MessageResource::collection($messages)->response();
    }

    /**
     * Send message via API.
     */
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if (!$conversation->hasParticipant($user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        try {
            $message = $this->sender->send($user, $conversation, $validated['body']);
            $message->load('sender');

            return (new MessageResource($message))
                ->response()
                ->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Gagal mengirim pesan.',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}

