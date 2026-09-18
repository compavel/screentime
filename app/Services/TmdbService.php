<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TmdbService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $imageUrl;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
        $this->baseUrl = config('services.tmdb.base_url');
        $this->imageUrl = config('services.tmdb.image_url');
    }

    protected function get($endpoint, $params = []): ?array
    {
        $params['api_key'] = $this->apiKey;
        $params['language'] = 'en-US';

        $response = Http::get("{$this->baseUrl}{$endpoint}", $params);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getTrendingMovies(): array
    {
        $data = $this->get('/trending/movie/week');
        return $data['results'] ?? [];
    }

    public function getTrendingTv(): array
    {
        $data = $this->get('/trending/tv/week');
        return $data['results'] ?? [];
    }

    public function getPopularMovies(): array
    {
        $data = $this->get('/movie/popular');
        return $data['results'] ?? [];
    }

    public function getPopularTv(): array
    {
        $data = $this->get('/tv/popular');
        return $data['results'] ?? [];
    }

    public function discoverMovies(array $filters = []): array
    {
        $params = [
            'sort_by' => $filters['sort'] ?? 'popularity.desc',
            'page' => $filters['page'] ?? 1,
        ];

        if (!empty($filters['genre_id'])) {
            $params['with_genres'] = $filters['genre_id'];
        }

        if (!empty($filters['vote_average_gte'])) {
            $params['vote_average.gte'] = $filters['vote_average_gte'];
        }

        if (!empty($filters['year'])) {
            $params['primary_release_year'] = $filters['year'];
        }

        return $this->get('/discover/movie', $params) ?? [];
    }

    public function discoverTv(array $filters = []): array
    {
        $params = [
            'sort_by' => $filters['sort'] ?? 'popularity.desc',
            'page' => $filters['page'] ?? 1,
        ];

        if (!empty($filters['genre_id'])) {
            $params['with_genres'] = $filters['genre_id'];
        }

        if (!empty($filters['vote_average_gte'])) {
            $params['vote_average.gte'] = $filters['vote_average_gte'];
        }

        if (!empty($filters['year'])) {
            $params['first_air_date_year'] = $filters['year'];
        }

        return $this->get('/discover/tv', $params) ?? [];
    }

    public function searchMovies(string $query, int $page = 1): array
    {
        return $this->get('/search/movie', [
            'query' => $query,
            'page' => $page,
        ]) ?? [];
    }

    public function searchTv(string $query, int $page = 1): array
    {
        return $this->get('/search/tv', [
            'query' => $query,
            'page' => $page,
        ]) ?? [];
    }

    public function getMovieDetail(int $id): ?array
    {
        return $this->get("/movie/{$id}", [
            'append_to_response' => 'credits,videos,similar',
        ]);
    }

    public function getTvDetail(int $id): ?array
    {
        return $this->get("/tv/{$id}", [
            'append_to_response' => 'credits,videos,similar',
        ]);
    }

    public function getMovieGenres(): array
    {
        return Cache::remember('movie_genres', 86400, function () {
            $data = $this->get('/genre/movie/list');
            return $data['genres'] ?? [];
        });
    }

    public function getTvGenres(): array
    {
        return Cache::remember('tv_genres', 86400, function () {
            $data = $this->get('/genre/tv/list');
            return $data['genres'] ?? [];
        });
    }

    public function imageUrl(?string $path, string $size = 'w500'): string
    {
        if (!$path) {
            return 'https://via.placeholder.com/500x750?text=No+Image';
        }

        return "https://image.tmdb.org/t/p/{$size}{$path}";
    }

    public function getGenreName(int $id, string $type = 'movie'): string
    {
        $genres = $type === 'movie' ? $this->getMovieGenres() : $this->getTvGenres();

        foreach ($genres as $genre) {
            if ($genre['id'] === $id) {
                return $genre['name'];
            }
        }

        return 'Unknown';
    }

    public function getRecommendations(int $movieId): array
    {
        $data = $this->get("/movie/{$movieId}/recommendations");
        return $data['results'] ?? [];
    }

    public function getTvRecommendations(int $tvId): array
    {
        $data = $this->get("/tv/{$tvId}/recommendations");
        return $data['results'] ?? [];
    }

    public function discoverByGenres(array $genreIds, int $page = 1): array
    {
        return $this->discoverMovies([
            'genre_id' => implode(',', $genreIds),
            'page' => $page,
        ]);
    }

    public function getSimilarMovies(int $movieId): array
    {
        $data = $this->get("/movie/{$movieId}/similar");
        return $data['results'] ?? [];
    }

    public function getSimilarTv(int $tvId): array
    {
        $data = $this->get("/tv/{$tvId}/similar");
        return $data['results'] ?? [];
    }

    public function getTopRatedMovies(): array
    {
        $data = $this->get('/movie/top_rated');
        return $data['results'] ?? [];
    }
}
