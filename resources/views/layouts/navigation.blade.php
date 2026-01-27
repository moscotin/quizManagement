<nav
    x-data="{ open: false }"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
    class="relative z-50 bg-white dark:bg-[#1a1a1a]
           border-b border-gray-100 dark:border-gray-800
           h-44 bg-[url('/img/nav_bg.webp')] bg-full"
>
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex justify-between h-full">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="/img/owl.webp" alt="Logo" class="h-32 mt-2" />
                    </a>
                </div>
            </div>

            <!-- Desktop Right -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="me-4">
                    <img src="/img/hat.webp" alt="Hat" class="h-12 mt-2" />
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2
                                   text-sm font-medium rounded-md
                                   text-black bg-white border border-gray-950
                                   dark:bg-[#1a1a1a]
                                   hover:text-gray-700 dark:hover:text-gray-300
                                   focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Профиль
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault(); this.closest('form').submit();">
                                Выйти
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center sm:hidden">
                <button
                    @click="open = !open"
                    class="inline-flex items-center justify-center p-2
                           rounded-md text-gray-500
                           hover:bg-gray-100 dark:hover:bg-gray-900
                           focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open }"
                              class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{ 'hidden': !open }"
                              class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile overlay -->
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-[998] bg-black/40 sm:hidden"
        @click="open = false"
    ></div>

    <!-- Mobile menu panel -->
    <div
        x-show="open"
        x-transition
        @keydown.escape.window="open = false"
        class="fixed inset-x-0 top-44 z-[999]
               sm:hidden bg-white dark:bg-[#1a1a1a]
               border-t border-gray-200 dark:border-gray-800"
    >
        <div class="pt-4 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                На главную
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-4 border-t border-gray-200 dark:border-gray-800">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-[#EDEDEC]">
                    {{ Auth::user()->name }}
                </div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-400">
                    {{ Auth::user()->email }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Профиль
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                        Выйти
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
