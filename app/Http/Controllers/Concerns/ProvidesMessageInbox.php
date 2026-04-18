<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait ProvidesMessageInbox
{
    /**
     * Narrow the contacts list (e.g. teachers: students + teachers only).
     */
    protected function scopeInboxContacts(Builder $query): Builder
    {
        return $query;
    }

    protected function touchUserSeen(User $user): void
    {
        if (! $user->last_seen_at || $user->last_seen_at->lt(now()->subMinute())) {
            $user->forceFill(['last_seen_at' => now()])->save();
        }
    }

    protected function buildConversationsQuery(User $user, string $search): Builder
    {
        $conversationsQuery = $user->conversations()
            ->with([
                'participants',
                'lastMessage.user',
            ])
            ->withCount('messages')
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->take(1)
            );

        if ($search !== '') {
            $conversationsQuery->where(function ($q) use ($search, $user) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhereHas('participants', function ($q) use ($search, $user) {
                        $q->where('users.id', '!=', $user->id)
                            ->where('users.name', 'like', '%'.$search.'%');
                    });
            });
        }

        return $conversationsQuery;
    }

    protected function filterConversationsForUser(Collection $conversations, User $user): Collection
    {
        return $conversations->filter(function (Conversation $conversation) use ($user) {
            $pivot = $conversation->participants
                ->firstWhere('id', $user->id)
                ?->pivot;

            if (! $pivot) {
                return false;
            }

            if (is_null($pivot->deleted_at)) {
                return true;
            }

            $last = $conversation->lastMessage;
            if (! $last) {
                return false;
            }

            return $last->created_at->gt($pivot->deleted_at);
        })->values();
    }

    protected function loadContacts(User $user, string $search): Collection
    {
        $contactsQuery = $this->scopeInboxContacts(
            User::query()->where('id', '!=', $user->id)
        );

        if ($search !== '') {
            $contactsQuery->where('name', 'like', '%'.$search.'%');
        }

        return $contactsQuery
            ->orderBy('name')
            ->limit(50)
            ->get();
    }

    protected function conversationJson(Conversation $conversation, User $user): array
    {
        $other = $conversation->participants
            ->firstWhere('id', '!=', $user->id) ?? $user;

        $unread = $conversation->unreadCountFor($user);
        $last = $conversation->lastMessage;
        $lastTime = $last?->created_at?->diffForHumans(null, true) ?? '';
        $preview = $last
            ? (($last->user_id === $user->id ? 'You: ' : $last->user->name.': ')
                .\Illuminate\Support\Str::limit($last->body, 40))
            : 'No messages yet';

        return [
            'id' => $conversation->id,
            'other' => [
                'id' => $other->id,
                'name' => $other->name,
                'role' => $other->role,
                'online' => $other->isOnline(),
                'pic' => $other->profile_pic ?? 'default-pp.png',
            ],
            'unread' => $unread,
            'preview' => $preview,
            'last_time' => $lastTime,
            'last_ts' => $last?->created_at?->timestamp,
        ];
    }

    protected function messageJson(Message $message, User $viewer): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'user_id' => $message->user_id,
            'user_name' => $message->user->name,
            'is_me' => (int) $message->user_id === (int) $viewer->id,
            'created_at' => $message->created_at->toIso8601String(),
            'created_label' => $message->created_at->format('M d, h:i a'),
        ];
    }

    protected function pollPayload(Request $request): array
    {
        $user = Auth::user();
        $this->touchUserSeen($user);

        $search = trim($request->input('q', ''));
        $conversationId = $request->input('conversation');
        $afterId = max(0, (int) $request->input('after_id', 0));
        $sinceTs = max(0, (int) $request->input('since', 0));

        $conversations = $this->filterConversationsForUser(
            $this->buildConversationsQuery($user, $search)->get(),
            $user
        );

        $payload = [
            'ok' => true,
            'conversations' => $conversations->map(fn (Conversation $c) => $this->conversationJson($c, $user))->values(),
        ];

        if (! $conversationId) {
            return $payload;
        }

        $activeConversation = Conversation::with(['participants', 'messages.user'])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->find($conversationId);

        if (! $activeConversation) {
            $payload['messages'] = [];
            $payload['cleared_conversation'] = true;

            return $payload;
        }

        $pivot = $activeConversation->participants
            ->firstWhere('id', $user->id)
            ?->pivot;

        $deletedAt = $pivot?->deleted_at;

        $messagesQuery = $activeConversation->messages()
            ->with('user')
            ->orderBy('id');

        if ($deletedAt) {
            $messagesQuery->where('created_at', '>', $deletedAt);
        }

        if ($afterId > 0) {
            $messagesQuery->where('id', '>', $afterId);
        } elseif ($sinceTs > 0) {
            $messagesQuery->where('created_at', '>', Carbon::createFromTimestamp($sinceTs));
        } else {
            $messagesQuery->whereRaw('1 = 0');
        }

        $messages = $messagesQuery->get();

        $activeConversation->participants()
            ->updateExistingPivot($user->id, [
                'last_read_at' => now(),
            ]);

        $payload['messages'] = $messages->map(fn (Message $m) => $this->messageJson($m, $user))->values();

        return $payload;
    }
}
