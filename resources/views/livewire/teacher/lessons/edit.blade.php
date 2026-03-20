<div class="card" style="max-width:800px;margin:auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
    <h2 style="font-size:22px;font-weight:700;color:#1e3a8a;margin-bottom:10px;">✏️ Edit Lesson</h2>

    @if (session('error'))
        <div style="background:#fee2e2;color:#991b1b;padding:12px;margin-bottom:20px;border-radius:8px;">
            ❌ {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="update" style="margin-top:20px;">
        <div style="margin-bottom:15px;">
            <label style="display:block;font-weight:bold;margin-bottom:5px;">Lesson Title:</label>
            <input type="text" wire:model="title" required style="width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;">
            @error('title') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block;font-weight:bold;margin-bottom:5px;">Section:</label>
            <input type="text" wire:model="section" style="width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;">
            @error('section') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block;font-weight:bold;margin-bottom:5px;">Replace File (optional):</label>
            <input type="file" wire:model="newFile" style="width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;">
            <div wire:loading wire:target="newFile" style="font-size: 0.8rem; color: #4f46e5;">Uploading file...</div>
            @error('newFile') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <button type="submit" style="background:#4f46e5;color:white;padding:10px 20px;border:none;border-radius:6px;cursor:pointer; font-weight: 600;">
            Update Lesson
        </button>
        <a href="{{ route('teacher.lessons.index') }}" style="margin-left: 10px; color: #6b7280; text-decoration: none;">Cancel</a>
    </form>
</div>
