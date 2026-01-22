<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quizzes for ') . $month }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('dashboard') }}" class="text-blue-500 hover:text-blue-700">
                            ← Back to Dashboard
                        </a>
                    </div>
                    
                    <h3 class="text-lg font-semibold mb-4">Select a Quiz</h3>
                    
                    @if($quizzes->count() > 0)
                        <div class="space-y-4">
                            @foreach($quizzes as $quiz)
                                <div class="border border-gray-300 rounded-lg p-4 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-semibold text-lg">{{ $quiz->name }}</h4>
                                            <p class="text-gray-600 text-sm">{{ $quiz->questions->count() }} questions</p>
                                            <p class="text-gray-600 text-sm">Time limit: 20 minutes</p>
                                        </div>
                                        <a href="{{ route('quiz.start', $quiz->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            Start Quiz
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No quizzes available for {{ $month }} yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
