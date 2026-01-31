<x-quiz-layout>

    @php
        $durationSeconds = $quiz->time_limit * 60;
        $elapsed = $participant->started_at ? max(0, $participant->started_at->diffInSeconds(now())) : 0;
        $remaining = max(0, $durationSeconds - $elapsed);
        $qt = $currentQuestion->question_type ?? 'single_choice';
    @endphp

    <div class="relative">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 relative z-10">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl glow-orange">
                <div class="p-6">
                    <!-- Timer and Progress -->
                    <div class="flex justify-between items-center mb-6 pb-4 border-b">
                        <div class="text-sm text-gray-600">
                            Вопрос <span id="current-question">{{ $questionNumber }}</span> из {{ $totalQuestions }}
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-sm text-gray-600">Осталось времени:</div>
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

                            {{-- Optional image --}}
                            <div id="question-image-wrap" class="{{ $currentQuestion->image ? '' : 'hidden' }} mb-4">
                                <img id="question-image"
                                     src="{{ $currentQuestion->image ? asset('storage/'.$currentQuestion->image) : '' }}"
                                     alt=""
                                     class="max-w-full rounded-xl border">
                            </div>
                        </div>

                        <form id="answer-form" novalidate>
                            @csrf
                            <input type="hidden" name="question_id" id="question-id" value="{{ $currentQuestion->id }}">
                            <input type="hidden" name="question_type" id="question-type" value="{{ $qt }}">

                            {{-- SINGLE CHOICE (radio) --}}
                            <div id="single-choice-block" class="{{ $qt === 'single_choice' ? '' : 'hidden' }}">
                                <div class="space-y-3" id="single-choice-options">
                                    @for ($i = 1; $i <= 6; $i++)
                                        @php $opt = $currentQuestion->{'option_'.$i}; @endphp
                                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition option-label"
                                               data-index="{{ $i }}"
                                               style="{{ $opt ? '' : 'display:none' }}">
                                            <input type="radio" name="selected_option" value="{{ $i }}" class="mr-3 h-5 w-5">
                                            <span class="option-text">{{ $opt }}</span>
                                        </label>
                                    @endfor
                                </div>
                            </div>

                            {{-- MULTIPLE CHOICE (checkbox) --}}
                            <div id="multiple-choice-block" class="{{ $qt === 'multiple_choice' ? '' : 'hidden' }}">
                                <div class="space-y-3" id="multiple-choice-options">
                                    @for ($i = 1; $i <= 6; $i++)
                                        @php $opt = $currentQuestion->{'option_'.$i}; @endphp
                                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition option-label-multi"
                                               data-index="{{ $i }}"
                                               style="{{ $opt ? '' : 'display:none' }}">
                                            <input type="checkbox" name="selected_options[]" value="{{ $i }}" class="mr-3 h-5 w-5">
                                            <span class="option-text">{{ $opt }}</span>
                                        </label>
                                    @endfor
                                </div>
                                <div class="text-xs text-gray-500 mt-2">Можно выбрать несколько вариантов.</div>
                            </div>

                            {{-- FILL IN THE BLANK / TEXT --}}
                            <div id="text-block" class="{{ in_array($qt, ['fill_in_the_blank','text']) ? '' : 'hidden' }}">
                                <label class="block text-sm text-gray-600 mb-2">Ваш ответ:</label>
                                <input type="text"
                                       name="answer_text"
                                       id="answer-text"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Введите ответ...">
                            </div>

                            {{-- MATCHING --}}
                            <div id="matching-block" class="{{ $qt === 'matching' ? '' : 'hidden' }}">
                                <div class="text-sm text-gray-600 mb-3">
                                    Соотнесите элементы. Выберите пару для каждой строки.
                                </div>

                                <div id="matching-ui" data-matching='@json($currentQuestion->matching_pairs ?? [])'></div>
                                <input type="hidden" name="matching_response" id="matching-response" value="">
                            </div>

                            <div class="mt-6 flex justify-between items-center">
                                <div id="error-message" class="text-red-600 text-sm"></div>
                                <button type="submit" id="submit-btn" class="btn-purple btn-hover-white btn-glow">
                                    Отправить ответ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Background decorative elements -->
            <div class="absolute -top-28 -right-20 w-48 h-48 bg-[url('/img/quiz_decorations/en1.webp')] bg-contain bg-no-repeat -z-1"></div>
        </div>

        <div class="absolute bottom-0 left-0 w-full h-full bg-[url('/img/quiz_decorations/en2.webp')] bg-contain bg-no-repeat -z-1"></div>
        <div class="absolute bottom-0 -right-12 w-64 h-64 bg-[url('/img/quiz_decorations/en3.webp')] bg-contain bg-no-repeat -z-1"></div>
    </div>

    @push('scripts')
        <script>
            // ----------------------------
            // Timer
            // ----------------------------
            let timeRemaining = parseInt(@json($remaining), 10);
            if (Number.isNaN(timeRemaining) || timeRemaining < 0) timeRemaining = 0;

            const timerElement = document.getElementById('timer');
            const completeUrl = @json(route('quiz.complete', $quiz->id));

            function renderTimer() {
                const t = Math.max(0, Math.floor(timeRemaining));
                const minutes = Math.floor(t / 60);
                const seconds = t % 60;
                timerElement.textContent = `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;

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
                timeRemaining = Math.floor(timeRemaining) - 1;
            }

            const timerInterval = setInterval(tick, 1000);
            tick();

            // ----------------------------
            // Blocks enable/disable (IMPORTANT FIX)
            // ----------------------------
            const singleBlock = document.getElementById('single-choice-block');
            const multiBlock  = document.getElementById('multiple-choice-block');
            const textBlock   = document.getElementById('text-block');
            const matchBlock  = document.getElementById('matching-block');

            function setBlockEnabled(blockEl, enabled) {
                blockEl.querySelectorAll('input, select, textarea').forEach(el => {
                    el.disabled = !enabled;
                });
            }

            function clearAllInputs() {
                document.querySelectorAll('input[type="radio"][name="selected_option"]').forEach(r => r.checked = false);
                document.querySelectorAll('input[type="checkbox"][name="selected_options[]"]').forEach(c => c.checked = false);
                const at = document.getElementById('answer-text');
                if (at) at.value = '';
                const mr = document.getElementById('matching-response');
                if (mr) mr.value = '';
                // clear matching radios too (if any)
                document.querySelectorAll('#matching-ui input[type="radio"]').forEach(r => r.checked = false);
            }

            function showOnlyBlock(type) {
                // hide all
                singleBlock.classList.add('hidden');
                multiBlock.classList.add('hidden');
                textBlock.classList.add('hidden');
                matchBlock.classList.add('hidden');

                // disable all inputs in all blocks
                setBlockEnabled(singleBlock, false);
                setBlockEnabled(multiBlock, false);
                setBlockEnabled(textBlock, false);
                setBlockEnabled(matchBlock, false);

                // clear values when switching type
                clearAllInputs();

                if (type === 'multiple_choice') {
                    multiBlock.classList.remove('hidden');
                    setBlockEnabled(multiBlock, true);
                } else if (type === 'fill_in_the_blank' || type === 'text') {
                    textBlock.classList.remove('hidden');
                    setBlockEnabled(textBlock, true);
                } else if (type === 'matching') {
                    matchBlock.classList.remove('hidden');
                    setBlockEnabled(matchBlock, true);
                } else {
                    singleBlock.classList.remove('hidden');
                    setBlockEnabled(singleBlock, true);
                }
            }

            function setQuestionImage(imagePathOrNull) {
                const wrap = document.getElementById('question-image-wrap');
                const img  = document.getElementById('question-image');

                if (imagePathOrNull && String(imagePathOrNull).trim() !== '') {
                    img.src = imagePathOrNull.startsWith('http')
                        ? imagePathOrNull
                        : @json(asset('storage')) + '/' + imagePathOrNull.replace(/^\/+/, '');
                    wrap.classList.remove('hidden');
                } else {
                    img.src = '';
                    wrap.classList.add('hidden');
                }
            }

            function updateOptionsSingle(question) {
                const labels = document.querySelectorAll('#single-choice-options .option-label');
                labels.forEach((label) => {
                    const idx = parseInt(label.dataset.index, 10);
                    const textEl = label.querySelector('.option-text');
                    const input = label.querySelector('input[type="radio"]');
                    const value = question[`option_${idx}`];

                    if (value !== null && value !== undefined && String(value).trim() !== '') {
                        textEl.textContent = value;
                        input.checked = false;
                        label.style.display = 'flex';
                    } else {
                        input.checked = false;
                        label.style.display = 'none';
                    }
                });
            }

            function updateOptionsMulti(question) {
                const labels = document.querySelectorAll('#multiple-choice-options .option-label-multi');
                labels.forEach((label) => {
                    const idx = parseInt(label.dataset.index, 10);
                    const textEl = label.querySelector('.option-text');
                    const input = label.querySelector('input[type="checkbox"]');
                    const value = question[`option_${idx}`];

                    if (value !== null && value !== undefined && String(value).trim() !== '') {
                        textEl.textContent = value;
                        input.checked = false;
                        label.style.display = 'flex';
                    } else {
                        input.checked = false;
                        label.style.display = 'none';
                    }
                });
            }

            // ----------------------------
            // MATCHING UI (RADIO LISTS)
            // ----------------------------
            function renderMatchingUI(matchingPairs) {
                const container = document.getElementById('matching-ui');
                const hidden = document.getElementById('matching-response');

                container.innerHTML = '';
                hidden.value = '';

                let pairs = [];
                if (Array.isArray(matchingPairs)) {
                    pairs = matchingPairs;
                } else if (matchingPairs && typeof matchingPairs === 'object') {
                    pairs = Object.entries(matchingPairs).map(([left, right]) => ({ left, right }));
                }

                const leftItems = pairs.map(p => p.left);
                const rightItems = pairs.map(p => p.right);

                // store selection per left item
                const selections = {};

                const updateHidden = () => {
                    const map = {};
                    for (const [l, v] of Object.entries(selections)) {
                        if (l && v) map[l] = v;
                    }
                    hidden.value = JSON.stringify(map);
                };

                const makeSafe = (s) => String(s ?? '').toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_а-яё-]/gi, '');
                const groupPrefix = `match_${Date.now()}_`;

                leftItems.forEach((left, idx) => {
                    const row = document.createElement('div');
                    row.className = 'p-3 border rounded-lg mb-2';

                    const leftEl = document.createElement('div');
                    leftEl.className = 'font-medium mb-2';
                    leftEl.textContent = left;

                    const groupName = groupPrefix + idx + '_' + makeSafe(left);

                    const optionsWrap = document.createElement('div');
                    optionsWrap.className = 'grid grid-cols-1 sm:grid-cols-2 gap-2';

                    // "Not selected" option (acts like placeholder)
                    // const noneLabel = document.createElement('label');
                    // noneLabel.className = 'flex items-center gap-2 p-2 border rounded-lg cursor-pointer hover:bg-gray-50';

                    // const noneRadio = document.createElement('input');
                    // noneRadio.type = 'radio';
                    // noneRadio.name = groupName;
                    // noneRadio.value = '';
                    // noneRadio.checked = true;
                    //
                    // const noneText = document.createElement('span');
                    // noneText.className = 'text-gray-500';
                    // noneText.textContent = 'Не выбрано';
                    //
                    // noneRadio.addEventListener('change', () => {
                    //     selections[left] = '';
                    //     updateHidden();
                    // });
                    //
                    // noneLabel.appendChild(noneRadio);
                    // noneLabel.appendChild(noneText);
                    // optionsWrap.appendChild(noneLabel);

                    // Right options as radios
                    rightItems.forEach((r) => {
                        const label = document.createElement('label');
                        label.className = 'flex items-center gap-2 p-2 border rounded-lg cursor-pointer hover:bg-gray-50';

                        const radio = document.createElement('input');
                        radio.type = 'radio';
                        radio.name = groupName;
                        radio.value = r;

                        const text = document.createElement('span');
                        text.textContent = r;

                        radio.addEventListener('change', () => {
                            selections[left] = r;
                            updateHidden();
                        });

                        label.appendChild(radio);
                        label.appendChild(text);
                        optionsWrap.appendChild(label);
                    });

                    row.appendChild(leftEl);
                    row.appendChild(optionsWrap);
                    container.appendChild(row);
                });
            }

            // ----------------------------
            // Form submission
            // ----------------------------
            const form = document.getElementById('answer-form');
            const submitBtn = document.getElementById('submit-btn');
            const errorMessage = document.getElementById('error-message');

            const quizId = Number(@json($quiz->id));

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const qType = document.getElementById('question-type').value || 'single_choice';
                const questionId = document.getElementById('question-id').value;
                const submitUrl = `/quiz/${quizId}/question/${questionId}/answer`;

                errorMessage.textContent = '';

                let payload = {};

                if (qType === 'multiple_choice') {
                    const selected = Array.from(document.querySelectorAll('input[name="selected_options[]"]:checked'))
                        .map(el => parseInt(el.value, 10))
                        .filter(n => Number.isInteger(n));

                    if (selected.length === 0) {
                        errorMessage.textContent = 'Пожалуйста, выберите хотя бы один вариант.';
                        return;
                    }
                    payload.selected_options = selected;

                } else if (qType === 'fill_in_the_blank' || qType === 'text') {
                    const v = (document.getElementById('answer-text').value || '').trim();
                    if (!v) {
                        errorMessage.textContent = 'Пожалуйста, введите ответ.';
                        return;
                    }
                    payload.answer_text = v;

                } else if (qType === 'matching') {
                    const raw = document.getElementById('matching-response').value;
                    if (!raw) {
                        errorMessage.textContent = 'Пожалуйста, составьте пары.';
                        return;
                    }
                    try {
                        payload.matching_response = JSON.parse(raw);
                    } catch {
                        errorMessage.textContent = 'Ошибка формата пар. Попробуйте еще раз.';
                        return;
                    }

                } else {
                    const selected = document.querySelector('input[name="selected_option"]:checked')?.value;
                    if (!selected) {
                        errorMessage.textContent = 'Пожалуйста, выберите вариант ответа.';
                        return;
                    }
                    payload.selected_option = parseInt(selected, 10);
                }

                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';

                try {
                    const response = await fetch(submitUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const contentType = response.headers.get('content-type') || '';
                    if (!contentType.includes('application/json')) throw new Error('Unexpected response (not JSON).');

                    const data = await response.json();

                    if (data.completed && data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }

                    if (data.error) {
                        errorMessage.textContent = data.error;
                        if (data.redirect) setTimeout(() => window.location.href = data.redirect, 1500);
                        return;
                    }

                    if (data.next_question && typeof data.question_number !== 'undefined') {
                        loadQuestion(data.next_question, data.question_number);
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
                document.getElementById('current-question').textContent = (questionNumber + 1);

                const qType = question.question_type ?? 'single_choice';
                document.getElementById('question-type').value = qType;

                showOnlyBlock(qType);

                setQuestionImage(question.image ?? null);

                updateOptionsSingle(question);
                updateOptionsMulti(question);

                if (qType === 'matching') {
                    renderMatchingUI(question.matching_pairs ?? []);
                } else {
                    const mu = document.getElementById('matching-ui');
                    const mr = document.getElementById('matching-response');
                    if (mu) mu.innerHTML = '';
                    if (mr) mr.value = '';
                }
            }

            // Initial init
            (function initFirstQuestion() {
                const initialType = document.getElementById('question-type').value || 'single_choice';
                showOnlyBlock(initialType);

                if (initialType === 'matching') {
                    const container = document.getElementById('matching-ui');
                    const mp = container ? container.dataset.matching : null;
                    if (mp) { try { renderMatchingUI(JSON.parse(mp)); } catch {} }
                }
            })();
        </script>
    @endpush
</x-quiz-layout>
