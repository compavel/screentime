<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white mb-5">Browse {{ $type === 'movie' ? 'Movies' : 'Anime / TV Shows' }}</h1>

            <form method="GET" action="{{ route('browse') }}" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="type" value="{{ $type }}">

                <select name="genre" class="bg-gray-800 border border-gray-700/50 text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                    <option value="">All Genres</option>
                    @foreach($genres as $genre)
                        <option value="{{ $genre['id'] }}" {{ $genreId == $genre['id'] ? 'selected' : '' }}>{{ $genre['name'] }}</option>
                    @endforeach
                </select>

                <select name="sort" class="bg-gray-800 border border-gray-700/50 text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                    <option value="popularity.desc" {{ $sort === 'popularity.desc' ? 'selected' : '' }}>Most Popular</option>
                    <option value="vote_average.desc" {{ $sort === 'vote_average.desc' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="primary_release_date.desc" {{ $sort === 'primary_release_date.desc' ? 'selected' : '' }}>Newest</option>
                </select>

                <select name="rating" class="bg-gray-800 border border-gray-700/50 text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Any Rating</option>
                    <option value="7" {{ $rating == '7' ? 'selected' : '' }}>⭐ 7+</option>
                    <option value="8" {{ $rating == '8' ? 'selected' : '' }}>⭐ 8+</option>
                    <option value="9" {{ $rating == '9' ? 'selected' : '' }}>⭐ 9+</option>
                </select>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-5 py-2 rounded-lg transition">
                    Apply
                </button>
            </form>
        </div>

        @if($totalResults > 0)
            <p class="text-gray-500 text-xs mb-5">{{ number_format($totalResults) }} results</p>
        @endif

        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3 mb-8">
            @forelse($items as $item)
                <a href="{{ $type === 'tv' ? route('detail.tv', $item['id']) : route('detail.movie', $item['id']) }}" class="group">
                    <div class="relative overflow-hidden rounded-xl card-hover transition-all duration-300">
                        <img src="{{ app(\App\Services\TmdbService::class)->imageUrl($item['poster_path'] ?? null) }}"
                             alt="{{ $item['title'] ?? $item['name'] }}" class="w-full aspect-[2/3] object-cover">
                        <div class="absolute top-2 right-2 bg-black/70 backdrop-blur-sm px-1.5 py-0.5 rounded-full text-[10px] font-bold text-yellow-400">
                            ⭐ {{ number_format($item['vote_average'], 1) }}
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="text-xs text-white font-medium truncate">{{ $item['title'] ?? $item['name'] }}</p>
                        <p class="text-[10px] text-gray-500">{{ date('Y', strtotime($item['release_date'] ?? $item['first_air_date'] ?? '')) }}</p>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-16">
                    <p class="text-gray-500">No results found.</p>
                </div>
            @endforelse
        </div>

        @if($totalPages > 1)
            <div class="flex justify-center gap-2">
                @if($page > 1)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}" class="bg-gray-800 hover:bg-gray-700 text-white text-xs px-4 py-2 rounded-lg transition">← Prev</a>
                @endif
                <span class="bg-gray-800/50 text-gray-400 text-xs px-4 py-2 rounded-lg">Page {{ $page }} of {{ min($totalPages, 500) }}</span>
                @if($page < $totalPages && $page < 500)
                    <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" class="bg-gray-800 hover:bg-gray-700 text-white text-xs px-4 py-2 rounded-lg transition">Next →</a>
                @endif
            </div>
        @endif

    </div>
</x-app-layout>
