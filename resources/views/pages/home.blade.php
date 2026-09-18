<x-app-layout>
    {{-- Hero Section --}}
    @php
        $featured = $trendingMovies[0] ?? null;
        $tmdbService = app(\App\Services\TmdbService::class);
    @endphp

    @if($featured)
        <div class="relative h-[70vh] min-h-[500px]">
            <img src="{{ $tmdbService->imageUrl($featured['backdrop_path'] ?? null, 'original') }}"
                 alt="{{ $featured['title'] }}" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/80 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>

            <div class="relative z-10 h-full flex items-center">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    <div class="max-w-xl">
                        <span class="inline-block bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-full mb-4">🔥 #1 TRENDING</span>
                        <h1 class="text-4xl sm:text-5xl font-bold text-white mb-3">{{ $featured['title'] }}</h1>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="text-yellow-400 font-bold">⭐ {{ number_format($featured['vote_average'], 1) }}</span>
                            <span class="text-gray-400">|</span>
                            <span class="text-gray-400 text-sm">{{ date('Y', strtotime($featured['release_date'])) }}</span>
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed mb-6 line-clamp-3">{{ $featured['overview'] }}</p>
                        <div class="flex gap-3">
                            <a href="{{ route('detail.movie', $featured['id']) }}" class="bg-white hover:bg-gray-200 text-gray-900 font-bold px-6 py-3 rounded-lg text-sm transition flex items-center gap-2">
                                ▶ Detail
                            </a>
                            @auth
                                <form method="POST" action="{{ route('watchlist.store') }}">
                                    @csrf
                                    <input type="hidden" name="tmdb_id" value="{{ $featured['id'] }}">
                                    <input type="hidden" name="title" value="{{ $featured['title'] }}">
                                    <input type="hidden" name="poster_path" value="{{ $featured['poster_path'] ?? '' }}">
                                    <input type="hidden" name="media_type" value="movie">
                                    <input type="hidden" name="status" value="want_to_watch">
                                    <button type="submit" class="bg-gray-700/80 hover:bg-gray-600 text-white font-bold px-6 py-3 rounded-lg text-sm transition flex items-center gap-2">
                                        + My List
                                    </button>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20 pb-12">

        {{-- Trending Movies --}}
        <section class="mb-10 carousel-section relative group/section">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-white">Trending Movies</h2>
                <a href="{{ route('browse', ['type' => 'movie']) }}" class="text-indigo-400 hover:text-indigo-300 text-xs font-medium">View All →</a>
            </div>
            <div class="relative">
                <button onclick="scrollCarousel(this, -1)" 
                    class="carousel-btn-left absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/70 hover:bg-indigo-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover/section:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="carousel-track flex gap-4 overflow-x-auto pb-4 scrollbar-hide scroll-smooth" onscroll="updateCarouselArrows(this)">
                    @foreach(array_slice($trendingMovies, 0, 10) as $movie)
                        <a href="{{ route('detail.movie', $movie['id']) }}" class="flex-none w-36 sm:w-40 group">
                            <div class="relative overflow-hidden rounded-xl card-hover transition-all duration-300">
                                <img src="{{ $tmdbService->imageUrl($movie['poster_path']) }}"
                                     alt="{{ $movie['title'] }}" class="w-full aspect-[2/3] object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3">
                                    <span class="text-yellow-400 text-xs font-bold mb-1">⭐ {{ number_format($movie['vote_average'], 1) }}</span>
                                    <span class="text-white text-xs line-clamp-2">{{ $movie['overview'] }}</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs text-white font-medium truncate">{{ $movie['title'] }}</p>
                                <p class="text-[10px] text-gray-500">{{ date('Y', strtotime($movie['release_date'])) }} · Movie</p>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button onclick="scrollCarousel(this, 1)" 
                    class="carousel-btn-right absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/70 hover:bg-indigo-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover/section:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div class="absolute left-0 top-0 bottom-4 w-8 bg-gradient-to-r from-gray-900 to-transparent pointer-events-none z-[5]"></div>
                <div class="absolute right-0 top-0 bottom-4 w-8 bg-gradient-to-l from-gray-900 to-transparent pointer-events-none z-[5]"></div>
            </div>
        </section>

        {{-- Trending Anime / TV --}}
        <section class="mb-10 carousel-section relative group/section">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-white">Trending Anime / TV</h2>
                <a href="{{ route('browse', ['type' => 'tv']) }}" class="text-indigo-400 hover:text-indigo-300 text-xs font-medium">View All →</a>
            </div>
            <div class="relative">
                <button onclick="scrollCarousel(this, -1)" 
                    class="carousel-btn-left absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/70 hover:bg-indigo-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover/section:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="carousel-track flex gap-4 overflow-x-auto pb-4 scrollbar-hide scroll-smooth" onscroll="updateCarouselArrows(this)">
                    @foreach(array_slice($trendingTv, 0, 10) as $show)
                        <a href="{{ route('detail.tv', $show['id']) }}" class="flex-none w-36 sm:w-40 group">
                            <div class="relative overflow-hidden rounded-xl card-hover transition-all duration-300">
                                <img src="{{ $tmdbService->imageUrl($show['poster_path']) }}"
                                     alt="{{ $show['name'] }}" class="w-full aspect-[2/3] object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3">
                                    <span class="text-yellow-400 text-xs font-bold mb-1">⭐ {{ number_format($show['vote_average'], 1) }}</span>
                                    <span class="text-white text-xs line-clamp-2">{{ $show['overview'] }}</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs text-white font-medium truncate">{{ $show['name'] }}</p>
                                <p class="text-[10px] text-gray-500">{{ date('Y', strtotime($show['first_air_date'])) }} · TV</p>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button onclick="scrollCarousel(this, 1)" 
                    class="carousel-btn-right absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/70 hover:bg-indigo-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover/section:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div class="absolute left-0 top-0 bottom-4 w-8 bg-gradient-to-r from-gray-900 to-transparent pointer-events-none z-[5]"></div>
                <div class="absolute right-0 top-0 bottom-4 w-8 bg-gradient-to-l from-gray-900 to-transparent pointer-events-none z-[5]"></div>
            </div>
        </section>

        {{-- Popular Genres --}}
        <section class="mb-10">
            <h2 class="text-lg font-bold text-white mb-4">Popular Genres</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @php
                    $genreColors = [
                        'Action' => 'from-red-600 to-red-900',
                        'Adventure' => 'from-green-600 to-green-900',
                        'Animation' => 'from-pink-600 to-pink-900',
                        'Comedy' => 'from-yellow-600 to-yellow-900',
                        'Drama' => 'from-blue-600 to-blue-900',
                        'Fantasy' => 'from-purple-600 to-purple-900',
                        'Horror' => 'from-gray-700 to-gray-900',
                        'Mystery' => 'from-indigo-600 to-indigo-900',
                        'Romance' => 'from-pink-500 to-pink-800',
                        'Sci-Fi' => 'from-cyan-600 to-cyan-900',
                        'Thriller' => 'from-orange-600 to-orange-900',
                        'War' => 'from-amber-700 to-amber-900',
                    ];
                @endphp
                @foreach(array_slice($genres ?? [], 0, 6) as $genre)
                    <a href="{{ route('browse', ['type' => 'movie', 'genre' => $genre['id']]) }}"
                       class="relative overflow-hidden rounded-xl h-24 flex items-end p-3 card-hover transition-all duration-300 bg-gradient-to-br {{ $genreColors[$genre['name']] ?? 'from-gray-600 to-gray-900' }}">
                        <span class="text-white font-bold text-sm relative z-10">{{ $genre['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Popular Movies --}}
        <section class="mb-10 carousel-section relative group/section">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-white">Popular Movies</h2>
                <a href="{{ route('browse', ['type' => 'movie', 'sort' => 'vote_average.desc']) }}" class="text-indigo-400 hover:text-indigo-300 text-xs font-medium">View All →</a>
            </div>
            <div class="relative">
                <button onclick="scrollCarousel(this, -1)" 
                    class="carousel-btn-left absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/70 hover:bg-indigo-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover/section:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="carousel-track flex gap-4 overflow-x-auto pb-4 scrollbar-hide scroll-smooth" onscroll="updateCarouselArrows(this)">
                    @foreach($popularMovies as $movie)
                        <a href="{{ route('detail.movie', $movie['id']) }}" class="flex-none w-36 sm:w-40 group">
                            <div class="relative overflow-hidden rounded-xl card-hover transition-all duration-300">
                                <img src="{{ $tmdbService->imageUrl($movie['poster_path']) }}"
                                     alt="{{ $movie['title'] }}" class="w-full aspect-[2/3] object-cover">
                                <div class="absolute top-2 right-2 bg-black/70 backdrop-blur-sm px-2 py-0.5 rounded-full text-[10px] font-bold text-yellow-400">
                                    ⭐ {{ number_format($movie['vote_average'], 1) }}
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs text-white font-medium truncate">{{ $movie['title'] }}</p>
                                <p class="text-[10px] text-gray-500">{{ date('Y', strtotime($movie['release_date'])) }} · Movie</p>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button onclick="scrollCarousel(this, 1)" 
                    class="carousel-btn-right absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/70 hover:bg-indigo-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover/section:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div class="absolute left-0 top-0 bottom-4 w-8 bg-gradient-to-r from-gray-900 to-transparent pointer-events-none z-[5]"></div>
                <div class="absolute right-0 top-0 bottom-4 w-8 bg-gradient-to-l from-gray-900 to-transparent pointer-events-none z-[5]"></div>
            </div>
        </section>

        {{-- Popular Anime / TV --}}
        <section class="carousel-section relative group/section">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-white">Popular Anime / TV</h2>
                <a href="{{ route('browse', ['type' => 'tv', 'sort' => 'vote_average.desc']) }}" class="text-indigo-400 hover:text-indigo-300 text-xs font-medium">View All →</a>
            </div>
            <div class="relative">
                <button onclick="scrollCarousel(this, -1)" 
                    class="carousel-btn-left absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/70 hover:bg-indigo-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover/section:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="carousel-track flex gap-4 overflow-x-auto pb-4 scrollbar-hide scroll-smooth" onscroll="updateCarouselArrows(this)">
                    @foreach($popularTv as $show)
                        <a href="{{ route('detail.tv', $show['id']) }}" class="flex-none w-36 sm:w-40 group">
                            <div class="relative overflow-hidden rounded-xl card-hover transition-all duration-300">
                                <img src="{{ $tmdbService->imageUrl($show['poster_path']) }}"
                                     alt="{{ $show['name'] }}" class="w-full aspect-[2/3] object-cover">
                                <div class="absolute top-2 right-2 bg-black/70 backdrop-blur-sm px-2 py-0.5 rounded-full text-[10px] font-bold text-yellow-400">
                                    ⭐ {{ number_format($show['vote_average'], 1) }}
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs text-white font-medium truncate">{{ $show['name'] }}</p>
                                <p class="text-[10px] text-gray-500">{{ date('Y', strtotime($show['first_air_date'])) }} · TV</p>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button onclick="scrollCarousel(this, 1)" 
                    class="carousel-btn-right absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-black/70 hover:bg-indigo-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover/section:opacity-100 transition-all duration-300 backdrop-blur-sm border border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div class="absolute left-0 top-0 bottom-4 w-8 bg-gradient-to-r from-gray-900 to-transparent pointer-events-none z-[5]"></div>
                <div class="absolute right-0 top-0 bottom-4 w-8 bg-gradient-to-l from-gray-900 to-transparent pointer-events-none z-[5]"></div>
            </div>
        </section>

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
