<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentMessagesController extends Controller
{
    /**
     * Show party chat screen.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // ✅ update "last_seen_at" so we can show online/last active
        if (! $user->last_seen_at || $user->last_seen_at->lt(now()->subMinute())) {
            $user->forceFill([
                'last_seen_at' => now(),
            ])->save();
        }

        $search = trim($request->input('q', ''));
        $activeConversationId = $request->input('conversation');

        // 1) Conversations that this user is part of
        $conversationsQuery = $user->conversations()
            ->with([
                'participants',       // all participants (with pivot deleted_at)
                'lastMessage.user',   // last message + sender
            ])
            ->withCount('messages')
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->take(1)
            );

        // Filter conversations by search (title or other user’s name)
        if ($search !== '') {
            $conversationsQuery->where(function ($q) use ($search, $user) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhereHas('participants', function ($q) use ($search, $user) {
                      $q->where('users.id', '!=', $user->id)
                        ->where('users.name', 'like', '%' . $search . '%');
                  });
            });
        }

        $conversations = $conversationsQuery->get();

        // ✅ Messenger-like behavior:
        // If THIS user cleared the conversation, hide it from their list
        // until there's a new message after deleted_at.
        $conversations = $conversations->filter(function (Conversation $conversation) use ($user) {
            $pivot = $conversation->participants
                ->firstWhere('id', $user->id)
                ?->pivot;

            if (! $pivot) {
                return false;
            }

            // Never deleted => always visible
            if (is_null($pivot->deleted_at)) {
                return true;
            }

            $last = $conversation->lastMessage;
            if (! $last) {
                // cleared and no messages at all => hide
                return false;
            }

            // Show only if last message is newer than clear time
            return $last->created_at->gt($pivot->deleted_at);
        })->values(); // reset indexes

        // 2) Contacts list: ALL other users (students + teachers)
        $contactsQuery = User::query()
            ->where('id', '!=', $user->id);

        if ($search !== '') {
            $contactsQuery->where('name', 'like', '%' . $search . '%');
        }

        $contacts = $contactsQuery
            ->orderBy('name')
            ->limit(50)
            ->get();

        // 3) Active conversation + messages (if one is selected)
        $activeConversation = null;
        $messages           = collect();

        if ($activeConversationId) {
            $activeConversation = Conversation::with(['participants', 'messages.user'])
                ->whereHas('participants', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->find($activeConversationId);

            if ($activeConversation) {
                // get pivot for THIS user
                $pivot = $activeConversation->participants
                    ->firstWhere('id', $user->id)
                    ?->pivot;

                $deletedAt = $pivot?->deleted_at;

                $messagesQuery = $activeConversation->messages()
                    ->with('user')
                    ->orderBy('created_at');

                // ✅ show ONLY messages sent after user cleared/deleted the convo
                if ($deletedAt) {
                    $messagesQuery->where('created_at', '>', $deletedAt);
                }

                $messages = $messagesQuery->get();

                // ✅ mark visible messages as read for this user
                $activeConversation->participants()
                    ->updateExistingPivot($user->id, [
                        'last_read_at' => now(),
                        // DON'T touch deleted_at here – keep the clear point
                    ]);
            }
        }

        return view('student.messages.index', [
            'user'               => $user,
            'conversations'      => $conversations,
            'contacts'           => $contacts,
            'activeConversation' => $activeConversation,
            'messages'           => $messages,
            'search'             => $search,
        ]);
    }

    /**
     * Start (or reuse) a 1-to-1 conversation with another user.
     *
     * Messenger-style:
     *  - we REUSE the same conversation row
     *  - old messages before deleted_at stay hidden for THIS user
     */
    public function start(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user        = Auth::user();
        $otherUserId = (int) $request->input('user_id');

        // Prevent chatting with self
        if ($otherUserId === $user->id) {
            return back();
        }

        // Try to find existing 1-to-1 conversation (even if one or both cleared it)
        $conversation = Conversation::where('is_group', false)
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->whereHas('participants', function ($q) use ($otherUserId) {
                $q->where('users.id', $otherUserId);
            })
            ->first();

        // If none, create new
        if (! $conversation) {
            $conversation = DB::transaction(function () use ($user, $otherUserId) {
                $conv = Conversation::create([
                    'title'    => null,
                    'is_group' => false,
                ]);

                $conv->participants()->attach([$user->id, $otherUserId]);

                return $conv;
            });
        }

        // IMPORTANT:
        // We DO NOT reset deleted_at here.
        // It stays as the "clear point", so this user will only see
        // messages created AFTER the last time they cleared the convo.

        return redirect()->route('student.messages', [
            'conversation' => $conversation->id,
        ]);
    }

    /**
     * Send a message in a conversation.
     */
    public function send(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        // Make sure user is part of this conversation
        if (! $conversation->participants()->where('users.id', $user->id)->exists()) {
            abort(403);
        }

        $data = $request->validate([
            'body' => 'required|string|max:4000',
        ]);

        $conversation->messages()->create([
            'user_id' => $user->id,
            'body'    => $data['body'],
        ]);

        // bump last_seen_at on send as well (user is clearly active)
        $user->forceFill([
            'last_seen_at' => now(),
        ])->save();

        // mark last_read_at for this user (do not touch deleted_at)
        $conversation->participants()
            ->updateExistingPivot($user->id, [
                'last_read_at' => now(),
            ]);

        return redirect()->route('student.messages', [
            'conversation' => $conversation->id,
        ]);
    }

    /**
     * "Delete" a conversation only for THIS user (Messenger-style clear).
     *
     * - We DO NOT delete the conversation row.
     * - We only store the time this user cleared it in the pivot.
     * - User won't see old messages anymore; other user still can.
     */
    public function destroy(Conversation $conversation)
    {
        $user = Auth::user();

        if (! $conversation->participants()->where('users.id', $user->id)->exists()) {
            abort(403);
        }

        // 👇 store clear point only for THIS user
        $conversation->participants()
            ->updateExistingPivot($user->id, [
                'deleted_at'   => now(),  // clear/hidden from this point
                'last_read_at' => now(),
            ]);

        return redirect()->route('student.messages')
            ->with('status', 'Conversation cleared for you.');
    }
}
