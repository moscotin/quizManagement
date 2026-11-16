<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('dashboard') }}" class="font-blue hover:text-blue-700">
                            ← Назад к выбору периода
                        </a>
                    </div>

                    <h3 class="text-lg font-semibold mb-4 text-center">{{ $periodLabel }}</h3>

                    @if($quizCats->count() > 0)
                        <div class="space-y-4">
                            <div class="flex flex-wrap justify-center gap-4">
                            @foreach($quizCats as $quizCat)
                                <div class="flex w-full min-w-0 flex-col items-center space-y-4 p-4 lg:basis-[calc((100%_-_2rem)/3)]">
                                    <div class="h-64 flex items-end justify-center overflow-hidden">
                                        <a href="{{ route('quiz.category', [
                                            'month' => $month,
                                            'category' => $quizCat->id
                                        ]) }}">
                                            <img src="{{ asset('img/' . ($quizCat->image ?? 'default.png')) }}" alt="" class="max-h-64">
                                        </a>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="font-semibold text-lg">{{ $quizCat->name }}</h4>
                                    </div>
                                    <a href="{{ route('quiz.category', [
                                        'month' => $month,
                                        'category' => $quizCat->id
                                    ]) }}"
                                       class="btn-purple btn-hover-white btn-glow">
                                        Перейти в категорию
                                    </a>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-gray-600">Категории пока не опубликованы.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
