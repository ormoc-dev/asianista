@extends('teacher.dashboard')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-extrabold text-indigo-700">✏️ Edit Challenge</h2>
            <p class="text-gray-600 mt-1">Modify details of this challenge below.</p>
        </div>
        <a href="{{ route('teacher.gamification.index') }}" 
           class="bg-gray-300 text-gray-800 px-4 py-2.5 rounded-lg hover:bg-gray-400 transition">
           ← Back
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white shadow-lg rounded-2xl p-8 max-w-2xl mx-auto border border-gray-100">
        <form action="{{ route('teacher.gamification.update', $challenge->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">🏅 Challenge Title</label>
                <input type="text" name="title" value="{{ old('title', $challenge->title) }}" 
                       class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none" required>
            </div>

            <!-- Points -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">💰 Points</label>
                <input type="number" name="points" value="{{ old('points', $challenge->points) }}" 
                       class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none" required>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">📝 Description</label>
                <textarea name="description" rows="4" 
                          class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none">{{ old('description', $challenge->description) }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('teacher.gamification.index') }}" 
                   class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                   Cancel
                </a>
                <button type="submit" 
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 shadow transition">
                        💾 Update Challenge
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
