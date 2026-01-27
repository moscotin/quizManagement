<x-app-layout>
{{--    <x-slot name="header">--}}
{{--        <h2 class="font-semibold text-xl text-gray-800 dark:text-[#EDEDEC] leading-tight">--}}
{{--            {{ __('Dashboard') }}--}}
{{--        </h2>--}}
{{--    </x-slot>--}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-[#FDFDFC]">
                <div class="p-6 text-gray-900 dark:text-[#EDEDEC]">
                    <h3 class="text-lg font-semibold mb-4">Выберите месяц для прохождения викторины:</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="flex flex-col items-center">
                            <a href="{{ route('quiz.month', ['month' => 'february']) }}"
                               class="btn-glow btn-purple btn-hover-white text-center">
                                Февраль
                            </a>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                доступны с 1.02.2026
                            </div>
                        </div>
                        <div class="flex flex-col items-center">
                            <a href="{{ route('quiz.month', ['month' => 'march']) }}"
                               class="btn-glow btn-purple btn-hover-white text-center {{date('2026-03-01') <= now() ? '' : 'pointer-events-none opacity-50'}}">
                                Март
                            </a>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                доступны с 1.03.2026
                            </div>
                        </div>
                        <div class="flex flex-col items-center">
                            <a href="{{ route('quiz.month', ['month' => 'april']) }}"
                               class="btn-glow btn-purple btn-hover-white text-center {{date('2026-04-01') <= now() ? '' : 'pointer-events-none opacity-50'}}">
                                Апрель
                            </a>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                доступны с 1.04.2026
                            </div>
                        </div>
                        <div class="flex flex-col items-center">
                            <a href="{{ route('quiz.month', ['month' => 'may']) }}"
                               class="btn-glow btn-purple btn-hover-white text-center {{date('2026-05-01') <= now() ? '' : 'pointer-events-none opacity-50'}}">
                                Май
                            </a>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                доступны с 1.05.2026
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="border-black border-1" />
            </div>
        </div>
    </div>
</x-app-layout>
