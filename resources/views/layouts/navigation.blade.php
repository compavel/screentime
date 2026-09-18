<nav x-data="{ open: false, searchOpen: false }" class="bg-gray-900/95 backdrop-blur-md border-b border-gray-800/50 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-lg">🎬</span>
                    <span class="text-base font-bold text-white tracking-tight">ScreenTime</span>
                </a>

                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium {{ request()->routeIs('home') ? 'text-white' : 'text-gray-400 hover:text-white' }} transition">Home</a>
                    <a href="{{ route('browse', ['type' => 'movie']) }}" class="text-sm font-medium {{ request()->routeIs('browse') && request('type') === 'movie' ? 'text-white' : 'text-gray-400 hover:text-white' }} transition">Movies</a>
                    <a href="{{ route('browse', ['type' => 'tv']) }}" class="text-sm font-medium {{ request()->routeIs('browse') && request('type') === 'tv' ? 'text-white' : 'text-gray-400 hover:text-white' }} transition">TV Shows</a>
                    @auth
                        <a href="{{ route('for-you') }}" class="text-sm font-medium {{ request()->routeIs('for-you') ? 'text-white' : 'text-gray-400 hover:text-white' }} transition">For You</a>
                        <a href="{{ route('watchlist') }}" class="text-sm font-medium {{ request()->routeIs('watchlist') ? 'text-white' : 'text-gray-400 hover:text-white' }} transition">My List</a>
                    @endauth
                </div>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('search') }}" method="GET" class="hidden sm:flex items-center">
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search movies, shows..."
                               class="bg-gray-800/80 border border-gray-700/50 text-white text-xs rounded-full pl-9 pr-4 py-1.5 w-48 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none focus:w-64 transition-all">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </form>

                @auth
                    <div class="relative" x-data="{ userMenu: false }">
                        <button @click="userMenu = !userMenu" class="flex items-center gap-2 text-gray-400 hover:text-white transition">
                            <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                        <div x-show="userMenu" @click.outside="userMenu = false" x-transition
                             class="absolute right-0 mt-2 w-48 bg-gray-800 border border-gray-700 rounded-xl shadow-xl py-2 z-50">
                            <a href="{{ route('watchlist') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700/50">My Watchlist</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700/50">Profile</a>
                            <hr class="border-gray-700 my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700/50">Log Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-xs text-gray-400 hover:text-white transition">Log in</a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-1.5 rounded-full transition">Register</a>
                @endauth

                <button @click="open = !open" class="md:hidden text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-transition @click.outside="open = false" class="md:hidden border-t border-gray-800">
        <div class="px-4 py-3 space-y-2">
            <a href="{{ route('home') }}" class="block py-2 text-sm text-gray-300 hover:text-white">Home</a>
            <a href="{{ route('browse', ['type' => 'movie']) }}" class="block py-2 text-sm text-gray-300 hover:text-white">Movies</a>
            <a href="{{ route('browse', ['type' => 'tv']) }}" class="block py-2 text-sm text-gray-300 hover:text-white">TV Shows</a>
            @auth
                <a href="{{ route('for-you') }}" class="block py-2 text-sm text-gray-300 hover:text-white">For You</a>
                <a href="{{ route('watchlist') }}" class="block py-2 text-sm text-gray-300 hover:text-white">My List</a>
            @endauth
            <form action="{{ route('search') }}" method="GET" class="sm:hidden pt-2">
                <input type="text" name="q" placeholder="Search..." class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg px-4 py-2 focus:ring-indigo-500 focus:outline-none">
            </form>
        </div>
    </div>
</nav>
