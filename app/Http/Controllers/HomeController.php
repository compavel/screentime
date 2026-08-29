<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(protected TmdbService $tmdb) {}

    public function index()
    {
        $trendingMovies = $this->tmdb->getTrendingMovies();
        $trendingTv = $this->tmdb->getTrendingTv();
        $popularMovies = $this->tmdb->getPopularMovies();
        $popularTv = $this->tmdb->getPopularTv();
        $genres = $this->tmdb->getMovieGenres();

        return view('pages.home', compact(
            'trendingMovies',
            'trendingTv',
            'popularMovies',
            'popularTv',
            'genres'
        ));
    }
}
