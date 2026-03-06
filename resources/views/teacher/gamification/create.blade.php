@extends('teacher.dashboard')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-extrabold text-indigo-700">➕ Create New Challenge</h2>
            <p class="text-gray-600 mt-1">Encourage your students with goals, points, and achievements!</p>
        </div>
        <a href="{{ route('teacher.gamification.index') }}" 
           class="bg-gray-300 text-gray-800 px-4 py-2.5 rounded-lg hover:bg-gray-400 transition">
           ← Back
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white shadow-lg rounded-2xl p-8 max-w-2xl mx-auto border border-gray-100">
        <form action="{{ route('teacher.gamification.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">🏅 Challenge Title</label>
                <input type="text" name="title" class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none" placeholder="Ex: Complete 5 Lessons" required>
            </div>

            <!-- Points -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">💰 Points</label>
                <input type="number" name="points" class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none" placeholder="Ex: 200" required>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">📝 Description (Optional)</label>
                <textarea name="description" rows="4" class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none" placeholder="Describe what students need to achieve..."></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('teacher.gamification.index') }}" 
                   class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                   Cancel
                </a>
                <button type="submit" 
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 shadow transition">
                        💾 Save Challenge
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
