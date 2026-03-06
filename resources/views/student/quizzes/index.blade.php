@extends('student.dashboard')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">🧠 Assessments & Activities</h2>
    <table class="min-w-full bg-white border">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4">Quiz Title</th>
                <th class="py-2 px-4 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quizzes as $quiz)
            <tr>
                <td class="py-2 px-4 border-t">{{ $quiz['title'] }}</td>
                <td class="py-2 px-4 border-t text-center">
                    <a href="{{ route('student.quizzes.take', $quiz['id']) }}" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Take Quiz</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
