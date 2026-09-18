@php
    $title = $item['title'] ?? $item['name'];
    $date = $item['release_date'] ?? $item['first_air_date'] ?? '';
    $year = $date ? date('Y', strtotime($date)) : 'N/A';
    $runtime = $item['runtime'] ?? null;
    $seasons = $item['number_of_seasons'] ?? null;
    $tmdbService = app(\App\Services\TmdbService::class);
    $director = collect($item['credits']['crew'] ?? [])->firstWhere('job', 'Director');
    $writers = collect($item['credits']['crew'] ?? [])->filter(fn($c) => in_array($c['job'], ['Writer', 'Screenplay', 'Story']))->take(3);
    $cast = collect($item['credits']['cast'] ?? [])->take(5);
@endphp

<x-app-layout>
    {{-- Backdrop Hero --}}
    <div class="relative h-[50vh] min-h-[350px]">
        <img src="{{ $tmdbService->imageUrl($item['backdrop_path'] ?? null, 'original') }}"
             alt="{{ $title }}" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-[#0f1117] via-[#0f1117]/60 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0f1117]/80 to-transparent"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-48 relative z-10 pb-12">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Poster --}}
            <div class="flex-none mx-auto lg:mx-0">
                <img src="{{ $tmdbService->imageUrl($item['poster_path'] ?? null) }}"
                     alt="{{ $title }}" class="w-44 sm:w-52 rounded-xl shadow-2xl ring-1 ring-gray-800">
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">{{ $title }}</h1>

                <div class="flex flex-wrap items-center gap-3 mb-4 text-sm">
                    <span class="flex items-center gap-1 text-yellow-400 font-bold">⭐ {{ number_format($item['vote_average'], 1) }}</span>
                    <span class="text-gray-600">·</span>
                    <span class="text-gray-400">{{ number_format($item['vote_count']) }} votes</span>
                    <span class="text-gray-600">·</span>
                    <span class="text-gray-400">{{ $year }}</span>
                    @if($runtime)
                        <span class="text-gray-600">·</span>
                        <span class="text-gray-400">{{ floor($runtime / 60) }}h {{ $runtime % 60 }}m</span>
                    @endif
                    @if($seasons)
                        <span class="text-gray-600">·</span>
                        <span class="text-gray-400">{{ $seasons }} Season{{ $seasons > 1 ? 's' : '' }}</span>
                    @endif
                </div>

                @if(!empty($item['genres']))
                    <div class="flex flex-wrap gap-2 mb-5">
                        @foreach($item['genres'] as $genre)
                            <a href="{{ route('browse', ['type' => $type, 'genre' => $genre['id']]) }}"
                               class="bg-gray-800/80 hover:bg-gray-700 text-gray-300 text-xs px-3 py-1 rounded-full border border-gray-700/50 transition">
                                {{ $genre['name'] }}
                            </a>
                        @endforeach
                    </div>
                @endif

                @if($item['tagline'])
                    <p class="text-gray-500 text-sm italic mb-4">"{{ $item['tagline'] }}"</p>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-3 mb-6">
                    @auth
                        <form method="POST" action="{{ route('watchlist.store') }}" class="flex gap-2">
                            @csrf
                            <input type="hidden" name="tmdb_id" value="{{ $item['id'] }}">
                            <input type="hidden" name="title" value="{{ $title }}">
                            <input type="hidden" name="poster_path" value="{{ $item['poster_path'] ?? '' }}">
                            <input type="hidden" name="media_type" value="{{ $type }}">
                            <select name="status" class="bg-gray-800 border border-gray-700 text-white text-xs rounded-lg px-3 py-2">
                                <option value="want_to_watch" {{ $watchlist && $watchlist->status === 'want_to_watch' ? 'selected' : '' }}>📋 Want to Watch</option>
                                <option value="watching" {{ $watchlist && $watchlist->status === 'watching' ? 'selected' : '' }}>▶️ Watching</option>
                                <option value="watched" {{ $watchlist && $watchlist->status === 'watched' ? 'selected' : '' }}>✅ Watched</option>
                            </select>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium px-4 py-2 rounded-lg transition">
                                {{ $watchlist ? 'Update' : '+ My List' }}
                            </button>
                        </form>
                        @if($watchlist)
                            <form method="POST" action="{{ route('watchlist.destroy', $watchlist) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-gray-800 hover:bg-red-900/50 text-gray-400 hover:text-red-400 text-xs font-medium px-4 py-2 rounded-lg border border-gray-700 hover:border-red-600/50 transition">
                                    Remove
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-medium px-4 py-2 rounded-lg border border-gray-700 transition">
                            Login to add to watchlist
                        </a>
                    @endauth
                </div>

                {{-- Rating Section --}}
                @auth
                    <div class="bg-gray-800/50 rounded-xl p-4 mb-6 border border-gray-700/50" x-data="ratingSystem()">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-white">Your Rating</h3>
                            <span class="text-xs text-gray-400" x-text="currentRating > 0 ? 'Rated ' + currentRating + '/5' : 'Not rated yet'"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex gap-1">
                                <template x-for="star in 5" :key="star">
                                    <button @click="setRating(star)" 
                                            class="text-2xl transition-all hover:scale-110"
                                            :class="star <= currentRating ? 'text-yellow-400' : 'text-gray-600 hover:text-yellow-400/50'">
                                        ★
                                    </button>
                                </template>
                            </div>
                            <button x-show="currentRating > 0" @click="removeRating()" 
                                    class="text-xs text-gray-500 hover:text-red-400 ml-2 transition">
                                Remove
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2">Rating ini membantu kami memberikan rekomendasi yang lebih akurat untukmu</p>
                    </div>

                    <script>
                        const ratingData = {
                            tmdbId: {{ $item['id'] }},
                            title: {!! json_encode($title) !!},
                            posterPath: {!! json_encode($item['poster_path'] ?? '') !!},
                            mediaType: {!! json_encode($type) !!},
                            genreIds: {!! json_encode(implode(',', array_column($item['genres'] ?? [], 'id'))) !!},
                            currentRating: {{ $userRating ? $userRating->rating : 0 }},
                            storeUrl: '{{ route("rating.store") }}',
                            deleteUrl: '{{ route("rating.delete") }}',
                            csrfToken: '{{ csrf_token() }}'
                        };

                        function ratingSystem() {
                            return {
                                currentRating: ratingData.currentRating,
                                setRating(rating) {
                                    this.currentRating = rating;
                                    fetch(ratingData.storeUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': ratingData.csrfToken
                                        },
                                        body: JSON.stringify({
                                            tmdb_id: ratingData.tmdbId,
                                            title: ratingData.title,
                                            poster_path: ratingData.posterPath,
                                            media_type: ratingData.mediaType,
                                            rating: rating,
                                            genre_ids: ratingData.genreIds
                                        })
                                    }).then(r => r.json()).then(data => {
                                        if (data.success) {
                                            this.showToast('Rating saved!');
                                        }
                                    });
                                },
                                removeRating() {
                                    this.currentRating = 0;
                                    fetch(ratingData.deleteUrl, {
                                        method: 'DELETE',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': ratingData.csrfToken
                                        },
                                        body: JSON.stringify({
                                            tmdb_id: ratingData.tmdbId
                                        })
                                    }).then(r => r.json()).then(data => {
                                        if (data.success) {
                                            this.showToast('Rating removed!');
                                        }
                                    });
                                },
                                showToast(message) {
                                    // Simple toast notification
                                    const toast = document.createElement('div');
                                    toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm';
                                    toast.textContent = message;
                                    document.body.appendChild(toast);
                                    setTimeout(() => toast.remove(), 2000);
                                }
                            }
                        }
                    </script>
                @endauth

                {{-- Storyline --}}
                @if($item['overview'])
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-white mb-2">Storyline</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ $item['overview'] }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Details --}}
                    <div>
                        <h3 class="text-sm font-bold text-white mb-3">Details</h3>
                        <div class="space-y-2 text-sm">
                            @if($director)
                                <div class="flex gap-3">
                                    <span class="text-gray-500 w-20 shrink-0">Director</span>
                                    <span class="text-gray-300">{{ $director['name'] }}</span>
                                </div>
                            @endif
                            @if($writers->count())
                                <div class="flex gap-3">
                                    <span class="text-gray-500 w-20 shrink-0">Writers</span>
                                    <span class="text-gray-300">{{ $writers->pluck('name')->implode(', ') }}</span>
                                </div>
                            @endif
                            <div class="flex gap-3">
                                <span class="text-gray-500 w-20 shrink-0">Language</span>
                                <span class="text-gray-300">{{ strtoupper($item['original_language'] ?? 'N/A') }}</span>
                            </div>
                            <div class="flex gap-3">
                                <span class="text-gray-500 w-20 shrink-0">Release</span>
                                <span class="text-gray-300">{{ $date ? date('d M Y', strtotime($date)) : 'N/A' }}</span>
                            </div>
                            @if(!empty($item['production_companies']))
                                <div class="flex gap-3">
                                    <span class="text-gray-500 w-20 shrink-0">Studio</span>
                                    <span class="text-gray-300">{{ $item['production_companies'][0]['name'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Cast --}}
                    @if($cast->count())
                        <div>
                            <h3 class="text-sm font-bold text-white mb-3">Cast</h3>
                            <div class="space-y-2">
                                @foreach($cast as $person)
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $person['profile_path'] ? $tmdbService->imageUrl($person['profile_path'], 'w185') : 'https://ui-avatars.com/api/?name=' . urlencode($person['name']) . '&background=1f2937&color=9ca3af&size=36' }}"
                                             alt="{{ $person['name'] }}" class="w-8 h-8 rounded-full object-cover">
                                        <div>
                                            <p class="text-xs text-white font-medium">{{ $person['name'] }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $person['character'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Similar --}}
        @if(!empty($item['similar']['results']))
            <section class="mt-12">
                <h3 class="text-lg font-bold text-white mb-4">Similar {{ $type === 'movie' ? 'Movies' : 'Shows' }}</h3>
                <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                    @foreach(array_slice($item['similar']['results'], 0, 8) as $similar)
                        <a href="{{ $type === 'tv' ? route('detail.tv', $similar['id']) : route('detail.movie', $similar['id']) }}" class="flex-none w-32 group">
                            <div class="overflow-hidden rounded-xl card-hover transition-all duration-300">
                                <img src="{{ $tmdbService->imageUrl($similar['poster_path'] ?? null) }}"
                                     alt="{{ $similar['title'] ?? $similar['name'] }}" class="w-full aspect-[2/3] object-cover">
                            </div>
                            <p class="mt-2 text-xs text-gray-400 truncate">{{ $similar['title'] ?? $similar['name'] }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
