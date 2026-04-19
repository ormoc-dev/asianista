<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesMessageInbox;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class MessageInboxController extends Controller
{
    use ProvidesMessageInbox;

    abstract protected function inboxView(): string;

    abstract protected function messageRouteGroup(): string;

    abstract protected function indexRoute(array $query = []): string;

    abstract protected function pollUrl(): string;

    abstract protected function sendUrlJsTemplate(): string;

    /**
     * Full thread JSON for AJAX inbox (no page reload).
     */
    public function thread(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        $this->touchUserSeen($user);

        $visible = $this->filterConversationsForUser(
            $this->buildConversationsQuery($user, '')
                ->where('conversations.id', $conversation->id)
                ->get(),
            $user
        );

        if ($visible->isEmpty()) {
            abort(403);
        }

        $activeConversation = Conversation::with([
            'participants',
            'lastMessage.user',
        ])->find($conversation->id);

        if (! $activeConversation) {
            abort(404);
        }

        $pivot = $activeConversation->participants
            ->firstWhere('id', $user->id)
            ?->pivot;

        $deletedAt = $pivot?->deleted_at;

        $messagesQuery = $activeConversation->messages()
            ->with('user')
            ->orderBy('created_at');

        if ($deletedAt) {
            $messagesQuery->where('created_at', '>', $deletedAt);
        }

        $messages = $messagesQuery->get();

        $activeConversation->participants()
            ->updateExistingPivot($user->id, [
                'last_read_at' => now(),
            ]);

        return response()->json([
            'ok' => true,
            'conversation' => $this->conversationJson($activeConversation, $user),
            'messages' => $messages->map(fn (Message $m) => $this->messageJson($m, $user))->values(),
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $this->touchUserSeen($user);

        $search = trim($request->input('q', ''));
        $activeConversationId = $request->input('conversation');

        $conversations = $this->filterConversationsForUser(
            $this->buildConversationsQuery($user, $search)->get(),
            $user
        );

        $contacts = $this->loadContacts($user, $search);

        $activeConversation = null;
        $messages = collect();

        if ($activeConversationId) {
            $activeConversation = Conversation::with(['participants', 'messages.user'])
                ->whereHas('participants', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->find($activeConversationId);

            if ($activeConversation) {
                $pivot = $activeConversation->participants
                    ->firstWhere('id', $user->id)
                    ?->pivot;

                $deletedAt = $pivot?->deleted_at;

                $messagesQuery = $activeConversation->messages()
                    ->with('user')
                    ->orderBy('created_at');

                if ($deletedAt) {
                    $messagesQuery->where('created_at', '>', $deletedAt);
                }

                $messages = $messagesQuery->get();

                $activeConversation->participants()
                    ->updateExistingPivot($user->id, [
                        'last_read_at' => now(),
                    ]);
            }
        }

        $convPlaceholder = 999999998;
        $threadPlaceholder = 999999997;
        $destroyPlaceholder = 999999996;

        return view($this->inboxView(), array_merge([
            'user' => $user,
            'conversations' => $conversations,
            'contacts' => $contacts,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'search' => $search,
            'routeGroup' => $this->messageRouteGroup(),
            'pollUrl' => $this->pollUrl(),
            'sendUrlTemplate' => $this->sendUrlJsTemplate(),
            'conversationUrlTemplate' => str_replace((string) $convPlaceholder, '__CONV__', route($this->messageRouteGroup(), ['conversation' => $convPlaceholder])),
            'threadUrlTemplate' => str_replace((string) $threadPlaceholder, '__CONV__', route($this->messageRouteGroup().'.thread', ['conversation' => $threadPlaceholder])),
            'destroyUrlTemplate' => str_replace((string) $destroyPlaceholder, '__CONV__', route($this->messageRouteGroup().'.destroy', ['conversation' => $destroyPlaceholder])),
            'messagesIndexUrl' => route($this->messageRouteGroup()),
            'currentUserId' => $user->id,
            'pollSince' => time(),
        ], $this->extraInboxViewData($user)));
    }

    /**
     * Extra variables passed to the inbox Blade view (override in subclass).
     *
     * @return array<string, mixed>
     */
    protected function extraInboxViewData(User $user): array
    {
        return [];
    }

    /**
     * Augment JSON for each conversation in poll responses (override in subclass).
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function decorateConversationJson(array $payload, Conversation $conversation, User $viewer): array
    {
        return $payload;
    }

    public function start(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $otherUserId = (int) $request->input('user_id');

        if ($otherUserId === $user->id) {
            return back();
        }

        $conversation = Conversation::where('is_group', false)
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->whereHas('participants', function ($q) use ($otherUserId) {
                $q->where('users.id', $otherUserId);
            })
            ->first();

        if (! $conversation) {
            $conversation = DB::transaction(function () use ($user, $otherUserId) {
                $conv = Conversation::create([
                    'title' => null,
                    'is_group' => false,
                ]);

                $conv->participants()->attach([$user->id, $otherUserId]);

                return $conv;
            });
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'conversation_id' => $conversation->id,
            ]);
        }

        return redirect()->to($this->indexRoute([
            'conversation' => $conversation->id,
        ]));
    }

    public function send(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        if (! $conversation->participants()->where('users.id', $user->id)->exists()) {
            abort(403);
        }

        $data = $request->validate([
            'body' => 'required|string|max:4000',
        ]);

        /** @var Message $message */
        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);

        $message->load('user');

        $user->setAttribute('last_seen_at', now());
        $user->save();

        $conversation->participants()
            ->updateExistingPivot($user->id, [
                'last_read_at' => now(),
            ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => $this->messageJson($message, $user),
            ]);
        }

        return redirect()->to($this->indexRoute([
            'conversation' => $conversation->id,
        ]));
    }

    public function destroy(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        if (! $conversation->participants()->where('users.id', $user->id)->exists()) {
            abort(403);
        }

        $conversation->participants()
            ->updateExistingPivot($user->id, [
                'deleted_at' => now(),
                'last_read_at' => now(),
            ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'cleared_conversation' => true]);
        }

        return redirect()->to($this->indexRoute([]))
            ->with('status', 'Conversation cleared for you.');
    }

    public function poll(Request $request)
    {
        return response()->json($this->pollPayload($request));
    }
}
