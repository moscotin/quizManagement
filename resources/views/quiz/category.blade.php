<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('quiz.month', ['month' => $month]) }}" class="font-blue hover:text-blue-700">
                            ← Назад к выбору категории
                        </a>
                    </div>

                    <h3 class="text-lg font-semibold mb-4 text-center">{{ $russianMonthName($month) }}: {{ $category_name }}</h3>

                    @if($quizzes->count() > 0)
                        <div class="space-y-4">
                            <div class="grid lg:grid-cols-4 gap-4">
                            @foreach($quizzes as $quiz)
                                <div class="flex-col p-4 flex items-center space-y-4">
                                    <div class="text-center">
                                        <h4 class="font-semibold text-lg min-h-14">{{ $quiz->name }}</h4>
                                        @if($quiz->isStartedByUser(auth()->user()))
                                            <!-- display a warning icon -->
                                            <div class="flex items-center justify-center min-h-10 mt-2">
                                                <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-yellow-600 font-medium">Викторина начата, время идёт!</span>
                                            </div>
                                        @elseif($quiz->isTakenByUser(auth()->user()))
                                            <!-- display a checkmark icon -->
                                            <div class="flex items-center justify-center min-h-10 mt-2">
                                                <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span class="text-green-600 font-medium">Викторина пройдена</span>
                                            </div>
                                        @else
                                            <div class="justify-center min-h-10 mt-2">
                                                <p class="text-gray-600 text-sm">{{ $quiz->questions->count() }} {{ $questionWording($quiz->questions->count()) }}</p>
                                                <p class="text-gray-600 text-sm">Ограничение по времени: {{ $quiz->time_limit }} минут</p>
                                            </div>
                                        @endif
                                    </div>
                                    @if($quiz->isStartedByUser(auth()->user()))
                                    <a href="{{ route('quiz.start', $quiz->id) }}" class="mt-auto btn-purple btn-hover-white btn-glow text-sm {{ $quiz->isTakenByUser(auth()->user()) ? 'opacity-50 pointer-events-none' : '' }}">
                                        Перейти к викторине
                                    </a>
                                    @elseif($quiz->isTakenByUser(auth()->user()))
                                    <a href="{{ route('quiz.certificate', $quiz->id) }}" class="mt-auto btn-purple btn-hover-white btn-glow text-sm">
                                        Скачать сертификат
                                    </a>
                                    @else
                                        <a href="{{ route('quiz.start', $quiz->id) }}"
                                           class="mt-auto btn-purple btn-hover-white btn-glow open-quiz-modal text-sm"
                                           data-start-url="{{ route('quiz.start', $quiz->id) }}"
                                           data-time-limit="{{ $quiz->time_limit }}"
                                        >
                                            Начать викторину
                                        </a>
                                    @endif
                                </div>
                                    <div id="quiz-modal"
                                         class="fixed inset-0 z-50 flex items-center justify-center
            bg-black/50
            opacity-0 pointer-events-none
            transition-opacity duration-300 ease-in-out">

                                        <div id="quiz-modal-panel"
                                             class="bg-white rounded-3xl shadow-lg max-w-6xl w-full
                transform scale-95 translate-y-4
                transition-all duration-300 ease-in-out">
                                        <div class="bg-[url('/img/bg_left.svg')] bg-left bg-no-repeat lg:p-6 p-2 lg:px-20">
                                            <div class="badge lg:my-4 text-left">Правила участия в викторине:</div>

                                            <div class="text-block lg:my-4 lg:p-6 bg-white mx-auto text-center rounded-3xl border-gray-100 border">
                                                <p>
                                                    Для получения диплома победителя нужно пройти каждую из четырех викторин
                                                    с результатом 90% и выше.
                                                </p>

                                                <p>
                                                    На прохождение каждой викторины выделяется <span id="minuteHolder">12</span> минут. Таймер запускается после
                                                    перехода к первому вопросу.
                                                </p>
                                                <p>Вернуться к предыдущему вопросу после ответа на него
                                                    будет невозможно.</p>
                                            </div>

                                            <div class="badge wide my-4  mx-auto">
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
                            @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-gray-600">No quizzes available for {{ $month }} yet.</p>
                    @endif

                    @if ($all_quizzes_passed)
                        <div class="mt-6 text-center">
                            <a href="{{ route('quiz.diploma', ['month' => $month, 'category' => $category_id]) }}" class="btn-purple btn-hover-white btn-glow">
                                Скачать диплом
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($category_desc)
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center">
            <span class="px-6 p-2 text-gray-900 border-orange-400 border-2 rounded-3xl bg-white text-center font-bold inline-block">
                    {!! nl2br(e($category_desc)) !!}
            </span>
        </div>
    @endif
</x-app-layout>

<!-- Modal script -->
<script>
    const modal = document.getElementById('quiz-modal');
    const panel = document.getElementById('quiz-modal-panel');
    const startEl = document.getElementById('quiz-modal-start');
    const cancelBtn = document.getElementById('quiz-modal-cancel');

    function openModal({ startUrl }) {
        startEl.href = startUrl || '#';

        // Fill the minuteHolder span with the time limit from the quiz
        // from the data-time-limit attribute of the clicked button
        const clickedButton = document.querySelector(`a.open-quiz-modal[data-start-url="${startUrl}"]`);
        const timeLimit = clickedButton ? clickedButton.dataset.timeLimit : null;
        const minuteHolder = document.getElementById('minuteHolder');
        if (timeLimit && minuteHolder) {
            minuteHolder.textContent = timeLimit;
        }

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');

        panel.classList.remove('scale-95', 'translate-y-4');
        panel.classList.add('scale-100', 'translate-y-0');
    }

    function closeModal() {
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.classList.remove('opacity-100');

        panel.classList.add('scale-95', 'translate-y-4');
        panel.classList.remove('scale-100', 'translate-y-0');
    }

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.open-quiz-modal');
        if (!btn) return;

        e.preventDefault();

        openModal({
            startUrl: btn.dataset.startUrl || btn.getAttribute('href'),
        });
    });

    cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
</script>
