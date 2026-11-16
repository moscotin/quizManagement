<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-[#FDFDFC]">
                <div class="p-6 text-gray-900 dark:text-[#EDEDEC]">
                    <h3 class="text-lg font-semibold mb-4">Выберите период для прохождения викторины</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($months as $m)
                            <div class="flex flex-col items-center">
                                @if($m['enabled'])
                                    <a href="{{ route('quiz.month', ['month' => $m['slug']]) }}"
                                       class="btn-glow btn-purple btn-hover-white text-center">
                                        {{ $m['label'] }}
                                    </a>
                                @else
                                    <span class="btn-glow btn-purple text-center opacity-50" aria-disabled="true">
                                        {{ $m['label'] }}
                                    </span>
                                @endif

                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    доступны с {{ $m['availableAt']->format('d.m.Y') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr class="border-black border-1" />
            </div>
        </div>
    </div>
</x-app-layout>
