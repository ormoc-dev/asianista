@extends('teacher.dashboard')

@section('content')
<div class="card">
    <h2>✏️ Edit Lesson</h2>

    <form action="{{ route('teacher.lessons.update', $lesson->id) }}" method="POST" enctype="multipart/form-data" style="margin-top:20px;">
        @csrf

        <div style="margin-bottom:15px;">
            <label style="display:block;font-weight:bold;margin-bottom:5px;">Lesson Title:</label>
            <input type="text" name="title" value="{{ $lesson->title }}" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block;font-weight:bold;margin-bottom:5px;">Section:</label>
            <input type="text" name="section" value="{{ $lesson->section }}" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block;font-weight:bold;margin-bottom:5px;">Replace File (optional):</label>
            <input type="file" name="file" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
        </div>

        <button type="submit" style="background:#4f46e5;color:white;padding:10px 20px;border:none;border-radius:6px;cursor:pointer;">
            Update
        </button>
    </form>
</div>
@endsection
