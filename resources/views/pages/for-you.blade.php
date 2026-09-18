@php
    $tmdbService = app(\App\Services\TmdbService::class);
@endphp

<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">For You</h1>
            @if($hasRatings)
                <p class="text-gray-400 text-sm">Rekomendasi berdasarkan rating dan genre favoritmu</p>
            @else
                <p class="text-gray-400 text-sm">Rate some movies first to get personalized recommendations!</p>
            @endif
        </div>

        {{-- Genre Tags --}}
        @if(count($genreNames) > 0)
            <div class="mb-6">
                <p class="text-xs text-gray-500 mb-2">Your favorite genres:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($genreNames as $genre)
                        <span class="bg-indigo-600/20 text-indigo-400 text-xs px-3 py-1 rounded-full border border-indigo-500/30">
                            {{ $genre }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Top Rated Section --}}
        @if($topRated->count())
            <section class="mb-10">
                <h2 class="text-lg font-bold text-white mb-4">⭐ Your Top Rated</h2>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                    @foreach($topRated as $rated)
                        <a href="{{ $rated->media_type === 'tv' ? route('detail.tv', $rated->tmdb_id) : route('detail.movie', $rated->tmdb_id) }}" 
                           class="group">
                            <div class="overflow-hidden rounded-lg card-hover transition-all duration-300">
                                <img src="{{ $tmdbService->imageUrl($rated->poster_path, 'w342') }}"
                                     alt="{{ $rated->title }}" 
                                     class="w-full aspect-[2/3] object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <p class="mt-1.5 text-xs text-gray-400 truncate">{{ $rated->title }}</p>
                            <div class="flex items-center gap-0.5 mt-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-[10px] {{ $i <= $rated->rating ? 'text-yellow-400' : 'text-gray-600' }}">★</span>
                                @endfor
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Grouped Recommendations by Genre --}}
        @if(count($groupedRecommendations) > 0)
            @foreach($groupedRecommendations as $genre => $movies)
                @if(count($movies) > 0)
                    <section class="mb-10 carousel-section relative group/section">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold text-white">
                                @if($hasRatings)
                                    {{ $genre }} Picks For You
                                @else
                                    {{ $genre }}
                                @endif
                            </h2>
                        </div>
                        <div class="relative">
                            <button onclick="scrollCarousel(this, -1)" 
                                class="carousel-btn-left absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/70 hover:bg-indigo-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover/section:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <div class="carousel-track flex gap-3 overflow-x-auto pb-4 scrollbar-hide scroll-smooth" onscroll="updateCarouselArrows(this)">
                                @foreach($movies as $movie)
                                    <a href="{{ route('detail.movie', $movie['id']) }}" class="flex-none w-32 sm:w-36 group">
                                        <div class="overflow-hidden rounded-lg card-hover transition-all duration-300">
                                            <img src="{{ $tmdbService->imageUrl($movie['poster_path'] ?? null, 'w342') }}"
                                                 alt="{{ $movie['title'] ?? $movie['name'] }}" 
                                                 class="w-full aspect-[2/3] object-cover group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                        <p class="mt-1.5 text-xs text-gray-400 truncate">{{ $movie['title'] ?? $movie['name'] }}</p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[10px] text-yellow-400">⭐ {{ number_format($movie['vote_average'] ?? 0, 1) }}</span>
                                            <span class="text-[10px] text-gray-500">{{ !empty($movie['release_date']) ? date('Y', strtotime($movie['release_date'])) : '' }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <button onclick="scrollCarousel(this, 1)" 
                                class="carousel-btn-right absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/70 hover:bg-indigo-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover/section:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div class="absolute left-0 top-0 bottom-4 w-8 bg-gradient-to-r from-[#0a0a0a] to-transparent pointer-events-none z-[5]"></div>
                            <div class="absolute right-0 top-0 bottom-4 w-8 bg-gradient-to-l from-[#0a0a0a] to-transparent pointer-events-none z-[5]"></div>
                        </div>
                    </section>
                @endif
            @endforeach
        @else
            {{-- Fallback: show all recommendations --}}
            <section>
                <h2 class="text-lg font-bold text-white mb-4">
                    @if($hasRatings)
                        Recommended For You
                    @else
                        Trending Movies
                    @endif
                </h2>
                
                @if(count($allRecommendations) > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        @foreach($allRecommendations as $movie)
                            <a href="{{ route('detail.movie', $movie['id']) }}" class="group">
                                <div class="overflow-hidden rounded-lg card-hover transition-all duration-300">
                                    <img src="{{ $tmdbService->imageUrl($movie['poster_path'] ?? null, 'w342') }}"
                                         alt="{{ $movie['title'] ?? $movie['name'] }}" 
                                         class="w-full aspect-[2/3] object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                                <p class="mt-1.5 text-xs text-gray-400 truncate">{{ $movie['title'] ?? $movie['name'] }}</p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] text-yellow-400">⭐ {{ number_format($movie['vote_average'] ?? 0, 1) }}</span>
                                    <span class="text-[10px] text-gray-500">{{ !empty($movie['release_date']) ? date('Y', strtotime($movie['release_date'])) : '' }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 bg-gray-800/30 rounded-xl">
                        <p class="text-gray-500 text-sm mb-4">No recommendations yet. Start rating movies!</p>
                        <a href="{{ route('browse', ['type' => 'movie']) }}" 
                           class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2 rounded-lg transition">
                            Browse Movies
                        </a>
                    </div>
                @endif
            </section>
        @endif
    </div>

    @push('scripts')
    <script>
        function scrollCarousel(btn, direction) {
            const section = btn.closest('.carousel-section');
            const track = section.querySelector('.carousel-track');
            const scrollAmount = track.clientWidth * 0.75;
            track.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
        }

        function updateCarouselArrows(track) {
            const section = track.closest('.carousel-section');
            const leftBtn = section.querySelector('.carousel-btn-left');
            const rightBtn = section.querySelector('.carousel-btn-right');
            const maxScroll = track.scrollWidth - track.clientWidth;

            leftBtn.style.opacity = track.scrollLeft <= 10 ? '0' : '1';
            leftBtn.style.pointerEvents = track.scrollLeft <= 10 ? 'none' : 'auto';
            
            rightBtn.style.opacity = track.scrollLeft >= maxScroll - 10 ? '0' : '1';
            rightBtn.style.pointerEvents = track.scrollLeft >= maxScroll - 10 ? 'none' : 'auto';
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.carousel-track').forEach(track => {
                track.scrollLeft = 0;
                updateCarouselArrows(track);
            });
        });
    </script>
    @endpush
</x-app-layout>
