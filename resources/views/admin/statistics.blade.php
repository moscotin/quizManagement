<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('dashboard') }}" class="font-blue hover:text-blue-700">
                            ← Назад на главную
                        </a>
                    </div>

                    <h3 class="text-2xl font-bold mb-6 text-center">Статистика викторин</h3>

                    <!-- Overall Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-blue-100 p-4 rounded-lg text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $totalUsers }}</div>
                            <div class="text-sm text-gray-600">Всего пользователей</div>
                        </div>
                        <div class="bg-green-100 p-4 rounded-lg text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $totalQuizzes }}</div>
                            <div class="text-sm text-gray-600">Всего викторин</div>
                        </div>
                        <div class="bg-purple-100 p-4 rounded-lg text-center">
                            <div class="text-3xl font-bold text-purple-600">{{ $totalParticipants }}</div>
                            <div class="text-sm text-gray-600">Завершенных попыток</div>
                        </div>
                        <div class="bg-orange-100 p-4 rounded-lg text-center">
                            <div class="text-3xl font-bold text-orange-600">{{ $totalAttempts }}</div>
                            <div class="text-sm text-gray-600">Всего попыток</div>
                        </div>
                    </div>

                    <!-- Quiz Statistics -->
                    <div class="mb-8">
                        <h4 class="text-xl font-semibold mb-4">Статистика по викторинам</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-300">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border-b text-left">Название</th>
                                        <th class="px-4 py-2 border-b text-left">Дата начала</th>
                                        <th class="px-4 py-2 border-b text-center">Вопросов</th>
                                        <th class="px-4 py-2 border-b text-center">Необходимо правильных</th>
                                        <th class="px-4 py-2 border-b text-center">Попыток</th>
                                        <th class="px-4 py-2 border-b text-center">Завершено</th>
                                        <th class="px-4 py-2 border-b text-center">Сдано</th>
                                        <th class="px-4 py-2 border-b text-center">% успеха</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($quizStats as $stat)
                                    @php
                                        // Make sure your $stat includes quiz_id (needed!)
                                        $quizId = $stat['quiz_id'] ?? null;
                                        $canExpand = ($stat['completed_attempts'] ?? 0) > 0 && $quizId;
                                        $rowId = 'quiz-expand-' . $quizId;

                                        $participants = $quizId && isset($participantsByQuiz[$quizId])
                                            ? $participantsByQuiz[$quizId]['participants']
                                            : collect();
                                    @endphp

                                    {{-- Main row --}}
                                    <tr
                                        class="hover:bg-gray-50 {{ $canExpand ? 'cursor-pointer' : '' }}"
                                        @if($canExpand)
                                            onclick="
                const el = document.getElementById('{{ $rowId }}');
                const icon = document.getElementById('{{ $rowId }}-icon');
                const isHidden = el.classList.contains('hidden');
                el.classList.toggle('hidden');
                if(icon){ icon.textContent = isHidden ? '▾' : '▸'; }
            "
                                        title="Нажмите, чтобы показать участников"
                                        @endif
                                    >
                                        <td class="px-4 py-2 border-b">
                                            @if($canExpand)
                                                <span id="{{ $rowId }}-icon" class="mr-2 text-gray-500">▸</span>
                                            @endif
                                            {{ $stat['name'] }}
                                        </td>

                                        <td class="px-4 py-2 border-b">{{ $stat['start'] }}</td>
                                        <td class="px-4 py-2 border-b text-center">{{ $stat['total_questions'] }}</td>
                                        <td class="px-4 py-2 border-b text-center">{{ $stat['required_correct_answers'] }}</td>
                                        <td class="px-4 py-2 border-b text-center">{{ $stat['total_attempts'] }}</td>
                                        <td class="px-4 py-2 border-b text-center">{{ $stat['completed_attempts'] }}</td>
                                        <td class="px-4 py-2 border-b text-center">{{ $stat['passed_attempts'] }}</td>
                                        <td class="px-4 py-2 border-b text-center">
            <span class="font-semibold {{ $stat['pass_rate'] >= 50 ? 'text-green-600' : 'text-red-600' }}">
                {{ $stat['pass_rate'] }}%
            </span>
                                        </td>
                                    </tr>

                                    {{-- Expandable details row --}}
                                    @if($canExpand)
                                        <tr id="{{ $rowId }}" class="hidden bg-gray-50">
                                            <td colspan="8" class="px-4 py-4 border-b">
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full text-sm bg-white rounded-xl overflow-hidden shadow-sm">
                                                        <thead class="bg-gray-100">
                                                        <tr>
                                                            <th class="px-3 py-2 text-left">ФИО</th>
                                                            <th class="px-3 py-2 text-left">Email</th>
                                                            <th class="px-3 py-2 text-left">Телефон</th>
                                                            <th class="px-3 py-2 text-left">Организация</th>
                                                            <th class="px-3 py-2 text-center">Баллы</th>
                                                            <th class="px-3 py-2 text-center">Статус</th>
                                                            <th class="px-3 py-2 text-center">Начал</th>
                                                            <th class="px-3 py-2 text-center">Завершил</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @forelse($participants as $p)
                                                            <tr class="border-t">
                                                                <td class="px-3 py-2">{{ $p['user_name'] }}</td>
                                                                <td class="px-3 py-2">{{ $p['user_email'] }}</td>
                                                                <td class="px-3 py-2">{{ $p['user_phone_number'] }}</td>
                                                                <td class="px-3 py-2">{{ $p['user_organization'] }}</td>
                                                                <td class="px-3 py-2 text-center">{{ $p['score'] }}</td>
                                                                <td class="px-3 py-2 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            {{ $p['passed'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $p['passed'] ? 'Сдал' : 'Не сдал' }}
                                        </span>
                                                                </td>
                                                                <td class="px-3 py-2 text-center">{{ $p['started_at'] }}</td>
                                                                <td class="px-3 py-2 text-center">{{ $p['completed_at'] }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="8" class="px-3 py-3 text-center text-gray-500">
                                                                    Нет участников
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-2 border-b text-center text-gray-500">
                                            Нет данных
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- User Statistics -->
                    <div class="mb-8">
                        <h4 class="text-xl font-semibold mb-4">Топ пользователей (по количеству пройденных викторин)</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-300">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border-b text-left">Имя</th>
                                        <th class="px-4 py-2 border-b text-left">Email</th>
                                        <th class="px-4 py-2 border-b text-left">Организация</th>
                                        <th class="px-4 py-2 border-b text-center">Пройдено</th>
                                        <th class="px-4 py-2 border-b text-center">Сдано</th>
                                        <th class="px-4 py-2 border-b text-center">% успеха</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($userStats as $stat)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 border-b">{{ $stat['name'] }}</td>
                                            <td class="px-4 py-2 border-b">{{ $stat['email'] }}</td>
                                            <td class="px-4 py-2 border-b">{{ $stat['organization'] ?? '-' }}</td>
                                            <td class="px-4 py-2 border-b text-center">{{ $stat['total_quizzes_taken'] }}</td>
                                            <td class="px-4 py-2 border-b text-center">{{ $stat['quizzes_passed'] }}</td>
                                            <td class="px-4 py-2 border-b text-center">
                                                <span class="font-semibold {{ $stat['pass_rate'] >= 50 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $stat['pass_rate'] }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-2 border-b text-center text-gray-500">
                                                Нет данных
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
