<x-quiz-layout>

    @php
        // Timer: compute remaining seconds on the server to avoid JS Date parsing/timezone issues (Safari etc.)
        $durationSeconds = 20 * 60; // 20 minutes
        $elapsed = $participant->started_at
            ? max(0, $participant->started_at->diffInSeconds(now()))
            : 0;

        $remaining = max(0, $durationSeconds - $elapsed);
    @endphp

    <div class="relative">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 relative z-10">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl  glow-orange">
                <div class="p-6">
                    <!-- Timer and Progress -->
                    <div class="flex justify-between items-center mb-6 pb-4 border-b">
                        <div class="text-sm text-gray-600">
                            Вопрос <span id="current-question">{{ $questionNumber }}</span> из {{ $totalQuestions }}
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-sm text-gray-600">
                                Осталось времени:
                            </div>
                            <div id="timer" class="text-2xl font-bold font-blue">
                                {{ gmdate('i:s', $remaining) }}
                            </div>
                        </div>
                    </div>

                    <!-- Question Container -->
                    <div id="question-container">
                        <div class="mb-6">
                            <h3 class="text-xl font-semibold mb-4" id="question-text">
                                {{ $currentQuestion->question }}
                            </h3>
                        </div>

                        <!-- Options -->
                        <form id="answer-form">
                            @csrf
                            <input type="hidden" name="question_id" id="question-id" value="{{ $currentQuestion->id }}">

                            <div class="space-y-3">
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition option-label">
                                    <input type="radio" name="selected_option" value="1" class="mr-3 h-5 w-5" required>
                                    <span class="option-text">{{ $currentQuestion->option_1 }}</span>
                                </label>

                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition option-label">
                                    <input type="radio" name="selected_option" value="2" class="mr-3 h-5 w-5" required>
                                    <span class="option-text">{{ $currentQuestion->option_2 }}</span>
                                </label>

                                {{-- Render 3rd/4th as labels too, but hide if null. This keeps JS simpler. --}}
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition option-label"
                                       style="{{ $currentQuestion->option_3 ? '' : 'display:none' }}">
                                    <input type="radio" name="selected_option" value="3" class="mr-3 h-5 w-5" required>
                                    <span class="option-text">{{ $currentQuestion->option_3 }}</span>
                                </label>

                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition option-label"
                                       style="{{ $currentQuestion->option_4 ? '' : 'display:none' }}">
                                    <input type="radio" name="selected_option" value="4" class="mr-3 h-5 w-5" required>
                                    <span class="option-text">{{ $currentQuestion->option_4 }}</span>
                                </label>
                            </div>

                            <div class="mt-6 flex justify-between items-center">
                                <div id="error-message" class="text-red-600 text-sm"></div>
                                <button type="submit" id="submit-btn" class="btn-purple btn-hover-white btn-glow">
                                    Отправить ответ
                                </button>
                            </div>
                        </form>
                    </div> <!-- /question-container -->
                </div>
            </div>

            <!-- Background decorative elements -->
            <!-- flag -->
            <div class="absolute -top-28 -right-20 w-48 h-48 bg-[url('/img/quiz_decorations/en1.webp')] bg-contain bg-no-repeat -z-1"></div>
        </div>

        <!-- More background decorations -->
        <!-- bottom left -->
        <div class="absolute bottom-0 left-0 w-full h-full bg-[url('/img/quiz_decorations/en2.webp')] bg-contain bg-no-repeat -z-1"></div>
        <!-- bottom right -->
        <div class="absolute bottom-0 -right-12 w-64 h-64 bg-[url('/img/quiz_decorations/en3.webp')] bg-contain bg-no-repeat -z-1"></div>
        <!-- top left -->
        <!--div class="absolute top-0 left-0 w-64 h-64 bg-[url('/img/quiz_decorations/es4.webp')] bg-contain bg-no-repeat -z-1"></div-->

    </div>

    @push('scripts')
        <script>
            // ----------------------------
            // Timer functionality
            // ----------------------------
            // server-computed seconds remaining (force integer)
            let timeRemaining = parseInt(@json($remaining), 10);
            if (Number.isNaN(timeRemaining) || timeRemaining < 0) timeRemaining = 0;

            const timerElement = document.getElementById('timer');
            const completeUrl = @json(route('quiz.complete', $quiz->id));

            function renderTimer() {
                const t = Math.max(0, Math.floor(timeRemaining));   // <-- force integer
                const minutes = Math.floor(t / 60);
                const seconds = t % 60;

                timerElement.textContent =
                    `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;

                if (t <= 300) {
                    timerElement.classList.remove('text-blue-600');
                    timerElement.classList.add('text-red-600');
                }
            }

            function tick() {
                renderTimer();

                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    window.location.href = completeUrl;
                    return;
                }

                timeRemaining = Math.floor(timeRemaining) - 1; // <-- keep integer forever
            }

            const timerInterval = setInterval(tick, 1000);
            tick();

            // ----------------------------
            // Form submission
            // ----------------------------
            const form = document.getElementById('answer-form');
            const submitBtn = document.getElementById('submit-btn');
            const errorMessage = document.getElementById('error-message');

            const quizId = Number(@json($quiz->id));
            let currentQuestionNumber = Number(@json($questionNumber));
            const totalQuestions = Number(@json($totalQuestions));

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(form);
                const selected = formData.get('selected_option');

                if (!selected) {
                    errorMessage.textContent = 'Пожалуйста, выберите вариант ответа.';
                    return;
                }

                const questionId = document.getElementById('question-id').value;
                const submitUrl = `/quiz/${quizId}/question/${questionId}/answer`;

                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
                errorMessage.textContent = '';

                try {
                    const response = await fetch(submitUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            selected_option: parseInt(selected, 10)
                        })
                    });

                    // Handle non-JSON / server errors gracefully
                    let data = null;
                    const contentType = response.headers.get('content-type') || '';
                    if (contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        throw new Error('Unexpected response (not JSON).');
                    }

                    if (data.completed && data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }

                    if (data.next_question && data.question_number) {
                        loadQuestion(data.next_question, data.question_number);
                        return;
                    }

                    if (data.error) {
                        errorMessage.textContent = data.error;
                        if (data.redirect) {
                            setTimeout(() => window.location.href = data.redirect, 2000);
                        }
                        return;
                    }

                    errorMessage.textContent = 'Возникла неизвестная ошибка. Пожалуйста, попробуйте еще раз.';
                } catch (err) {
                    errorMessage.textContent = 'Возникла ошибка при отправке ответа. Пожалуйста, попробуйте еще раз.';
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Отправить ответ';
                }
            });

            function loadQuestion(question, questionNumber) {
                document.getElementById('question-text').textContent = question.question ?? '';
                document.getElementById('question-id').value = question.id ?? '';
                document.getElementById('current-question').textContent = questionNumber + 1;

                const labels = document.querySelectorAll('.option-label');
                const options = [question.option_1, question.option_2, question.option_3, question.option_4];

                labels.forEach((label, index) => {
                    const optionText = label.querySelector('.option-text');
                    const radioInput = label.querySelector('input');

                    const value = options[index];

                    if (value !== null && value !== undefined && String(value).trim() !== '') {
                        if (optionText) optionText.textContent = value;
                        if (radioInput) radioInput.checked = false;
                        label.style.display = 'flex';
                    } else {
                        // If hidden, also ensure it's not selected
                        if (radioInput) radioInput.checked = false;
                        label.style.display = 'none';
                    }
                });

                currentQuestionNumber = questionNumber;
            }
        </script>
    @endpush
</x-quiz-layout>
