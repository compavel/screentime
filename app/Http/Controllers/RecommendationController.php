<?php

namespace App\Http\Controllers;

use App\Models\UserRating;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get user's top rated movies (rating >= 4)
        $topRated = UserRating::where('user_id', $user->id)
            ->where('rating', '>=', 4)
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get();

        $recommendations = [];
        $seenIds = [];

        if ($topRated->isEmpty()) {
            // No ratings yet, show popular movies
            $recommendations = $this->tmdb->getPopularMovies();
        } else {
            // Get genre preferences from rated movies
            $genreCounts = [];
            
            foreach ($topRated as $rated) {
                if ($rated->genre_ids) {
                    $genres = explode(',', $rated->genre_ids);
                    foreach ($genres as $genreId) {
                        $genreId = (int) $genreId;
                        $weight = $rated->rating >= 4 ? 2 : 1;
                        $genreCounts[$genreId] = ($genreCounts[$genreId] ?? 0) + $weight;
                    }
                }
                $seenIds[] = $rated->tmdb_id;
            }

            // Sort genres by weight
            arsort($genreCounts);
            $topGenres = array_slice(array_keys($genreCounts), 0, 3);

            // Get recommendations from top rated movies
            foreach ($topRated as $rated) {
                $recs = $this->tmdb->getRecommendations($rated->tmdb_id);
                foreach ($recs as $rec) {
                    if (!in_array($rec['id'], $seenIds)) {
                        $recommendations[] = $rec;
                        $seenIds[] = $rec['id'];
                    }
                }
            }

            // If not enough recommendations, discover by top genres
            if (count($recommendations) < 10 && !empty($topGenres)) {
                $genreRecs = $this->tmdb->discoverByGenres($topGenres);
                $results = $genreRecs['results'] ?? [];
                foreach ($results as $rec) {
                    if (!in_array($rec['id'], $seenIds)) {
                        $recommendations[] = $rec;
                        $seenIds[] = $rec['id'];
                    }
                }
            }
        }

        // Limit to 20 results
        $recommendations = array_slice($recommendations, 0, 20);

        // Get genre names for display
        $genreNames = [];
        foreach ($topRated as $rated) {
            if ($rated->genre_ids) {
                $genres = explode(',', $rated->genre_ids);
                foreach ($genres as $genreId) {
                    $genreNames[] = $this->tmdb->getGenreName((int) $genreId);
                }
            }
        }
        $genreNames = array_unique(array_filter($genreNames));

        return view('pages.for-you', [
            'recommendations' => $recommendations,
            'topRated' => $topRated,
            'genreNames' => array_slice($genreNames, 0, 5),
            'hasRatings' => $topRated->isNotEmpty(),
        ]);
    }

    public function storeRating(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer',
            'title' => 'required|string',
            'poster_path' => 'nullable|string',
            'media_type' => 'required|string|in:movie,tv',
            'rating' => 'required|integer|between:1,5',
            'genre_ids' => 'nullable|string',
        ]);

        $user = Auth::user();

        UserRating::updateOrCreate(
            [
                'user_id' => $user->id,
                'tmdb_id' => $request->tmdb_id,
            ],
            [
                'title' => $request->title,
                'poster_path' => $request->poster_path,
                'media_type' => $request->media_type,
                'rating' => $request->rating,
                'genre_ids' => $request->genre_ids,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Rating saved!']);
    }

    public function deleteRating(Request $request)
    {
        $user = Auth::user();
        
        UserRating::where('user_id', $user->id)
            ->where('tmdb_id', $request->tmdb_id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Rating removed!']);
    }
}
