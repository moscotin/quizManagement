<x-guest-layout>
    <h2 class="text-2xl font-bold mb-6 text-center dark:text-[#EDEDEC]">Регистрация</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Имя -->
        <div>
            <x-input-label for="name" :value="__('Имя')" />
            <x-text-input id="name"
                          class="block mt-1 w-full"
                          type="text"
                          name="name"
                          :value="old('name')"
                          required
                          autofocus
                          autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Возраст -->
        <div class="mt-4">
            <x-input-label for="age" :value="__('Возраст')" />
            <x-text-input id="age"
                          class="block mt-1 w-full"
                          type="number"
                          name="age"
                          :value="old('age')"
                          required
                          min="1"
                          max="150" />
            <x-input-error :messages="$errors->get('age')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Электронная почта')" />
            <x-text-input id="email"
                          class="block mt-1 w-full"
                          type="email"
                          name="email"
                          :value="old('email')"
                          required
                          autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Телефон -->
        <div class="mt-4">
            <x-input-label for="phone_number" :value="__('Номер телефона')" />
            <x-text-input id="phone_number"
                          class="block mt-1 w-full"
                          type="text"
                          name="phone_number"
                          :value="old('phone_number')"
                          required
                          autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>

        <!-- Пароль -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Пароль')" />

            <x-text-input id="password"
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required
                          autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Подтверждение пароля -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Подтвердите пароль')" />

            <x-text-input id="password_confirmation"
                          class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation"
                          required
                          autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-[#333398] dark:hover:text-[#5555CC] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#333398]"
               href="{{ route('login') }}">
                {{ __('Уже зарегистрированы?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Зарегистрироваться') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
