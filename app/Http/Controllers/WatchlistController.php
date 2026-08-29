<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function __construct(protected TmdbService $tmdb) {}

    public function index(Request $request)
    {
        $status = $request->get('status');

        $query = Watchlist::where('user_id', Auth::id())
            ->orderByDesc('updated_at');

        if ($status) {
            $query->where('status', $status);
        }

        $watchlists = $query->get();

        $stats = [
            'total' => Watchlist::where('user_id', Auth::id())->count(),
            'want_to_watch' => Watchlist::where('user_id', Auth::id())->where('status', 'want_to_watch')->count(),
            'watching' => Watchlist::where('user_id', Auth::id())->where('status', 'watching')->count(),
            'watched' => Watchlist::where('user_id', Auth::id())->where('status', 'watched')->count(),
        ];

        return view('pages.watchlist', compact('watchlists', 'status', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tmdb_id' => 'required|integer',
            'title' => 'required|string',
            'poster_path' => 'nullable|string',
            'media_type' => 'required|in:movie,tv',
            'status' => 'required|in:want_to_watch,watching,watched',
        ]);

        $validated['user_id'] = Auth::id();

        $existing = Watchlist::where('user_id', Auth::id())
            ->where('tmdb_id', $validated['tmdb_id'])
            ->first();

        if ($existing) {
            $existing->update(['status' => $validated['status']]);
            return back()->with('success', 'Watchlist updated!');
        }

        Watchlist::create($validated);

        return back()->with('success', 'Added to watchlist!');
    }

    public function update(Request $request, Watchlist $watchlist)
    {
        if ($watchlist->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:want_to_watch,watching,watched',
            'rating' => 'nullable|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $watchlist->update($validated);

        return back()->with('success', 'Watchlist updated!');
    }

    public function destroy(Watchlist $watchlist)
    {
        if ($watchlist->user_id !== Auth::id()) {
            abort(403);
        }

        $watchlist->delete();

        return back()->with('success', 'Removed from watchlist!');
    }
}
