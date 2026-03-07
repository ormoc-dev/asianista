@extends('admin.dashboard')

@section('content')

<style>
    .content-shell {
        margin-top: 10px;
    }

    .content-card {
        background: radial-gradient(circle at top, rgba(191,197,219,0.7), rgba(241,241,224,0.9));
        border-radius: 18px;
        padding: 22px 24px 26px;
        box-shadow: 0 14px 35px rgba(15,23,42,0.35);
        border: 1px solid rgba(255,255,255,0.7);
        backdrop-filter: blur(10px);
    }

    .content-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
        border-bottom: 2px solid rgba(0,35,102,0.1);
        padding-bottom: 15px;
    }

    .content-title {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #002366;
    }

    .content-title i {
        font-size: 1.8rem;
        color: #f5c400;
        text-shadow: 0 0 10px rgba(245,196,0,0.3);
    }

    .content-title h2 {
        font-size: 1.3rem;
        font-weight: 700;
    }

    /* Table Styling */
    .realm-table-wrapper {
        background: rgba(255,255,255,0.8);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 24px rgba(15,23,42,0.22);
    }

    .realm-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .realm-table thead {
        background: linear-gradient(90deg, #0f172a, #1e293b);
        color: #e2e8f0;
    }

    .realm-table th,
    .realm-table td {
        padding: 12px 15px;
        text-align: left;
        vertical-align: middle;
    }

    .realm-table th {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .realm-table tbody tr {
        border-bottom: 1px solid rgba(0,35,102,0.05);
        transition: background 0.2s;
    }

    .realm-table tbody tr:hover {
        background: rgba(254,249,195,0.5);
    }

    /* Status Badges */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-rejected { background: #fee2e2; color: #991b1b; }

    /* Action Buttons */
    .hero-command {
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .cmd-approve {
        background: linear-gradient(135deg, #34d399, #059669);
        color: #fff;
        box-shadow: 0 4px 10px rgba(5,150,105,0.3);
    }

    .cmd-reject {
        background: linear-gradient(135deg, #fb7185, #e11d48);
        color: #fff;
        box-shadow: 0 4px 10px rgba(225,29,72,0.3);
    }

    .cmd-view {
        background: linear-gradient(135deg, #60a5fa, #2563eb);
        color: #fff;
        box-shadow: 0 4px 10px rgba(37,99,235,0.3);
    }

    .hero-command:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.2);
        filter: brightness(1.1);
    }

    .alert {
        padding: 12px 18px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.3s ease-out;
    }

    .alert-success {
        background: rgba(16,185,129,0.1);
        border: 1px solid rgba(16,185,129,0.2);
        color: #065f46;
    }

    @keyframes slideIn {
        from { transform: translateY(-10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 768px) {
        .realm-table th:nth-child(3),
        .realm-table td:nth-child(3) { display: none; }
    }
</style>

<div class="content-shell">
    <div class="content-card">
        <div class="content-card-header">
            <div class="content-title">
                <i class="fas fa-scroll"></i>
                <div>
                    <h2>Content Control</h2>
                    <p style="font-size:0.85rem; color:#64748b; font-weight:400;">Review and moderate lessons uploaded to the realm.</p>
                </div>
            </div>
            <div style="text-align:right;">
                <span style="font-size:0.75rem; color:#94a3b8; text-transform:uppercase; letter-spacing:1px;">{{ now()->format('M d, Y') }}</span>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="realm-table-wrapper">
            <table class="realm-table">
                <thead>
                    <tr>
                        <th style="width:30%;">Lesson Title</th>
                        <th>Teacher</th>
                        <th>Target Section</th>
                        <th>Review Status</th>
                        <th style="text-align:right;">Hero Commands</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lessons as $lesson)
                        <tr>
                            <td>
                                <div style="font-weight:600; color:#0f172a;">{{ $lesson->title }}</div>
                                <div style="font-size:0.75rem; color:#64748b;">Uploaded {{ $lesson->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="width:24px; height:24px; border-radius:50%; overflow:hidden; border:1px solid #f5c400;">
                                        <img src="{{ asset('images/' . ($lesson->teacher->profile_pic ?? 'default-pp.png')) }}" style="width:100%; height:100%; object-fit:cover;">
                                    </div>
                                    <span style="font-weight:500;">{{ $lesson->teacher->name ?? 'Unknown Hero' }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:0.8rem; background:rgba(0,35,102,0.05); padding:2px 8px; border-radius:4px; color:#475569;">
                                    {{ $lesson->section ?? 'All Sections' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $status = $lesson->status ?? 'pending';
                                    $icon = $status === 'approved' ? 'fa-check' : ($status === 'rejected' ? 'fa-times' : 'fa-clock');
                                @endphp
                                <span class="status-pill status-{{ $status }}">
                                    <i class="fas {{ $icon }}"></i>
                                    {{ $status }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex; justify-content:flex-end; gap:8px; flex-wrap:wrap;">
                                    @if($lesson->status === 'pending')
                                        <form action="{{ route('admin.lessons.approve', $lesson->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="hero-command cmd-approve">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.lessons.reject', $lesson->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="hero-command cmd-reject">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    @endif

                                    @if($lesson->file_path)
                                        <a href="{{ asset('storage/' . $lesson->file_path) }}" target="_blank" class="hero-command cmd-view">
                                            <i class="fas fa-eye"></i> View File
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:30px; color:#94a3b8; font-style:italic;">
                                No lessons have been submitted for review in this realm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:15px; text-align:center; font-size:0.8rem; color:#94a3b8;">
            Monitoring <strong>{{ $lessons->count() }}</strong> ancient scrolls (lessons)
        </div>
    </div>
</div>

@endsection
