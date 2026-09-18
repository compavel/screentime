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

        // Get top rated (4-5 stars)
        $topRated = $allRatings->where('rating', '>=', 4);

        $seenIds = [];

        // Mark all rated movies as seen
        foreach ($allRatings as $rated) {
            $seenIds[] = $rated->tmdb_id;
        }

        // Build genre preference map
        $genreCounts = [];
        foreach ($allRatings as $rated) {
            if ($rated->genre_ids) {
                $genres = array_filter(explode(',', $rated->genre_ids));
                foreach ($genres as $genreId) {
                    $genreId = (int) trim($genreId);
                    if ($genreId > 0) {
                        $genreCounts[$genreId] = ($genreCounts[$genreId] ?? 0) + $rated->rating;
                    }
                }
            }
        }
        arsort($genreCounts);
        $topGenreIds = array_slice(array_keys($genreCounts), 0, 4);

        // Group recommendations by genre
        $groupedRecommendations = [];
        $allRecommendations = [];

        if ($topRated->isEmpty()) {
            // No ratings, show trending grouped by genre
            $trending = $this->tmdb->getTrendingMovies();
            foreach ($trending as $movie) {
                $movieGenres = $movie['genre_ids'] ?? [];
                foreach ($movieGenres as $genreId) {
                    if (in_array($genreId, [28, 12, 16, 35, 80, 99, 18, 10751, 14, 36, 27, 9648, 10749, 878, 10770, 53, 10752, 37])) {
                        $genreName = $this->tmdb->getGenreName($genreId);
                        if (!isset($groupedRecommendations[$genreName])) {
                            $groupedRecommendations[$genreName] = [];
                        }
                        $groupedRecommendations[$genreName][] = $movie;
                        $allRecommendations[] = $movie;
                    }
                }
            }
        } else {
            // 1. Get recommendations from each top rated movie
            $rawRecs = [];
            foreach ($topRated as $rated) {
                if ($rated->media_type === 'movie') {
                    $recs = $this->tmdb->getRecommendations($rated->tmdb_id);
                } else {
                    $recs = $this->tmdb->getTvRecommendations($rated->tmdb_id);
                }
                
                foreach ($recs as $rec) {
                    $recId = $rec['id'] ?? 0;
                    if ($recId && !in_array($recId, $seenIds)) {
                        $rawRecs[] = $rec;
                        $seenIds[] = $recId;
                    }
                }
            }

            // 2. If not enough, discover by top genres
            if (count($rawRecs) < 15 && !empty($topGenreIds)) {
                $genreRecs = $this->tmdb->discoverByGenres($topGenreIds);
                $results = $genreRecs['results'] ?? [];
                foreach ($results as $rec) {
                    $recId = $rec['id'] ?? 0;
                    if ($recId && !in_array($recId, $seenIds)) {
                        $rawRecs[] = $rec;
                        $seenIds[] = $recId;
                    }
                }
            }

            // 3. Group by primary genre
            foreach ($rawRecs as $movie) {
                $movieGenres = $movie['genre_ids'] ?? [];
                $assigned = false;
                
                // Try to assign to a top genre first
                foreach ($topGenreIds as $preferredGenreId) {
                    if (in_array($preferredGenreId, $movieGenres)) {
                        $genreName = $this->tmdb->getGenreName($preferredGenreId);
                        if (!isset($groupedRecommendations[$genreName])) {
                            $groupedRecommendations[$genreName] = [];
                        }
                        $groupedRecommendations[$genreName][] = $movie;
                        $allRecommendations[] = $movie;
                        $assigned = true;
                        break;
                    }
                }

                // If not assigned to preferred genre, use first available genre
                if (!$assigned && !empty($movieGenres)) {
                    $firstGenreId = $movieGenres[0];
                    $genreName = $this->tmdb->getGenreName($firstGenreId);
                    if (!isset($groupedRecommendations[$genreName])) {
                        $groupedRecommendations[$genreName] = [];
                    }
                    $groupedRecommendations[$genreName][] = $movie;
                    $allRecommendations[] = $movie;
                }
            }
        }

        // Sort genres by count (most recommendations first)
        uasort($groupedRecommendations, fn($a, $b) => count($b) - count($a));

        // Limit each group to 6 items
        foreach ($groupedRecommendations as $genre => &$movies) {
            $movies = array_slice($movies, 0, 6);
        }

        // Get genre names for display
        $genreNames = [];
        foreach ($topGenreIds as $genreId) {
            $name = $this->tmdb->getGenreName($genreId);
            if ($name !== 'Unknown') {
                $genreNames[] = $name;
            }
        }

        return view('pages.for-you', [
            'groupedRecommendations' => $groupedRecommendations,
            'allRecommendations' => array_slice($allRecommendations, 0, 18),
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
