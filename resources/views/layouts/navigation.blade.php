<nav x-data="{ open: false }"
    class="sticky top-0 z-50 w-full border-b border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-850/80 backdrop-blur-md px-6 py-3">

    <div class="max-w-7xl mx-auto flex items-center justify-between h-12">
        <div class="flex items-center gap-8">
            <div class="shrink-0 flex items-center gap-3">

                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="flex items-center justify-center ">
                        <i class="fa-solid fa-file-lines text-blue-500 text-xl mr-2"></i>

                        <h2 class="font-semibold text-lg tracking-tight text-gray-900 dark:text-white">
                            {{ __('Note List') }}
                        </h2>
                    </div>
                </a>
            </div>

            {{-- <div class="hidden space-x-4 sm:flex">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                    class="text-sm font-medium transition-colors border-blue-500 text-gray-900 dark:text-gray-400">
                    {{ __('Dashboard') }}
                </x-nav-link>
            </div> --}}
        </div>

        <div class="flex items-center gap-4 md:gap-6">

            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 group focus:outline-none transition-all">
                            <div class="text-right hidden md:block">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ Auth::user()->name }}</div>
                                <div class="text-[11px] text-gray-400 uppercase tracking-wider">Note Taker</div>
                            </div>
                            <div
                                class="size-10 rounded-full bg-gray-700 border border-gray-600 flex items-center justify-center text-blue-500 group-hover:border-blue-500/50 transition-all overflow-hidden">
                                <span class="font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-400">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-lg text-gray-400 hover:bg-gray-800 transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }"
        class="hidden sm:hidden mt-3 pb-4 space-y-2 border-t border-gray-700">
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="dark:text-gray-400">
            {{ __('Dashboard') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('profile.edit')" class="dark:text-gray-400">
            {{ __('Profile') }}
        </x-responsive-nav-link>
    </div>
</nav>
