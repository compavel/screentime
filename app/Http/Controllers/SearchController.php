<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(protected TmdbService $tmdb) {}

    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');
        $page = $request->get('page', 1);

        $movies = [];
        $tv = [];
        $totalResults = 0;

        if ($query) {
            if ($type === 'all' || $type === 'movie') {
                $movieResults = $this->tmdb->searchMovies($query, $page);
                $movies = $movieResults['results'] ?? [];
                $totalResults += $movieResults['total_results'] ?? 0;
            }

            if ($type === 'all' || $type === 'tv') {
                $tvResults = $this->tmdb->searchTv($query, $page);
                $tv = $tvResults['results'] ?? [];
                $totalResults += $tvResults['total_results'] ?? 0;
            }
        }

        return view('pages.search', compact(
            'query',
            'type',
            'movies',
            'tv',
            'page',
            'totalResults'
        ));
    }
}
