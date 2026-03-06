@extends('teacher.dashboard')

@section('content')
<div class="card" style="max-width:1000px;margin:auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
    <h2 style="font-size:22px;font-weight:700;color:#1e3a8a;margin-bottom:10px;">📘 Your Uploaded Lessons</h2>
    <p style="color:#6b7280;margin-bottom:20px;">Manage your uploaded lessons. You can view, edit, or delete them anytime.</p>

    <!-- Success Message -->
    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;padding:12px;margin-bottom:20px;border-radius:8px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Add Lesson Button -->
    <a href="{{ route('teacher.lessons.create') }}"
       style="background-color:#4f46e5;color:white;padding:10px 20px;text-decoration:none;
              border-radius:8px;font-weight:500;display:inline-block;margin-bottom:20px;">
        ➕ Upload New Lesson
    </a>

    <!-- File Lessons Section -->
    <div style="margin-top:20px;">
        <h3 style="color:#312e81;margin-bottom:10px;">📂 Uploaded Lessons</h3>

        @if($lessons->isEmpty())
            <p style="color:#6b7280;">No uploaded files yet.</p>
        @else
            <table style="width:100%;border-collapse:collapse;font-size:15px;">
                <thead>
                    <tr style="background-color:#eef2ff;text-align:left;">
                        <th style="padding:12px;border-bottom:1px solid #ddd;">Lesson Title</th>
                        <th style="padding:12px;border-bottom:1px solid #ddd;">Section</th>
                        <th style="padding:12px;border-bottom:1px solid #ddd;">Status</th>
                        <th style="padding:12px;border-bottom:1px solid #ddd;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lessons as $lesson)
                        <tr style="background-color:#fff;border-bottom:1px solid #eee;">
                            <td style="padding:12px;">{{ $lesson->title }}</td>
                            <td style="padding:12px;">{{ $lesson->section ?? 'N/A' }}</td>
                            <td style="padding:12px;">
                                @if($lesson->status === 'pending')
                                    <span style="background:#fef9c3;color:#854d0e;padding:4px 8px;border-radius:6px;">🕒 Pending</span>
                                @elseif($lesson->status === 'approved')
                                    <span style="background:#dcfce7;color:#166534;padding:4px 8px;border-radius:6px;">✅ Approved</span>
                                @else
                                    <span style="background:#fee2e2;color:#991b1b;padding:4px 8px;border-radius:6px;">❌ Rejected</span>
                                @endif
                            </td>
                            <td style="padding:12px;">
                                @if($lesson->file_path)
                                    <a href="{{ route('teacher.lessons.download', basename($lesson->file_path)) }}"
                                       style="color:#4f46e5;text-decoration:none;margin-right:10px;">⬇️ Download</a>
                                @endif
                                <a href="{{ route('teacher.lessons.edit', $lesson->id) }}"
                                   style="color:#16a34a;text-decoration:none;margin-right:10px;">✏️ Edit</a>
                                <form action="{{ route('teacher.lessons.destroy', $lesson->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        style="color:#dc2626;background:none;border:none;cursor:pointer;text-decoration:none;">🗑️ Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
