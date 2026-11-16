<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('quiz.month', ['month' => $month]) }}" class="font-blue hover:text-blue-700">
                            ← Назад к выбору направлений
                        </a>
                    </div>

                    <h3 class="text-lg font-semibold mb-4 text-center">{{ $periodLabel }} · {{ $category->name }}</h3>

                    @if($quizzes->count() > 0)
                        <div class="space-y-4">
                            <div class="flex flex-wrap justify-center gap-4">
                            @foreach($quizzes as $quiz)
                                @php
                                    $completed = $quiz->isTakenByUser(auth()->user());
                                    $started = $quiz->isStartedByUser(auth()->user());
                                    $expired = now()->gt($quiz->end);
                                    $upcoming = !auth()->user()->is_admin && now()->lt($quiz->start);
                                @endphp
                                <div class="flex w-full min-w-0 flex-col items-center space-y-4 p-4 lg:basis-[calc((100%_-_3rem)/4)]">
                                    <div class="text-center">
                                        <h4 class="font-semibold text-lg min-h-14">{{ $quiz->name }}</h4>
                                        @if($started && !$expired)
                                            <!-- display a warning icon -->
                                            <div class="flex items-center justify-center min-h-10 mt-2">
                                                <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-yellow-600 font-medium">Викторина начата, время идёт!</span>
                                            </div>
                                        @elseif($completed)
                                            <!-- display a checkmark icon -->
                                            <div class="flex items-center justify-center min-h-10 mt-2">
                                                <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span class="text-green-600 font-medium">Викторина пройдена</span>
                                            </div>
                                        @else
                                            <div class="justify-center min-h-10 mt-2">
                                                <p class="text-gray-600 text-sm">{{ $quiz->questions_count }} {{ $questionWording($quiz->questions_count) }}</p>
                                                <p class="text-gray-600 text-sm">Ограничение по времени: {{ $quiz->time_limit }} минут</p>
                                                @if($quiz->diploma_image)
                                                    <p class="text-gray-600 text-sm">Для диплома: {{ $quiz->required_correct_answers }} правильных ответов</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    @if($completed)
                                        @if($quiz->certificate_image)
                                            <a href="{{ route('quiz.certificate', $quiz->id) }}" class="mt-auto btn-purple btn-hover-white btn-glow text-sm">
                                                Скачать сертификат
                                            </a>
                                        @endif
                                    @elseif($expired)
                                        <p class="mt-auto text-sm text-gray-600">Приём ответов завершён.</p>
                                    @elseif($upcoming)
                                        <p class="mt-auto text-sm text-gray-600">Доступна с {{ $quiz->start->format('d.m.Y') }}</p>
                                    @elseif($started)
                                        <a href="{{ route('quiz.start', $quiz->id) }}" class="mt-auto btn-purple btn-hover-white btn-glow text-sm">
                                            Перейти к викторине
                                        </a>
                                    @else
                                        <a href="{{ route('quiz.start', $quiz->id) }}"
                                           class="mt-auto btn-purple btn-hover-white btn-glow open-quiz-modal text-sm"
                                           data-time-limit="{{ $quiz->time_limit }}"
                                        >
                                            Начать викторину
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                            </div>
                        </div>

                        <div id="quiz-modal" inert
                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out">
                            <div id="quiz-modal-panel"
                                 class="bg-white rounded-3xl shadow-lg max-w-6xl w-full transform scale-95 translate-y-4 transition-all duration-300 ease-in-out">
                                <div class="bg-[url('/img/bg_left.svg')] bg-left bg-no-repeat lg:p-6 p-2 lg:px-20">
                                    <div class="badge lg:my-4 text-left">Правила участия в викторине:</div>

                                    <div class="text-block lg:my-4 lg:p-6 bg-white mx-auto text-center rounded-3xl border-gray-100 border">
                                        <p>
                                            Для получения диплома победителя нужно пройти все викторины направления
                                            и набрать необходимое количество правильных ответов в каждой.
                                        </p>
                                        <p>
                                            На прохождение викторины выделяется <span id="minuteHolder">12</span> минут.
                                            Таймер запускается после перехода к первому вопросу.
                                        </p>
                                        <p>Вернуться к предыдущему вопросу после ответа на него будет невозможно.</p>
                                    </div>

                                    <div class="badge wide my-4 mx-auto">
                                        Для участия необходимо стабильное интернет-соединение. Организатор не несет
                                        ответственности за сбои, вызванные проблемами на стороне участника.
                                    </div>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" id="quiz-modal-cancel" class="px-4 py-2 btn-hover-white btn-glow btn-white">
                                            Отмена
                                        </button>
                                        <a href="#" id="quiz-modal-start" class="px-4 py-2 btn-purple btn-glow btn-hover-white">
                                            Начать
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-600">Викторины пока не опубликованы.</p>
                    @endif

                    @if ($canDownloadDiploma)
                        <div class="mt-6 text-center">
                            <a href="{{ route('quiz.diploma', ['month' => $month, 'category' => $category->id]) }}" class="btn-purple btn-hover-white btn-glow">
                                Скачать диплом
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($category->description)
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center">
            <span class="px-6 p-2 text-gray-900 border-orange-400 border-2 rounded-3xl bg-white text-center font-bold inline-block">
                    {!! nl2br(e($category->description)) !!}
            </span>
        </div>
    @endif
</x-app-layout>

@if($quizzes->isNotEmpty())
<script>
    const modal = document.getElementById('quiz-modal');
    const panel = document.getElementById('quiz-modal-panel');
    const startLink = document.getElementById('quiz-modal-start');
    const cancelButton = document.getElementById('quiz-modal-cancel');
    const minutes = document.getElementById('minuteHolder');

    function closeModal() {
        modal.inert = true;
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.classList.remove('opacity-100');
        panel.classList.add('scale-95', 'translate-y-4');
        panel.classList.remove('scale-100', 'translate-y-0');
    }

    document.querySelectorAll('.open-quiz-modal').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            modal.inert = false;
            startLink.href = button.href;
            minutes.textContent = button.dataset.timeLimit;
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
            panel.classList.remove('scale-95', 'translate-y-4');
            panel.classList.add('scale-100', 'translate-y-0');
        });
    });

    cancelButton.addEventListener('click', closeModal);

    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeModal();
    });
</script>
@endif
