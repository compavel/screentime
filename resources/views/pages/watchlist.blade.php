<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-white">My Watchlist</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('watchlist') }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ !$status ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white' }}">
                    All ({{ $stats['total'] }})
                </a>
                <a href="{{ route('watchlist', ['status' => 'want_to_watch']) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $status === 'want_to_watch' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white' }}">
                    📋 Want to Watch ({{ $stats['want_to_watch'] }})
                </a>
                <a href="{{ route('watchlist', ['status' => 'watching']) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $status === 'watching' ? 'bg-yellow-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white' }}">
                    ▶️ Watching ({{ $stats['watching'] }})
                </a>
                <a href="{{ route('watchlist', ['status' => 'watched']) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $status === 'watched' ? 'bg-green-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white' }}">
                    ✅ Watched ({{ $stats['watched'] }})
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-600/10 border border-green-600/30 text-green-400 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            @forelse($watchlists as $item)
                <div class="group relative">
                    <a href="{{ $item->media_type === 'tv' ? route('detail.tv', $item->tmdb_id) : route('detail.movie', $item->tmdb_id) }}">
                        <div class="relative overflow-hidden rounded-xl card-hover transition-all duration-300">
                            <img src="{{ app(\App\Services\TmdbService::class)->imageUrl($item->poster_path) }}"
                                 alt="{{ $item->title }}" class="w-full aspect-[2/3] object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3">
                                <span class="text-white text-xs font-medium truncate">{{ $item->title }}</span>
                                <span class="text-gray-400 text-[10px]">{{ $item->media_type === 'movie' ? '🎬 Movie' : '📺 TV' }}</span>
                            </div>
                            <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded text-[9px] font-bold
                                {{ match($item->status) {
                                    'want_to_watch' => 'bg-blue-600 text-white',
                                    'watching' => 'bg-yellow-600 text-white',
                                    'watched' => 'bg-green-600 text-white',
                                    default => 'bg-gray-600 text-gray-400',
                                } }}">
                                {{ $item->status_label }}
                            </span>
                        </div>
                    </a>
                    <div class="mt-2 flex items-center justify-between">
                        <p class="text-xs text-gray-400 truncate flex-1 mr-2">{{ $item->title }}</p>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition">
                            <form method="POST" action="{{ route('watchlist.update', $item) }}">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="bg-gray-800 border border-gray-700 text-white text-[10px] rounded px-1 py-0.5 cursor-pointer">
                                    <option value="want_to_watch" {{ $item->status === 'want_to_watch' ? 'selected' : '' }}>📋</option>
                                    <option value="watching" {{ $item->status === 'watching' ? 'selected' : '' }}>▶️</option>
                                    <option value="watched" {{ $item->status === 'watched' ? 'selected' : '' }}>✅</option>
                                </select>
                            </form>
                            <form method="POST" action="{{ route('watchlist.destroy', $item) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-[10px] px-1">✕</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="w-12 h-12 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="text-gray-500 mb-4">Your watchlist is empty</p>
                    <a href="{{ route('browse') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-5 py-2 rounded-lg transition">
                        Browse Movies & Anime
                    </a>
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>
