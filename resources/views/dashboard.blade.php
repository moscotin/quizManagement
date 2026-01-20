<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Select a Month to View Quizzes</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('quiz.month', ['month' => 'february']) }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-6 px-4 rounded text-center">
                            February
                        </a>
                        <a href="{{ route('quiz.month', ['month' => 'march']) }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-6 px-4 rounded text-center">
                            March
                        </a>
                        <a href="{{ route('quiz.month', ['month' => 'april']) }}" 
                           class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-6 px-4 rounded text-center">
                            April
                        </a>
                        <a href="{{ route('quiz.month', ['month' => 'may']) }}" 
                           class="bg-red-500 hover:bg-red-700 text-white font-bold py-6 px-4 rounded text-center">
                            May
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
