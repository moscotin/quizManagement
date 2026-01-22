<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $quiz->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Timer and Progress -->
                    <div class="flex justify-between items-center mb-6 pb-4 border-b">
                        <div class="text-sm text-gray-600">
                            Question <span id="current-question">{{ $questionNumber }}</span> of {{ $totalQuestions }}
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-sm text-gray-600">
                                Time Remaining:
                            </div>
                            <div id="timer" class="text-2xl font-bold text-blue-600">
                                20:00
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

                                @if($currentQuestion->option_3)
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition option-label">
                                    <input type="radio" name="selected_option" value="3" class="mr-3 h-5 w-5" required>
                                    <span class="option-text">{{ $currentQuestion->option_3 }}</span>
                                </label>
                                @endif

                                @if($currentQuestion->option_4)
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition option-label">
                                    <input type="radio" name="selected_option" value="4" class="mr-3 h-5 w-5" required>
                                    <span class="option-text">{{ $currentQuestion->option_4 }}</span>
                                </label>
                                @endif
                            </div>

                            <div class="mt-6 flex justify-between items-center">
                                <div id="error-message" class="text-red-600 text-sm"></div>
                                <button type="submit" id="submit-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition">
                                    Submit Answer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Timer functionality
        let timeRemaining = 20 * 60; // 20 minutes in seconds
        const startTime = new Date('{{ $participant->started_at }}').getTime();
        const currentTime = new Date().getTime();
        const elapsedSeconds = Math.floor((currentTime - startTime) / 1000);
        timeRemaining = Math.max(0, timeRemaining - elapsedSeconds);

        const timerElement = document.getElementById('timer');
        
        function updateTimer() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                window.location.href = '{{ route('quiz.complete', $quiz->id) }}';
            }
            
            // Change color when less than 5 minutes remaining
            if (timeRemaining <= 300) {
                timerElement.classList.remove('text-blue-600');
                timerElement.classList.add('text-red-600');
            }
            
            timeRemaining--;
        }
        
        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer(); // Initial call

        // Form submission
        const form = document.getElementById('answer-form');
        const submitBtn = document.getElementById('submit-btn');
        const errorMessage = document.getElementById('error-message');
        let currentQuestionNumber = {{ $questionNumber }};
        const totalQuestions = {{ $totalQuestions }};

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const questionId = document.getElementById('question-id').value;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            errorMessage.textContent = '';
            
            try {
                const response = await fetch(`{{ route('quiz.submit-answer', ['quizId' => $quiz->id, 'questionId' => '__QUESTION_ID__']) }}`.replace('__QUESTION_ID__', questionId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        selected_option: parseInt(formData.get('selected_option'))
                    })
                });
                
                const data = await response.json();
                
                if (data.completed) {
                    window.location.href = data.redirect;
                } else if (data.next_question) {
                    // Load next question
                    loadQuestion(data.next_question, data.question_number);
                } else if (data.error) {
                    errorMessage.textContent = data.error;
                    if (data.redirect) {
                        setTimeout(() => window.location.href = data.redirect, 2000);
                    }
                }
            } catch (error) {
                errorMessage.textContent = 'An error occurred. Please try again.';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Answer';
            }
        });

        function loadQuestion(question, questionNumber) {
            document.getElementById('question-text').textContent = question.question;
            document.getElementById('question-id').value = question.id;
            document.getElementById('current-question').textContent = questionNumber;
            
            // Update options
            const labels = document.querySelectorAll('.option-label');
            const options = [question.option_1, question.option_2, question.option_3, question.option_4];
            
            labels.forEach((label, index) => {
                if (options[index]) {
                    label.querySelector('.option-text').textContent = options[index];
                    label.style.display = 'flex';
                    label.querySelector('input').checked = false;
                } else {
                    label.style.display = 'none';
                }
            });
            
            currentQuestionNumber = questionNumber;
        }
    </script>
    @endpush
</x-app-layout>
