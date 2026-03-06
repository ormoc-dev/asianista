@extends('admin.dashboard')

@section('content')
<div class="card" style="max-width:1100px;margin:auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
    <h2 style="font-size:22px;font-weight:700;color:#1e3a8a;margin-bottom:10px;">📋 Quizzes, Pre-Tests & Post-Tests</h2>
    <p style="color:#6b7280;margin-bottom:20px;">Review quizzes uploaded by teachers and approve or reject them.</p>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;padding:12px;margin-bottom:20px;border-radius:8px;">
            ✅ {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div style="background:#fee2e2;color:#991b1b;padding:12px;margin-bottom:20px;border-radius:8px;">
            ❌ {{ session('error') }}
        </div>
    @endif

    @if($quizzes->isEmpty())
        <p style="color:#6b7280;">No quizzes uploaded yet.</p>
    @else
        <table style="width:100%;border-collapse:collapse;font-size:15px;">
            <thead>
                <tr style="background-color:#eef2ff;text-align:left;">
                    <th style="padding:12px;border-bottom:1px solid #ddd;">Title</th>
                    <th style="padding:12px;border-bottom:1px solid #ddd;">Type</th>
                    <th style="padding:12px;border-bottom:1px solid #ddd;">Uploaded By</th>
                    <th style="padding:12px;border-bottom:1px solid #ddd;">Status</th>
                    <th style="padding:12px;border-bottom:1px solid #ddd;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quizzes as $quiz)
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:12px;">{{ $quiz->title }}</td>
                        <td style="padding:12px;text-transform:capitalize;">{{ $quiz->type }}</td>
                        <td style="padding:12px;">{{ $quiz->teacher ? $quiz->teacher->name : 'N/A' }}</td>
                        <td style="padding:12px;">
                            @if($quiz->status === 'pending')
                                <span style="background:#fef9c3;color:#854d0e;padding:4px 8px;border-radius:6px;">🕒 Pending</span>
                            @elseif($quiz->status === 'active')
                                <span style="background:#dcfce7;color:#166534;padding:4px 8px;border-radius:6px;">✅ Approved</span>
                            @else
                                <span style="background:#fee2e2;color:#991b1b;padding:4px 8px;border-radius:6px;">❌ Rejected</span>
                            @endif
                        </td>
                        <td style="padding:12px;">
                            @if($quiz->file_path)
                                <a href="{{ asset('storage/'.$quiz->file_path) }}" target="_blank" style="color:#4f46e5;margin-right:10px;text-decoration:none;">📂 View</a>
                            @endif

                            @if($quiz->status === 'pending')
                                <form action="{{ route('admin.quizzes.approve', $quiz->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" style="background:#22c55e;color:white;padding:6px 10px;border:none;border-radius:6px;cursor:pointer;">✅ Approve</button>
                                </form>
                                <form action="{{ route('admin.quizzes.reject', $quiz->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" style="background:#ef4444;color:white;padding:6px 10px;border:none;border-radius:6px;cursor:pointer;">❌ Reject</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
