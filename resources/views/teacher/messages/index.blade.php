@extends('teacher.layouts.app')

@section('title', 'Messages')
@section('page-title', 'Messages')

@section('content')
@php
    use Illuminate\Support\Str;
    $myStudentIds = $myStudentIds ?? [];
    $myClassStudentIds = $myClassStudentIds ?? [];
    $myStudentsCount = $myStudentsCount ?? 0;
    $myClassCount = $myClassCount ?? 0;
@endphp

<style>
    .teacher-inbox-scope {
        --inbox-sidebar: #f1f5f9;
        --inbox-sidebar-border: #e2e8f0;
        --inbox-ink: #1e293b;
        --inbox-muted: #64748b;
        --accent-strong: #d97706;
    }

    .teacher-inbox-page .card {
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .teacher-inbox-page .inbox-card-body {
        padding: 0;
    }

    .messages-wrapper {
        display: grid;
        grid-template-columns: minmax(260px, 300px) minmax(0, 1fr);
        gap: 0;
        min-height: 580px;
    }

    .messages-sidebar {
        background: var(--inbox-sidebar);
        border-right: 1px solid var(--inbox-sidebar-border);
        display: flex;
        flex-direction: column;
        padding: 16px 14px;
        position: relative;
    }

    .messages-sidebar-header {
        margin-bottom: 12px;
    }

    .messages-sidebar-header h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--inbox-ink);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    .messages-sidebar-header h3 i {
        color: var(--primary);
    }

    .messages-sidebar-header .inbox-sub {
        font-size: 0.8rem;
        color: var(--inbox-muted);
        line-height: 1.35;
    }

    .roster-stat {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(79, 70, 229, 0.08);
        color: var(--primary);
        font-size: 0.75rem;
        font-weight: 600;
    }

    .roster-hint {
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: var(--radius-sm);
        background: #fffbeb;
        border: 1px solid #fde68a;
        font-size: 0.78rem;
        color: #92400e;
        line-height: 1.4;
    }

    .roster-hint a {
        color: var(--primary);
        font-weight: 600;
    }

    .messages-search {
        position: relative;
        margin-bottom: 10px;
    }

    .messages-search input {
        width: 100%;
        padding: 9px 12px 9px 36px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        outline: none;
        font-size: 0.85rem;
        background: #fff;
        color: var(--text-primary);
    }

    .messages-search i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .party-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 10px;
    }

    .party-filter button {
        flex: 1;
        min-width: 0;
        border-radius: 999px;
        border: 1px solid var(--border);
        padding: 7px 8px;
        font-size: 0.72rem;
        font-weight: 600;
        cursor: pointer;
        background: #fff;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }

    .party-filter button.active {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .party-filter button i {
        font-size: 0.75rem;
    }

    .messages-list {
        flex: 1;
        margin-top: 4px;
        overflow-y: auto;
        padding-right: 4px;
        min-height: 120px;
    }

    .messages-list::-webkit-scrollbar {
        width: 5px;
    }

    .messages-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .chat-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 8px;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: background 0.15s ease;
        position: relative;
        text-decoration: none;
        color: inherit;
        border: 1px solid transparent;
    }

    .chat-item:hover {
        background: #fff;
        border-color: var(--border);
    }

    .chat-item.active {
        background: #eef2ff;
        border-color: #c7d2fe;
        box-shadow: var(--shadow);
    }

    .chat-item.active .chat-name {
        color: var(--primary-dark);
    }

    .chat-avatar,
    .thread-avatar {
        position: relative;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        overflow: visible;
        flex-shrink: 0;
        background: #e2e8f0;
    }

    .chat-avatar img,
    .thread-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: var(--shadow);
    }

    .chat-status-dot {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid var(--inbox-sidebar);
    }

    .chat-status-dot.online {
        background: var(--success);
    }

    .chat-status-dot.offline {
        background: #94a3b8;
    }

    .chat-text {
        flex: 1;
        min-width: 0;
    }

    .chat-name {
        font-size: 0.86rem;
        font-weight: 600;
        color: var(--inbox-ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .role-tag {
        font-size: 0.62rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 2px 6px;
        border-radius: 999px;
        background: rgba(245, 158, 11, 0.2);
        color: #b45309;
        font-weight: 700;
    }

    .role-tag.class-tag {
        background: rgba(79, 70, 229, 0.12);
        color: var(--primary-dark);
    }

    .chat-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .chat-last {
        font-size: 0.75rem;
        color: var(--inbox-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-time {
        font-size: 0.68rem;
        color: #94a3b8;
        flex-shrink: 0;
    }

    .chat-unread-badge {
        position: absolute;
        right: 6px;
        top: 6px;
        background: var(--accent);
        color: #0f172a;
        font-size: 0.65rem;
        min-width: 18px;
        height: 18px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        padding: 0 4px;
    }

    .contacts-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--inbox-muted);
        margin: 4px 2px 6px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .contact-start-form button {
        border-radius: 999px;
        border: none;
        padding: 5px 11px;
        font-size: 0.72rem;
        font-weight: 600;
        cursor: pointer;
        background: var(--primary);
        color: #fff;
        white-space: nowrap;
        transition: background 0.15s;
    }

    .contact-start-form button:hover {
        background: var(--primary-dark);
    }

    .contacts-search-panel {
        position: absolute;
        left: 14px;
        right: 14px;
        top: 118px;
        background: #fff;
        border-radius: var(--radius-sm);
        padding: 8px 6px 6px;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        max-height: 280px;
        overflow-y: auto;
        display: none;
        z-index: 30;
    }

    .messages-main {
        background: var(--bg-main);
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .messages-main-inner {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 16px 18px 14px;
        min-height: 580px;
    }

    .messages-main-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
    }

    .thread-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .thread-avatar {
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.25);
    }

    .thread-info h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    .badge-role {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 3px 8px;
        border-radius: 999px;
        background: #e0e7ff;
        color: var(--primary-dark);
        font-weight: 700;
    }

    .thread-info .status-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-top: 4px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: var(--success);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
    }

    .thread-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .thread-actions button {
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        padding: 6px 10px;
        font-size: 0.78rem;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        color: var(--text-primary);
    }

    .thread-actions button:hover {
        background: var(--bg-main);
    }

    .thread-actions button.danger {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .messages-thread {
        flex: 1;
        margin-top: 4px;
        padding: 12px 4px;
        overflow-y: auto;
        background: #fff;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        min-height: 280px;
    }

    .messages-thread::-webkit-scrollbar {
        width: 6px;
    }

    .messages-thread::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .msg-row {
        display: flex;
        margin-bottom: 10px;
    }

    .msg-row.me {
        justify-content: flex-end;
    }

    .msg-bubble {
        max-width: 72%;
        padding: 10px 12px;
        border-radius: 14px;
        font-size: 0.86rem;
        line-height: 1.45;
        box-shadow: var(--shadow);
    }

    .msg-row.me .msg-bubble {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .msg-row.them .msg-bubble {
        background: #f1f5f9;
        color: var(--text-primary);
        border-bottom-left-radius: 4px;
        border: 1px solid var(--border);
    }

    .msg-meta {
        font-size: 0.7rem;
        margin-top: 4px;
        text-align: right;
        opacity: 0.85;
    }

    .msg-row.me .msg-meta {
        color: rgba(255, 255, 255, 0.9);
    }

    .msg-row.them .msg-meta {
        color: var(--text-muted);
    }

    .empty-thread {
        font-size: 0.88rem;
        color: var(--text-secondary);
        text-align: center;
        padding: 48px 16px;
        line-height: 1.5;
    }

    .messages-input-bar {
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-top: 12px;
        border-top: 1px solid var(--border);
    }

    .messages-tools button {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: #fff;
        color: var(--text-secondary);
    }

    .messages-input-wrapper {
        flex: 1;
        position: relative;
    }

    .messages-input-wrapper input {
        width: 100%;
        border-radius: 999px;
        border: 1px solid var(--border);
        padding: 10px 96px 10px 16px;
        font-size: 0.88rem;
        outline: none;
        background: #fff;
    }

    .messages-input-wrapper input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    .messages-send-btn {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        border-radius: 999px;
        border: none;
        padding: 7px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        background: var(--accent);
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .messages-send-btn:hover {
        filter: brightness(1.05);
    }

    @media (max-width: 992px) {
        .messages-wrapper {
            grid-template-columns: 1fr 1.2fr;
        }
    }

    @media (max-width: 768px) {
        .messages-wrapper {
            grid-template-columns: 1fr;
        }

        .messages-sidebar {
            border-right: none;
            border-bottom: 1px solid var(--inbox-sidebar-border);
        }

        .contacts-search-panel {
            position: static;
            margin-top: 8px;
            box-shadow: var(--shadow);
        }

        .messages-main-inner {
            min-height: 420px;
        }
    }
</style>

<div class="teacher-inbox-scope teacher-inbox-page">
    <div class="card inbox-card">
        <div class="inbox-card-body">
            <div class="messages-wrapper">

                <div class="messages-sidebar">
                    <div class="messages-sidebar-header">
                        <h3>
                            <i class="fas fa-comments"></i>
                            Inbox
                        </h3>
                        <p class="inbox-sub">
                            Message the same <strong>students</strong> you see in reports (scores), plus colleagues.
                        </p>
                        <div class="roster-stat">
                            <i class="fas fa-user-graduate"></i>
                            <span>{{ $myStudentsCount }} students · {{ $myClassCount }} linked to your content</span>
                        </div>
                        @if($myStudentsCount === 0)
                            <div class="roster-hint">
                                                               <strong>No student accounts yet.</strong> When students register, they will show here the same way they appear in
                                <a href="{{ route('teacher.reports.scores') }}">Reports</a>— add students via
                                <a href="{{ route('teacher.registration') }}">Registration</a>.
                            </div>
                        @elseif($myClassCount === 0)
                            <div class="roster-hint" style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;">
                                <strong>Tip:</strong> Everyone listed under “Start a chat” is messageable. The <strong>My class</strong> filter highlights students who share a grade/section on your lessons/quizzes/quests or have taken your quizzes/quests.
                                <a href="{{ route('teacher.lessons.create') }}">Add targeted content</a>
                            </div>
                        @endif
                    </div>

                    <form class="messages-search" method="GET" action="{{ route($routeGroup) }}">
                        <i class="fas fa-search"></i>
                        <input
                            type="text"
                            name="q"
                            id="messages-search-input"
                            placeholder="Search name…"
                            autocomplete="off"
                            value="{{ $search ?? '' }}"
                        >
                    </form>

                    <div id="contacts-search-panel" class="contacts-search-panel">
                        <div class="contacts-label">Start a chat</div>

                        @forelse($contacts as $contact)
                            @php
                                $online = $contact->isOnline();
                                $isMyClass = $contact->role === 'student' && in_array($contact->id, $myClassStudentIds, true);
                            @endphp
                            <div class="chat-item chat-filter-item"
                                 data-role="{{ $contact->role }}"
                                 data-my-class="{{ $isMyClass ? '1' : '0' }}">
                                <div class="chat-avatar">
                                    <img src="{{ asset('images/' . ($contact->profile_pic ?? 'default-pp.png')) }}"
                                         alt="{{ $contact->name }}">
                                    <span class="chat-status-dot {{ $online ? 'online' : 'offline' }}"></span>
                                </div>
                                <div class="chat-text">
                                    <div class="chat-name">
                                        {{ $contact->name }}
                                        @if($contact->role === 'teacher')
                                            <span class="role-tag">Teacher</span>
                                        @elseif($isMyClass)
                                            <span class="role-tag class-tag">My class</span>
                                        @endif
                                    </div>
                                    <div class="chat-meta-row">
                                        <div class="chat-last">
                                            {{ $contact->role === 'teacher' ? 'Colleague' : 'Student' }}
                                        </div>
                                    </div>
                                </div>

                                <form method="POST"
                                      action="{{ route($routeGroup.'.start') }}"
                                      class="contact-start-form js-contact-start">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $contact->id }}">
                                    <button type="submit">Chat</button>
                                </form>
                            </div>
                        @empty
                            <div style="font-size:0.8rem; color: var(--inbox-muted); padding:8px 6px;">
                                No students or colleagues match your search. Try clearing the search box.
                            </div>
                        @endforelse
                    </div>

                    <div class="party-filter">
                        <button type="button" class="active filter-btn" data-filter="all">
                            <i class="fas fa-layer-group"></i> All
                        </button>
                        <button type="button" class="filter-btn" data-filter="my-class">
                            <i class="fas fa-chalkboard-teacher"></i> My class
                        </button>
                        <button type="button" class="filter-btn" data-filter="teacher">
                            <i class="fas fa-user-tie"></i> Colleagues
                        </button>
                    </div>

                    <div class="messages-list" id="messages-list">
                        @forelse($conversations as $conversation)
                            @php
                                $other = $conversation->participants
                                    ->firstWhere('id', '!=', $user->id) ?? $user;

                                $unread   = $conversation->unreadCountFor($user);
                                $isActive = optional($activeConversation)->id === $conversation->id;
                                $online   = $other->isOnline();
                                $last     = $conversation->lastMessage;
                                $lastTime = $last?->created_at?->diffForHumans(null, true) ?? '';
                                $preview  = $last
                                    ? (($last->user_id === $user->id ? 'You: ' : $last->user->name . ': ')
                                       . Str::limit($last->body, 40))
                                    : 'No messages yet';
                                $isMyClass = $other->role === 'student' && in_array($other->id, $myClassStudentIds, true);
                            @endphp

                            <a href="{{ route($routeGroup, ['conversation' => $conversation->id]) }}"
                               class="chat-item chat-filter-item {{ $isActive ? 'active' : '' }}"
                               data-conv-id="{{ $conversation->id }}"
                               data-role="{{ $other->role }}"
                               data-my-class="{{ $isMyClass ? '1' : '0' }}">
                                <div class="chat-avatar">
                                    <img src="{{ asset('images/' . ($other->profile_pic ?? 'default-pp.png')) }}"
                                         alt="{{ $other->name }}">
                                    <span class="chat-status-dot {{ $online ? 'online' : 'offline' }}"></span>
                                </div>

                                <div class="chat-text">
                                    <div class="chat-name">
                                        {{ $other->name }}
                                        @if($other->role === 'teacher')
                                            <span class="role-tag">Teacher</span>
                                        @elseif($isMyClass)
                                            <span class="role-tag class-tag">My class</span>
                                        @endif
                                    </div>
                                    <div class="chat-meta-row">
                                        <div class="chat-last">{{ $preview }}</div>
                                        <div class="chat-time">{{ $lastTime }}</div>
                                    </div>
                                </div>

                                @if($unread > 0)
                                    <span class="chat-unread-badge">
                                        {{ $unread > 9 ? '9+' : $unread }}
                                    </span>
                                @endif
                            </a>
                        @empty
                            <div style="font-size:0.82rem; color: var(--inbox-muted); padding:8px 4px; line-height:1.45;">
                                No conversations yet. Focus the search box to open contacts, or visit your roster above.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="messages-main">
                    <div class="messages-main-inner">
                        <div id="messages-main-panel">
                        @if($activeConversation)
                            @php
                                $other  = $activeConversation->participants
                                    ->firstWhere('id', '!=', $user->id) ?? $user;
                                $online = $other->isOnline();
                                $isMyClassThread = $other->role === 'student' && in_array($other->id, $myClassStudentIds, true);
                            @endphp

                            <div class="messages-main-header">
                                <div class="thread-user">
                                    <div class="thread-avatar">
                                        <img src="{{ asset('images/' . ($other->profile_pic ?? 'default-pp.png')) }}"
                                             alt="{{ $other->name }}">
                                    </div>
                                    <div class="thread-info">
                                        <h3>
                                            {{ $other->name }}
                                            <span class="badge-role">
                                                @if($other->role === 'teacher')
                                                    Teacher
                                                @else
                                                    Student
                                                @endif
                                            </span>
                                            @if($isMyClassThread)
                                                <span class="badge-role" style="background:#fef3c7;color:#92400e;">My class</span>
                                            @endif
                                        </h3>
                                        <div class="status-row">
                                            <span class="status-dot"></span>
                                            <span>
                                                {{ $other->role === 'teacher'
                                                    ? 'Colleague'
                                                    : ($isMyClassThread ? 'Student in your roster' : 'Student (outside current roster)') }}
                                                @if($online)
                                                    · Online
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="thread-actions">
                                    <button type="button" class="pin" title="Coming soon">
                                        <i class="fas fa-thumbtack"></i> Pin
                                    </button>
                                    <form method="POST"
                                          action="{{ route($routeGroup.'.destroy', $activeConversation) }}"
                                          onsubmit="return confirm('Clear this conversation from your inbox?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="danger" title="Clear conversation">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="messages-thread" id="messages-thread">
                                @forelse($messages as $message)
                                    @php $isMe = $message->user_id === $user->id; @endphp
                                    <div class="msg-row {{ $isMe ? 'me' : 'them' }}" data-message-id="{{ $message->id }}">
                                        <div class="msg-bubble">
                                            {{ $message->body }}
                                            <div class="msg-meta">
                                                {{ $isMe ? 'You' : $message->user->name }}
                                                · {{ $message->created_at->format('M d, h:i a') }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-thread">
                                        Say hello — keep communication clear and supportive.
                                    </div>
                                @endforelse
                            </div>

                            <form method="POST"
                                  id="message-send-form"
                                  action="{{ route($routeGroup.'.send', $activeConversation) }}"
                                  class="messages-input-bar">
                                @csrf
                                <div class="messages-tools">
                                    <button type="button" title="Attach (coming soon)" disabled style="opacity:0.45;cursor:not-allowed;">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                </div>

                                <div class="messages-input-wrapper">
                                    <input type="text"
                                           name="body"
                                           placeholder="Write a message…"
                                           autocomplete="off"
                                           required>
                                    <button class="messages-send-btn" type="submit">
                                        <i class="fas fa-paper-plane"></i>
                                        Send
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="empty-thread" style="flex:1; display:flex; align-items:center; justify-content:center;">
                                <div>
                                    <p style="font-weight:600; color:var(--text-primary); margin-bottom:8px;">Select a conversation</p>
                                    <p>Choose someone on the left, or search to start a new chat with your class or a colleague.</p>
                                </div>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@php $__teacherMessagesBoot = [
        'pollUrl' => $pollUrl,
        'sendTpl' => $sendUrlTemplate,
        'convUrlTpl' => $conversationUrlTemplate,
        'threadTpl' => $threadUrlTemplate,
        'destroyTpl' => $destroyUrlTemplate,
        'indexUrl' => $messagesIndexUrl,
        'csrf' => csrf_token(),
        'imagesBase' => asset('images/'),
        'conversationId' => optional($activeConversation)->id,
        'lastMessageId' => (int) ($messages->max('id') ?? 0),
        'pollSince' => (int) ($pollSince ?? 0),
    ];
@endphp
<script type="application/json" id="teacher-messages-boot">{!! json_encode($__teacherMessagesBoot) !!}</script>
<script>
(function () {
    const inboxScope    = '.teacher-inbox-page';
    const searchInput   = document.getElementById('messages-search-input');
    const searchForm    = document.querySelector('.teacher-inbox-page .messages-search');
    const contactsPanel = document.getElementById('contacts-search-panel');
    let activeFilter    = 'all';

    function getChatItems() {
        return document.querySelectorAll('.messages-list .chat-filter-item');
    }

    window.__messagesApplyFilter = function applyFilter() {
        getChatItems().forEach(function (item) {
            const role = (item.dataset.role || '').toLowerCase();
            const myClass = item.dataset.myClass === '1';
            let show = true;
            if (activeFilter === 'teacher') {
                show = role === 'teacher';
            } else if (activeFilter === 'my-class') {
                show = role === 'student' && myClass;
            }
            item.style.display = show ? '' : 'none';
        });
    };

    document.addEventListener('click', function (e) {
        const wrap = e.target.closest('.messages-wrapper');
        if (!wrap || !wrap.closest(inboxScope)) return;
        const btn = e.target.closest('.filter-btn');
        if (!btn) return;
        wrap.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        activeFilter = btn.dataset.filter || 'all';
        window.__messagesApplyFilter();
    });

    const bootEl = document.getElementById('teacher-messages-boot');
    const boot = bootEl ? JSON.parse(bootEl.textContent) : {};
    const pollUrl = boot.pollUrl || '';
    const sendTpl = boot.sendTpl || '';
    const convUrlTpl = boot.convUrlTpl || '';
    const threadTpl = boot.threadTpl || '';
    const destroyTpl = boot.destroyTpl || '';
    const indexUrl = boot.indexUrl || '';
    const csrf = boot.csrf || '';
    const imagesBase = boot.imagesBase || '';
    let conversationId = boot.conversationId;
    let lastMessageId = boot.lastMessageId || 0;
    let threadSince = (conversationId && lastMessageId === 0) ? (boot.pollSince || 0) : 0;

    function buildConvUrl(id) {
        return convUrlTpl.replace('__CONV__', String(id));
    }
    function buildSendUrl(id) {
        return sendTpl.replace('__CONV__', String(id));
    }
    function buildThreadUrl(id) {
        return threadTpl.replace('__CONV__', String(id));
    }
    function buildDestroyUrl(id) {
        return destroyTpl.replace('__CONV__', String(id));
    }

    function pushConversationUrl(id) {
        try {
            const u = new URL(indexUrl || window.location.pathname, window.location.origin);
            if (id) {
                u.searchParams.set('conversation', String(id));
            } else {
                u.searchParams.delete('conversation');
            }
            history.pushState({ teacherConv: id || null }, '', u.pathname + u.search);
        } catch (err) {
            console.warn('pushState', err);
        }
    }

    function setSidebarActive(id) {
        document.querySelectorAll('#messages-list .chat-item').forEach(function (a) {
            const cid = a.dataset.convId;
            a.classList.toggle('active', id != null && String(cid) === String(id));
        });
    }

    function escapeHtml(s) {
        if (s == null) return '';
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function buildTeacherThreadHtml(conv, messages) {
        const o = conv.other;
        const myClass = conv.is_my_class_student === true || conv.is_my_class_student === 1;
        const pic = imagesBase + (o.pic || 'default-pp.png');
        let roleBadges = '<span class="badge-role">' + (o.role === 'teacher' ? 'Teacher' : 'Student') + '</span>';
        if (myClass && o.role !== 'teacher') {
            roleBadges += '<span class="badge-role" style="background:#fef3c7;color:#92400e;">My class</span>';
        }
        const statusText = o.role === 'teacher' ? 'Colleague' : (myClass ? 'Student in your roster' : 'Student (outside current roster)');
        const onlineExtra = o.online ? ' · Online' : '';
        const sendUrl = buildSendUrl(conv.id);
        let msgs = '';
        messages.forEach(function (m) {
            const isMe = m.is_me === true || m.is_me === 1 || m.is_me === '1';
            msgs += '<div class="msg-row ' + (isMe ? 'me' : 'them') + '" data-message-id="' + m.id + '"><div class="msg-bubble">' +
                escapeHtml(m.body) + '<div class="msg-meta">' + escapeHtml(isMe ? 'You' : (m.user_name || '')) +
                ' · ' + escapeHtml(m.created_label) + '</div></div></div>';
        });
        if (!messages.length) {
            msgs = '<div class="empty-thread">Say hello — keep communication clear and supportive.</div>';
        }
        return (
            '<div class="messages-main-header">' +
            '<div class="thread-user">' +
            '<div class="thread-avatar"><img src="' + escapeHtml(pic) + '" alt=""></div>' +
            '<div class="thread-info"><h3>' + escapeHtml(o.name) + ' ' + roleBadges + '</h3>' +
            '<div class="status-row"><span class="status-dot"></span><span>' + escapeHtml(statusText) + onlineExtra + '</span></div></div></div>' +
            '<div class="thread-actions">' +
            '<button type="button" class="pin" title="Coming soon"><i class="fas fa-thumbtack"></i> Pin</button>' +
            '<button type="button" class="danger js-clear-thread" title="Clear conversation"><i class="fas fa-trash-alt"></i></button></div></div>' +
            '<div class="messages-thread" id="messages-thread">' + msgs + '</div>' +
            '<form method="POST" id="message-send-form" class="messages-input-bar" action="' + escapeHtml(sendUrl) + '">' +
            '<input type="hidden" name="_token" value="' + escapeHtml(csrf) + '">' +
            '<div class="messages-tools"><button type="button" title="Attach (coming soon)" disabled style="opacity:0.45;cursor:not-allowed;"><i class="fas fa-paperclip"></i></button></div>' +
            '<div class="messages-input-wrapper">' +
            '<input type="text" name="body" placeholder="Write a message…" autocomplete="off" required>' +
            '<button class="messages-send-btn" type="submit"><i class="fas fa-paper-plane"></i> Send</button></div></form>'
        );
    }

    function showEmptyPanel(opts) {
        opts = opts || {};
        const panel = document.getElementById('messages-main-panel');
        if (!panel) return;
        panel.innerHTML =
            '<div class="empty-thread" style="flex:1;display:flex;align-items:center;justify-content:center;">' +
            '<div><p style="font-weight:600;color:var(--text-primary);margin-bottom:8px;">Select a conversation</p>' +
            '<p>Choose someone on the left, or search to start a new chat with your class or a colleague.</p></div></div>';
        conversationId = null;
        lastMessageId = 0;
        threadSince = 0;
        setSidebarActive(null);
        if (!opts.skipHistory) {
            pushConversationUrl(null);
        }
    }

    async function loadConversation(convId, opts) {
        opts = opts || {};
        const id = parseInt(convId, 10);
        if (!id) return;
        const panel = document.getElementById('messages-main-panel');
        if (!panel || !threadTpl) return;
        try {
            const r = await fetch(buildThreadUrl(id), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!r.ok) return;
            const data = await r.json();
            if (!data.ok) return;
            const conv = data.conversation;
            const messages = data.messages || [];
            panel.innerHTML = buildTeacherThreadHtml(conv, messages);
            conversationId = conv.id;
            lastMessageId = messages.reduce(function (acc, m) { return Math.max(acc, m.id); }, 0);
            threadSince = (conversationId && lastMessageId === 0) ? Math.floor(Date.now() / 1000) : 0;
            const thread = document.getElementById('messages-thread');
            if (thread) thread.scrollTop = thread.scrollHeight;
            if (!opts.skipHistory) {
                pushConversationUrl(conversationId);
            }
            setSidebarActive(conversationId);
        } catch (e) {
            console.error('loadConversation', e);
        }
    }

    function appendMessage(m) {
        const thread = document.getElementById('messages-thread');
        if (!thread || !m || !m.id) return;
        if (thread.querySelector('[data-message-id="' + m.id + '"]')) return;
        const empty = thread.querySelector('.empty-thread');
        if (empty) empty.remove();
        const row = document.createElement('div');
        const isMe = m.is_me === true || m.is_me === 1 || m.is_me === '1';
        row.className = 'msg-row ' + (isMe ? 'me' : 'them');
        row.setAttribute('data-message-id', String(m.id));
        const who = isMe ? 'You' : (m.user_name || '');
        row.innerHTML = '<div class="msg-bubble">' + escapeHtml(m.body) +
            '<div class="msg-meta">' + escapeHtml(who) + ' · ' + escapeHtml(m.created_label) + '</div></div>';
        thread.appendChild(row);
        thread.scrollTop = thread.scrollHeight;
    }

    function renderSidebarList(conversations) {
        const list = document.getElementById('messages-list');
        if (!list || !conversations) return;
        let html = '';
        conversations.forEach(function (c) {
            const o = c.other;
            const active = conversationId && c.id === conversationId ? 'active' : '';
            const unread = c.unread > 0
                ? '<span class="chat-unread-badge">' + (c.unread > 9 ? '9+' : c.unread) + '</span>'
                : '';
            const dotClass = o.online ? 'online' : 'offline';
            let tag = '';
            if (o.role === 'teacher') {
                tag = '<span class="role-tag">Teacher</span>';
            } else if (c.is_my_class_student) {
                tag = '<span class="role-tag class-tag">My class</span>';
            }
            const pic = imagesBase + (o.pic || 'default-pp.png');
            const myClassAttr = c.is_my_class_student ? '1' : '0';
            html += '<a href="' + buildConvUrl(c.id) + '" class="chat-item chat-filter-item ' + active + '" data-role="' + escapeHtml(o.role) + '" data-my-class="' + myClassAttr + '" data-conv-id="' + c.id + '">' +
                '<div class="chat-avatar"><img src="' + pic + '" alt=""><span class="chat-status-dot ' + dotClass + '"></span></div>' +
                '<div class="chat-text"><div class="chat-name">' + escapeHtml(o.name) + ' ' + tag + '</div>' +
                '<div class="chat-meta-row"><div class="chat-last">' + escapeHtml(c.preview) + '</div>' +
                '<div class="chat-time">' + escapeHtml(c.last_time || '') + '</div></div></div>' + unread + '</a>';
        });
        if (!conversations.length) {
            html = '<div style="font-size:0.82rem; color: var(--inbox-muted); padding:8px 4px;">No conversations yet.</div>';
        }
        list.innerHTML = html;
        if (typeof window.__messagesApplyFilter === 'function') {
            window.__messagesApplyFilter();
        }
    }

    let pollTimer = null;

    async function poll() {
        if (document.visibilityState === 'hidden') return;
        const params = new URLSearchParams();
        if (searchInput && searchInput.value.trim()) {
            params.set('q', searchInput.value.trim());
        }
        if (conversationId) {
            params.set('conversation', conversationId);
            if (lastMessageId > 0) {
                params.set('after_id', lastMessageId);
            } else if (threadSince > 0) {
                params.set('since', threadSince);
            }
        }
        const url = pollUrl + (params.toString() ? '?' + params.toString() : '');
        try {
            const r = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await r.json();
            if (!data.ok) return;
            if (data.conversations) {
                renderSidebarList(data.conversations);
            }
            if (data.messages && data.messages.length) {
                data.messages.forEach(function (m) {
                    appendMessage(m);
                    lastMessageId = Math.max(lastMessageId, m.id);
                });
                threadSince = 0;
            }
            if (data.cleared_conversation) {
                showEmptyPanel({ skipHistory: true });
                pushConversationUrl(null);
            }
        } catch (e) {
            console.warn('messages poll', e);
        }
    }

    function startPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(poll, 2500);
        window.__msgPollTimer = pollTimer;
        poll();
    }

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') poll();
    });

    document.addEventListener('turbolinks:before-visit', function () {
        if (window.__msgPollTimer) {
            clearInterval(window.__msgPollTimer);
            window.__msgPollTimer = null;
        }
    });

    document.addEventListener('click', function (e) {
        const scope = e.target.closest(inboxScope);
        if (!scope) return;
        const link = e.target.closest('#messages-list a.chat-item');
        if (link) {
            e.preventDefault();
            const cid = link.dataset.convId;
            if (cid) loadConversation(cid, {});
            return;
        }
        if (e.target.closest('.js-clear-thread')) {
            e.preventDefault();
            if (!conversationId || !confirm('Clear this conversation from your inbox?')) return;
            const fd = new FormData();
            fd.append('_token', csrf);
            fd.append('_method', 'DELETE');
            fetch(buildDestroyUrl(conversationId), {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (r) { return r.json(); }).then(function (data) {
                if (data && data.ok) {
                    showEmptyPanel({});
                    poll();
                }
            }).catch(function (err) { console.error(err); });
        }
    });

    document.addEventListener('submit', function (e) {
        const scope = e.target.closest(inboxScope);
        if (!scope) return;
        const form = e.target;
        if (form.classList && form.classList.contains('js-contact-start')) {
            e.preventDefault();
            const fd = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (r) { return r.json(); }).then(function (data) {
                if (data && data.ok && data.conversation_id) {
                    if (contactsPanel) contactsPanel.style.display = 'none';
                    loadConversation(data.conversation_id, {});
                    poll();
                }
            }).catch(function (err) { console.error(err); });
            return;
        }
        if (form.id === 'message-send-form') {
            e.preventDefault();
            if (!conversationId) return;
            const input = form.querySelector('input[name="body"]');
            const body = (input && input.value || '').trim();
            if (!body) return;
            const fd = new FormData();
            fd.append('body', body);
            fd.append('_token', csrf);
            fetch(buildSendUrl(conversationId), {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (res) { return res.json(); }).then(function (data) {
                if (data.ok && data.message) {
                    input.value = '';
                    appendMessage(data.message);
                    lastMessageId = Math.max(lastMessageId, data.message.id);
                    poll();
                }
            }).catch(function (err) { console.error(err); });
        }
    });

    window.addEventListener('popstate', function () {
        const params = new URLSearchParams(window.location.search);
        const c = params.get('conversation');
        if (c) {
            loadConversation(c, { skipHistory: true });
        } else {
            showEmptyPanel({ skipHistory: true });
        }
    });

    function bootMessagesInbox() {
        window.__messagesApplyFilter();

        if (searchForm && !searchForm.dataset.ajaxSearch) {
            searchForm.dataset.ajaxSearch = '1';
            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();
                poll();
            });
        }

        if (searchInput && contactsPanel && !searchInput.dataset.panelBound) {
            searchInput.dataset.panelBound = '1';
            searchInput.addEventListener('focus', function () {
                contactsPanel.style.display = 'block';
            });
            searchInput.addEventListener('input', function () {
                contactsPanel.style.display = 'block';
            });
            document.addEventListener('click', function (e) {
                const isSearch = searchInput.contains(e.target);
                const isContacts = contactsPanel.contains(e.target);
                if (!isSearch && !isContacts) {
                    contactsPanel.style.display = 'none';
                }
            });
        }

        startPolling();
    }

    if (typeof Turbolinks !== 'undefined') {
        document.addEventListener('turbolinks:load', bootMessagesInbox);
    } else {
        document.addEventListener('DOMContentLoaded', bootMessagesInbox);
    }
})();
</script>
@endsection
