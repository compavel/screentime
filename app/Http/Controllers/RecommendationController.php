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
        
        // Get ALL user's rated movies
        $allRatings = UserRating::where('user_id', $user->id)
            ->orderBy('rating', 'desc')
            ->get();

        // Get top rated (4-5 stars) for recommendations
        $topRated = $allRatings->where('rating', '>=', 4);

        $recommendations = [];
        $seenIds = [];

        // Mark all rated movies as seen
        foreach ($allRatings as $rated) {
            $seenIds[] = $rated->tmdb_id;
        }

        if ($topRated->isEmpty()) {
            // No ratings yet, show trending movies
            $recommendations = $this->tmdb->getTrendingMovies();
        } else {
            // 1. Get genre preferences from rated movies
            $genreCounts = [];
            
            foreach ($allRatings as $rated) {
                if ($rated->genre_ids) {
                    $genres = array_filter(explode(',', $rated->genre_ids));
                    foreach ($genres as $genreId) {
                        $genreId = (int) trim($genreId);
                        if ($genreId > 0) {
                            // Higher weight for higher ratings
                            $weight = $rated->rating;
                            $genreCounts[$genreId] = ($genreCounts[$genreId] ?? 0) + $weight;
                        }
                    }
                }
            }

            // Sort genres by weight, get top 3
            arsort($genreCounts);
            $topGenreIds = array_slice(array_keys($genreCounts), 0, 3);

            // 2. Get recommendations from each top rated movie
            foreach ($topRated as $rated) {
                if ($rated->media_type === 'movie') {
                    $recs = $this->tmdb->getRecommendations($rated->tmdb_id);
                } else {
                    $recs = $this->tmdb->getTvRecommendations($rated->tmdb_id);
                }
                
                foreach ($recs as $rec) {
                    $recId = $rec['id'] ?? 0;
                    if ($recId && !in_array($recId, $seenIds)) {
                        $recommendations[] = $rec;
                        $seenIds[] = $recId;
                    }
                }
            }

            // 3. If not enough, discover by top genres
            if (count($recommendations) < 12 && !empty($topGenreIds)) {
                $genreRecs = $this->tmdb->discoverByGenres($topGenreIds);
                $results = $genreRecs['results'] ?? [];
                foreach ($results as $rec) {
                    $recId = $rec['id'] ?? 0;
                    if ($recId && !in_array($recId, $seenIds)) {
                        $recommendations[] = $rec;
                        $seenIds[] = $recId;
                    }
                }
            }
        }

        // Limit to 18 results
        $recommendations = array_slice($recommendations, 0, 18);

        // Get genre names for display (only top genres)
        $genreCounts = [];
        foreach ($allRatings as $rated) {
            if ($rated->genre_ids) {
                $genres = array_filter(explode(',', $rated->genre_ids));
                foreach ($genres as $genreId) {
                    $genreId = (int) trim($genreId);
                    if ($genreId > 0) {
                        $genreCounts[$genreId] = ($genreCounts[$genreId] ?? 0) + 1;
                    }
                }
            }
        }
        arsort($genreCounts);
        $topGenreIds = array_slice(array_keys($genreCounts), 0, 5);
        
        $genreNames = [];
        foreach ($topGenreIds as $genreId) {
            $name = $this->tmdb->getGenreName($genreId);
            if ($name !== 'Unknown') {
                $genreNames[] = $name;
            }
        }

        return view('pages.for-you', [
            'recommendations' => $recommendations,
            'topRated' => $topRated->values(),
            'genreNames' => $genreNames,
            'hasRatings' => $allRatings->isNotEmpty(),
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
