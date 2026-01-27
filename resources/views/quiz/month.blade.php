<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('dashboard') }}" class="font-blue hover:text-blue-700">
                            ← Назад к выбору месяца
                        </a>
                    </div>

                    <h3 class="text-lg font-semibold mb-4 text-center">{{ $russianMonthName($month) }}</h3>

                    @if($quizCats->count() > 0)
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                            @foreach($quizCats as $quizCat)
                                <div class="flex-col lg:w-1/3 p-4 flex items-center space-y-4">
                                    <div class="">
                                        <a href="{{ route('quiz.category', [
                                            'month' => $month,
                                            'category' => $quizCat->id
                                        ]) }}">
                                            <img src="{{ asset('img/' . ($quizCat->image ?? 'default.png')) }}" alt="Quiz Icon" class="h-52">
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
                        <p class="text-gray-600">Не найдено викторин за {{ $month }}.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
