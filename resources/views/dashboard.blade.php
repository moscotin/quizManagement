<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-[#EDEDEC] leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#1a1a1a] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-[#EDEDEC]">
                    <h3 class="text-lg font-semibold mb-4">Select a Month to View Quizzes</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('quiz.month', ['month' => 'february']) }}" 
                           class="btn-glow btn-purple btn-hover-white h-16 text-center">
                            February
                        </a>
                        <a href="{{ route('quiz.month', ['month' => 'march']) }}" 
                           class="btn-glow btn-purple btn-hover-white h-16 text-center">
                            March
                        </a>
                        <a href="{{ route('quiz.month', ['month' => 'april']) }}" 
                           class="btn-glow btn-purple btn-hover-white h-16 text-center">
                            April
                        </a>
                        <a href="{{ route('quiz.month', ['month' => 'may']) }}" 
                           class="btn-glow btn-purple btn-hover-white h-16 text-center">
                            May
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
