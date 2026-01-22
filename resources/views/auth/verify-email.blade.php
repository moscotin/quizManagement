<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Спасибо за регистрацию! Перед началом работы, пожалуйста, подтвердите свой адрес электронной почты, перейдя по ссылке, которую мы только что отправили вам. Если вы не получили письмо, мы с радостью отправим его повторно.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Новая ссылка для подтверждения была отправлена на адрес электронной почты, указанный при регистрации.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Отправить письмо для подтверждения повторно') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Выйти') }}
            </button>
        </form>
    </div>
</x-guest-layout>
