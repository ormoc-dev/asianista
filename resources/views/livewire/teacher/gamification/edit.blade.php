<div class="card" style="max-width: 800px; margin: 20px auto; padding: 30px; background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
    <h2 style="color: var(--primary); font-size: 1.8rem; margin-bottom: 30px; text-align: center;">✏️ Edit Challenge</h2>

    <form wire:submit.prevent="update">
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Challenge Title:</label>
            <input type="text" wire:model="title" required
                style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; transition: border-color 0.2s;">
            @error('title') <span style="color: #ef4444; font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 8px;">XP Points:</label>
            <input type="number" wire:model="points" required
                style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem;">
            @error('points') <span style="color: #ef4444; font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Description:</label>
            <textarea wire:model="description" required rows="4"
                style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; resize: vertical;"></textarea>
            @error('description') <span style="color: #ef4444; font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end;">
            <a href="{{ route('teacher.gamification.index') }}" 
                style="padding: 12px 24px; color: #64748b; text-decoration: none; font-weight: 600; border-radius: 12px; background: #f1f5f9; transition: background 0.2s;">
                Cancel
            </a>
            <button type="submit" 
                style="padding: 12px 24px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: transform 0.2s;">
                Update Challenge
            </button>
        </div>
    </form>
</div>
