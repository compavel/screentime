<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function __construct(protected TmdbService $tmdb) {}

    public function index(Request $request)
    {
        $type = $request->get('type', 'movie');
        $genreId = $request->get('genre');
        $sort = $request->get('sort', 'popularity.desc');
        $rating = $request->get('rating');
        $year = $request->get('year');
        $page = $request->get('page', 1);

        $filters = [
            'sort' => $sort,
            'page' => $page,
        ];

        if ($genreId) {
            $filters['genre_id'] = $genreId;
        }

        if ($rating) {
            $filters['vote_average_gte'] = $rating;
        }

        if ($year) {
            $filters['year'] = $year;
        }

        if ($type === 'tv') {
            $results = $this->tmdb->discoverTv($filters);
            $genres = $this->tmdb->getTvGenres();
        } else {
            $results = $this->tmdb->discoverMovies($filters);
            $genres = $this->tmdb->getMovieGenres();
        }

        $items = $results['results'] ?? [];
        $totalPages = $results['total_pages'] ?? 1;
        $totalResults = $results['total_results'] ?? 0;

        return view('pages.browse', compact(
            'items',
            'genres',
            'type',
            'genreId',
            'sort',
            'rating',
            'year',
            'page',
            'totalPages',
            'totalResults'
        ));
    }
}
