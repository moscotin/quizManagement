<x-guest-layout>
    <h2 class="text-2xl font-bold mb-4 text-center dark:text-[#EDEDEC]">Восстановление пароля</h2>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Забыли пароль? Ничего страшного. Укажите адрес электронной почты, и мы отправим вам ссылку для сброса пароля, с помощью которой вы сможете задать новый.') }}
    </div>

    <!-- Статус сессии -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Электронная почта')" />
            <x-text-input id="email"
                          class="block mt-1 w-full"
                          type="email"
                          name="email"
                          :value="old('email')"
                          required
                          autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Отправить ссылку для сброса пароля') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
