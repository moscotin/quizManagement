<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quiz Complete') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mb-6">
                        <svg class="w-20 h-20 mx-auto text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Congratulations!</h1>
                        <p class="text-gray-600">You have completed {{ $quiz->name }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="text-5xl font-bold text-blue-600 mb-2">
                            {{ $score }} / {{ $totalQuestions }}
                        </div>
                        <div class="text-gray-600">
                            Correct Answers
                        </div>
                        <div class="text-sm text-gray-500 mt-2">
                            Score: {{ round(($score / $totalQuestions) * 100) }}%
                        </div>
                    </div>

                    <div class="space-y-2 text-sm text-gray-600 mb-6">
                        <p>Started: {{ $participant->started_at->format('M d, Y H:i') }}</p>
                        <p>Completed: {{ $participant->completed_at->format('M d, Y H:i') }}</p>
                        <p>Duration: {{ $participant->started_at->diffForHumans($participant->completed_at, true) }}</p>
                    </div>

                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
