<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white mb-5">Search</h1>

            <form method="GET" action="{{ route('search') }}" class="flex gap-3">
                <div class="relative flex-1">
                    <input type="text" name="q" value="{{ $query }}" placeholder="Search movies or anime..."
                           class="w-full bg-gray-800 border border-gray-700/50 text-white text-sm rounded-lg pl-10 pr-4 py-2.5 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <select name="type" class="bg-gray-800 border border-gray-700/50 text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All</option>
                    <option value="movie" {{ $type === 'movie' ? 'selected' : '' }}>🎬 Movies</option>
                    <option value="tv" {{ $type === 'tv' ? 'selected' : '' }}>📺 Anime / TV</option>
                </select>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-5 py-2 rounded-lg transition">
                    Search
                </button>
            </form>
        </div>

        @if($query)
            @if($totalResults > 0)
                <p class="text-gray-500 text-xs mb-5">{{ number_format($totalResults) }} results for "{{ $query }}"</p>
            @endif

            @if(count($movies) > 0)
                <section class="mb-10">
                    <h2 class="text-lg font-bold text-white mb-4">🎬 Movies</h2>
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                        @foreach($movies as $movie)
                            <a href="{{ route('detail.movie', $movie['id']) }}" class="group">
                                <div class="relative overflow-hidden rounded-xl card-hover transition-all duration-300">
                                    <img src="{{ app(\App\Services\TmdbService::class)->imageUrl($movie['poster_path'] ?? null) }}"
                                         alt="{{ $movie['title'] ?? 'Movie' }}" class="w-full aspect-[2/3] object-cover">
                                    @if(!empty($movie['vote_average']))
                                    <div class="absolute top-2 right-2 bg-black/70 backdrop-blur-sm px-1.5 py-0.5 rounded-full text-[10px] font-bold text-yellow-400">
                                        ⭐ {{ number_format($movie['vote_average'], 1) }}
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <p class="text-xs text-white font-medium truncate">{{ $movie['title'] ?? 'Unknown' }}</p>
                                    <p class="text-[10px] text-gray-500">{{ !empty($movie['release_date']) ? date('Y', strtotime($movie['release_date'])) : 'N/A' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if(count($tv) > 0)
                <section class="mb-10">
                    <h2 class="text-lg font-bold text-white mb-4">📺 Anime / TV</h2>
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                        @foreach($tv as $show)
                            <a href="{{ route('detail.tv', $show['id']) }}" class="group">
                                <div class="relative overflow-hidden rounded-xl card-hover transition-all duration-300">
                                    <img src="{{ app(\App\Services\TmdbService::class)->imageUrl($show['poster_path'] ?? null) }}"
                                         alt="{{ $show['name'] ?? 'TV Show' }}" class="w-full aspect-[2/3] object-cover">
                                    @if(!empty($show['vote_average']))
                                    <div class="absolute top-2 right-2 bg-black/70 backdrop-blur-sm px-1.5 py-0.5 rounded-full text-[10px] font-bold text-yellow-400">
                                        ⭐ {{ number_format($show['vote_average'], 1) }}
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <p class="text-xs text-white font-medium truncate">{{ $show['name'] ?? 'Unknown' }}</p>
                                    <p class="text-[10px] text-gray-500">{{ !empty($show['first_air_date']) ? date('Y', strtotime($show['first_air_date'])) : 'N/A' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($totalResults === 0)
                <div class="text-center py-16">
                    <p class="text-gray-500">No results found for "{{ $query }}"</p>
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <svg class="w-12 h-12 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-gray-500">Type something to search...</p>
            </div>
        @endif

    </div>
</x-app-layout>
