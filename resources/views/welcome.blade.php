<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex items-center lg:justify-center min-h-screen flex-col height-full relative overflow-hidden">
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0 height-full">
            <main class="flex w-full flex-col-reverse lg:flex-row height-full relative lg:pt-0 pt-24">
                <div class="absolute w-full px-12 top-0">
                    <div class="flex w-full mt-4">
                        <!-- first image floated left -->
                        <div class="lg:w-1/2 w-full block lg:text-left text-center">
                            <img src="/img/owl.webp" alt="Decor Left" class="h-24 lg:mx-0 mx-auto" />
                        </div>
                        <!-- second image floated right -->
                        <div class="w-1/2 lg:block hidden text-right">
                            <img src="/img/dep.webp" alt="Decor Right" class="h-16 inline-flex mr-6" />
                            <img src="/img/pres.webp" alt="Decor Right" class="h-12 inline-flex mr-6" />
                        </div>
                    </div>
                </div>
                <!-- buttons for register and login -->
                <div class="flex flex-col lg:w-1/2 w-full lg:justify-center mt-6 lg:mt-0 height-full p-4 px-12">
                    <h1 class="lg:text-4xl text-xl uppercase font-bold mb-4 dark:text-[#EDEDEC]">Городской турнир <br />по интеллектуальным играм</h1>
                    <h1 class="lg:text-6xl text-3xl uppercase font-blue font-bold mb-6 dark:text-[#C4C4C4]">"Кубок Пресни"</h1>
                    <div class="flex gap-4 mt-12 flex-wrap">
                        @if (Route::has('login'))
                            @auth
                                <a
                                    href="{{ url('/dashboard') }}"
                                    class="btn-glow btn-purple btn-hover-white h-12"
                                >
                                    Перейти к викторинам
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="btn-glow btn-purple btn-hover-white h-12"
                                >
                                    Вход
                                </a>
                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        class="btn-glow btn-purple btn-hover-white h-12"
                                    >
                                        Регистрация
                                    </a>
                                @endif
                                <div class="inline-flex">
                                    <img src="/img/hat.webp" alt="Hat" class="h-16 ml-4 mt-1" />
                                </div>
                            @endauth
                        @endif
                    </div>
                </div>
                <div class="flex-col w-1/2 lg:justify-center mt-6 lg:mt-0 bg-[url('/img/bg_blueGrid.webp')] bg-cover bg-center h-screen lg:flex hidden">
                    <!-- Decorative right side, can be left empty or add more decor -->
                </div>
            </main>
        </div>

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif

        <div class="absolute bottom-0 mt-10 w-full px-12 mb-4">
            <div class="flex w-full">
                <!-- first image floated left -->
                <div class="w-full lg:block text-center">
                    <img src="/img/mow.webp" alt="MOSKWA" class="h-24 mx-auto" />
                </div>
            </div>
        </div>
    </body>
</html>
