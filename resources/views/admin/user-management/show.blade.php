@extends('admin.dashboard')

@section('content')

<style>
    .um-show-page {
        padding: 20px;
    }
    .um-show-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        max-width: 100%;
    }
    .um-show-card .card-header {
        border-bottom: 1px solid var(--border);
    }
    .um-show-card .card-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .um-profile-head {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .um-avatar {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid var(--border);
        flex-shrink: 0;
        background: var(--bg-main);
    }
    .um-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .um-profile-meta h2 {
        margin: 0 0 6px 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
    }
    .um-profile-meta .um-email {
        display: block;
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 10px;
    }
    .um-role-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .um-role-admin { background: rgba(239, 68, 68, 0.12); color: #b91c1c; }
    .um-role-teacher { background: rgba(59, 130, 246, 0.12); color: #1d4ed8; }
    .um-role-student { background: rgba(16, 185, 129, 0.12); color: #047857; }

    .um-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
        margin-bottom: 24px;
    }
    .um-stat {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 16px;
        text-align: center;
    }
    .um-stat i {
        font-size: 1.25rem;
        color: var(--primary);
        margin-bottom: 8px;
        display: block;
    }
    .um-stat-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        display: block;
        margin-bottom: 4px;
    }
    .um-stat-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .um-details {
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
        background: var(--bg-card);
    }
    .um-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        font-size: 0.9rem;
    }
    .um-detail-row:last-child {
        border-bottom: none;
    }
    .um-detail-label {
        font-weight: 600;
        color: var(--text-secondary);
    }
    .um-detail-value {
        color: var(--text-primary);
        text-align: right;
    }

    .um-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    .um-status-pending { background: rgba(245, 158, 11, 0.15); color: #b45309; }
    .um-status-approved { background: rgba(16, 185, 129, 0.15); color: #065f46; }
    .um-status-rejected { background: rgba(239, 68, 68, 0.15); color: #991b1b; }

    .um-show-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
    }
</style>

@php
    $profilePic = $user->profile_pic ?? 'default-pp.png';
    $roleClass = $user->role === 'admin' ? 'um-role-admin' : ($user->role === 'teacher' ? 'um-role-teacher' : 'um-role-student');
    $status = $user->status ?? 'pending';
    $statusClass = 'um-status-' . $status;
@endphp

<div class="um-show-page">
    <div class="card um-show-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-user" style="color: var(--primary);"></i>
                User profile
            </h2>
        </div>
        <div class="card-body">
            <div class="um-profile-head">
                <div class="um-avatar">
                    <img src="{{ asset('images/' . $profilePic) }}" alt="{{ $user->name }}">
                </div>
                <div class="um-profile-meta">
                    <h2>{{ $user->name }}</h2>
                    <span class="um-email">{{ $user->email }}</span>
                    <span class="um-role-pill {{ $roleClass }}">
                        <i class="fas @if($user->role === 'admin') fa-shield-halved @elseif($user->role === 'teacher') fa-chalkboard-user @else fa-user-graduate @endif"></i>
                        {{ $user->role }}
                    </span>
                </div>
            </div>

            @if($user->role === 'student')
                <p style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin: 0 0 12px 0;">
                    Progression
                </p>
                <div class="um-stats">
                    <div class="um-stat">
                        <i class="fas fa-trophy"></i>
                        <span class="um-stat-label">Level</span>
                        <span class="um-stat-value">{{ $user->level ?? 1 }}</span>
                    </div>
                    <div class="um-stat">
                        <i class="fas fa-bolt"></i>
                        <span class="um-stat-label">Total XP</span>
                        <span class="um-stat-value">{{ number_format($user->xp ?? 0) }}</span>
                    </div>
                    <div class="um-stat">
                        <i class="fas fa-mask"></i>
                        <span class="um-stat-label">Character</span>
                        <span class="um-stat-value">{{ ucfirst($user->character ?? 'None') }}</span>
                    </div>
                </div>
            @endif

            <p style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin: 0 0 12px 0;">
                Account details
            </p>
            <div class="um-details">
                <div class="um-detail-row">
                    <span class="um-detail-label">Display name</span>
                    <span class="um-detail-value">{{ $user->name }}</span>
                </div>
                <div class="um-detail-row">
                    <span class="um-detail-label">Member since</span>
                    <span class="um-detail-value">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="um-detail-row">
                    <span class="um-detail-label">Status</span>
                    <span class="um-detail-value">
                        <span class="um-status-badge {{ $statusClass }}">
                            <i class="fas @if($status === 'approved') fa-check-circle @elseif($status === 'rejected') fa-times-circle @else fa-hourglass-half @endif"></i>
                            {{ $status }}
                        </span>
                    </span>
                </div>
            </div>

            <div class="um-show-actions">
                <a href="{{ route('admin.user-management') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to list
                </a>
                <a href="{{ route('admin.user-management.edit', $user->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit user
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
