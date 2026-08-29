<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetailController extends Controller
{
    public function __construct(protected TmdbService $tmdb) {}

    public function movie(int $id)
    {
        $item = $this->tmdb->getMovieDetail($id);

        if (!$item) {
            abort(404);
        }

        $watchlist = null;
        if (Auth::check()) {
            $watchlist = Watchlist::where('user_id', Auth::id())
                ->where('tmdb_id', $id)
                ->first();
        }

        return view('pages.detail', [
            'item' => $item,
            'type' => 'movie',
            'watchlist' => $watchlist,
        ]);
    }

    public function tv(int $id)
    {
        $item = $this->tmdb->getTvDetail($id);

        if (!$item) {
            abort(404);
        }

        $watchlist = null;
        if (Auth::check()) {
            $watchlist = Watchlist::where('user_id', Auth::id())
                ->where('tmdb_id', $id)
                ->first();
        }

        return view('pages.detail', [
            'item' => $item,
            'type' => 'tv',
            'watchlist' => $watchlist,
        ]);
    }
}
