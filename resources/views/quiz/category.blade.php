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

                    <h3 class="text-lg font-semibold mb-4 text-center">{{ $russianMonthName($month) }}</h3>

                    @if($quizzes->count() > 0)
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                            @foreach($quizzes as $quiz)
                                <div class="flex-col lg:w-1/3 w-full p-4 flex items-center space-y-4">
                                    <div class="text-center">
                                        <h4 class="font-semibold text-lg">{{ $quiz->name }}</h4>
                                        @if($quiz->isStartedByUser(auth()->user()))
                                            <!-- display a warning icon -->
                                            <div class="flex items-center justify-center mt-2">
                                                <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-yellow-600 font-medium">Викторина начата, время идёт!</span>
                                            </div>
                                        @elseif($quiz->isTakenByUser(auth()->user()))
                                            <!-- display a checkmark icon -->
                                            <div class="flex items-center justify-center mt-2">
                                                <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span class="text-green-600 font-medium">Викторина пройдена</span>
                                            </div>
                                        @else
                                        <p class="text-gray-600 text-sm">{{ $quiz->questions->count() }} {{ $questionWording($quiz->questions->count()) }}</p>
                                        <p class="text-gray-600 text-sm">Ограничение по времени: {{ $quiz->time_limit }} минут</p>
                                        @endif
                                    </div>
                                    @if($quiz->isStartedByUser(auth()->user()))
                                    <a href="{{ route('quiz.start', $quiz->id) }}" class="btn-purple btn-hover-white btn-glow {{ $quiz->isTakenByUser(auth()->user()) ? 'opacity-50 pointer-events-none' : '' }}">
                                        Перейти к викторине
                                    </a>
                                    @elseif($quiz->isTakenByUser(auth()->user()))
                                    <a href="{{ route('quiz.certificate', $quiz->id) }}" class="btn-purple btn-hover-white btn-glow">
                                        Скачать сертификат
                                    </a>
                                    @else
                                        <a href="{{ route('quiz.start', $quiz->id) }}"
                                           class="btn-purple btn-hover-white btn-glow open-quiz-modal"
                                           data-start-url="{{ route('quiz.start', $quiz->id) }}">
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
                                        <div class="bg-[url('/img/bg_left.svg')] bg-left bg-no-repeat p-6 px-20">
                                            <div class="badge my-4 text-left">Правила участия в викторине:</div>

                                            <div class="text-block my-4 p-6 bg-white mx-auto text-center rounded-3xl border-gray-100 border">
                                                <p>
                                                    Для получения диплома победителя нужно пройти каждую из четырех викторин
                                                    с результатом 90% и выше.
                                                </p>

                                                <p>
                                                    На прохождение каждой викторины выделяется 12 минут. Таймер запускается после
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Modal script -->
<script>
    const modal = document.getElementById('quiz-modal');
    const panel = document.getElementById('quiz-modal-panel');
    const startEl = document.getElementById('quiz-modal-start');
    const cancelBtn = document.getElementById('quiz-modal-cancel');

    function openModal({ startUrl }) {
        startEl.href = startUrl || '#';

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
