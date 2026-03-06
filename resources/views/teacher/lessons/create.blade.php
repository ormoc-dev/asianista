@extends('teacher.dashboard')

@section('content')
<div class="card" style="max-width:800px;margin:auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
    <h2 style="font-size:22px;font-weight:700;color:#1e3a8a;margin-bottom:10px;">📚 Upload Lesson</h2>
    <p style="color:#6b7280;margin-bottom:20px;">Add a new lesson and assign it to a specific class section.</p>

    @if (session('success'))
        <div style="background:#d1fae5;color:#065f46;padding:10px;margin-bottom:15px;border-radius:6px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('teacher.lessons.store') }}" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
        @csrf

        <label>Lesson Title:</label><br>
        <input type="text" name="title" required
            style="width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;"><br><br>

        <label>Assign to Section:</label><br>
        <select name="section" required
            style="width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;background:white;">
            <option value="">-- Select Section --</option>
            <option value="Grade 7 - A">Grade 7 - A</option>
            <option value="Grade 7 - B">Grade 7 - B</option>
            <option value="Grade 8 - A">Grade 8 - A</option>
            <option value="Grade 8 - B">Grade 8 - B</option>
            <option value="Grade 9 - A">Grade 9 - A</option>
            <option value="Grade 9 - B">Grade 9 - B</option>
            <option value="Grade 10 - A">Grade 10 - A</option>
            <option value="Grade 10 - B">Grade 10 - B</option>
        </select><br><br>

        <label>Lesson Content (optional):</label><br>
        <textarea name="content" rows="6"
            placeholder="Enter lesson text here..."
            style="width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;"></textarea><br><br>

        <label>Upload File (optional):</label><br>
        <input type="file" name="file" style="margin-top:5px;"><br><br>

        <button type="submit"
            style="background-color:#4f46e5;color:white;padding:10px 20px;border:none;border-radius:6px;cursor:pointer;">
            Upload Lesson
        </button>
    </form>
</div>
@endsection
