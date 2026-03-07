@extends('admin.dashboard')

@section('content')

<style>
    .user-profile-shell {
        margin-top: 10px;
    }

    .profile-card {
        background: radial-gradient(circle at top, rgba(191,197,219,0.7), rgba(241,241,224,0.9));
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 14px 35px rgba(15,23,42,0.35);
        border: 1px solid rgba(255,255,255,0.7);
        backdrop-filter: blur(10px);
        max-width:auto;
        margin: 0 auto;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 25px;
        margin-bottom: 30px;
        border-bottom: 2px solid rgba(0,35,102,0.1);
        padding-bottom: 20px;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #fff;
        box-shadow: 0 8px 16px rgba(15,23,42,0.3);
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-info h2 {
        color: #002366;
        font-size: 1.8rem;
        margin-bottom: 5px;
    }

    .profile-info .email {
        color: #64748b;
        font-size: 1rem;
        margin-bottom: 10px;
        display: block;
    }

    .role-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 6px 16px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-top: 10px;
    }

    .role-admin { background: rgba(239,68,68,0.15); color: #b91c1c; }
    .role-teacher { background: rgba(59,130,246,0.15); color: #1d4ed8; }
    .role-student { background: rgba(34,197,94,0.15); color: #15803d; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-pending { background: rgba(245,158,11,0.15); color: #b45309; }
    .status-approved { background: rgba(16,185,129,0.15); color: #065f46; }
    .status-rejected { background: rgba(239,68,68,0.15); color: #991b1b; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-box {
        background: rgba(255,255,255,0.6);
        padding: 15px;
        border-radius: 12px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.5);
    }

    .stat-box i {
        font-size: 1.5rem;
        color: #f5c400;
        margin-bottom: 8px;
        display: block;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
        display: block;
    }

    .profile-details {
        background: rgba(255,255,255,0.4);
        border-radius: 12px;
        padding: 20px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid rgba(0,35,102,0.05);
    }

    .detail-row:last-child { border-bottom: none; }

    .detail-label { font-weight: 600; color: #475569; }
    .detail-value { color: #0f172a; }

    .action-bar {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }

    .btn-back {
        text-decoration: none;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        transition: color 0.2s;
    }

    .btn-back:hover { color: #002366; }

    .btn-edit {
        background: linear-gradient(135deg, #ffd43b, #f5c400);
        color: #0b1020;
        padding: 10px 24px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(245,196,0,0.4);
        transition: all 0.2s;
    }

    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(245,196,0,0.5);
    }

    @media (max-width: 600px) {
        .profile-header { flex-direction: column; text-align: center; }
        .stats-grid { grid-template-columns: 1fr 1fr; }
    }
</style>

<div class="user-profile-shell">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                @php $profilePic = $user->profile_pic ?? 'default-pp.png'; @endphp
                <img src="{{ asset('images/' . $profilePic) }}" alt="{{ $user->name }}">
            </div>
            <div class="profile-info">
                <h2>{{ $user->name }}</h2>
                <span class="email">{{ $user->email }}</span>
                @php $roleClass = $user->role === 'admin' ? 'role-admin' : ($user->role === 'teacher' ? 'role-teacher' : 'role-student'); @endphp
                <span class="role-pill {{ $roleClass }}">
                    <i class="fas @if($user->role === 'admin') fa-crown @elseif($user->role === 'teacher') fa-chalkboard-teacher @else fa-user-graduate @endif"></i>
                    {{ strtoupper($user->role) }}
                </span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <i class="fas fa-trophy"></i>
                <span class="stat-label">Level</span>
                <span class="stat-value">{{ $user->level ?? '01' }}</span>
            </div>
            <div class="stat-box">
                <i class="fas fa-bolt"></i>
                <span class="stat-label">Total XP</span>
                <span class="stat-value">{{ number_format($user->xp ?? 0) }}</span>
            </div>
            <div class="stat-box">
                <i class="fas fa-dragon"></i>
                <span class="stat-label">Character</span>
                <span class="stat-value">{{ ucfirst($user->character ?? 'None') }}</span>
            </div>
        </div>

        <div class="profile-details">
            <div class="detail-row">
                <span class="detail-label">Username</span>
                <span class="detail-value">{{ $user->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Realm Joined</span>
                <span class="detail-value">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value text-capitalize">
                    @php
                        $status = $user->status ?? 'pending';
                        $statusClass = 'status-' . $status;
                    @endphp
                    <span class="status-badge {{ $statusClass }}">
                        <i class="fas @if($status === 'approved') fa-check-circle @elseif($status === 'rejected') fa-times-circle @else fa-hourglass-half @endif"></i>
                        {{ $status }}
                    </span>
                </span>
            </div>
        </div>

        <div class="action-bar">
            <a href="{{ route('admin.user-management') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Realm
            </a>
            <a href="{{ route('admin.user-management.edit', $user->id) }}" class="btn-edit">
                <i class="fas fa-edit"></i> Edit Hero
            </a>
        </div>
    </div>
</div>

@endsection
