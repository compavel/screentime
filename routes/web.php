<?php

use App\Http\Controllers\BrowseController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Setup route - jalankan sekali setelah deploy, lalu hapus
Route::get('/setup/{key}', function ($key) {
    if ($key !== 'screentime-setup-2026') {
        abort(403);
    }

    try {
        // Generate APP_KEY jika belum ada
        if (!env('APP_KEY')) {
            Artisan::call('key:generate', ['--force' => true]);
        }

        // Run migration
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();

        return response("Setup Berhasil!\n\n" . $output, 200)
            ->header('Content-Type', 'text/plain');
    } catch (\Exception $e) {
        return response("Error: " . $e->getMessage(), 500)
            ->header('Content-Type', 'text/plain');
    }
});

Route::get('/browse', [BrowseController::class, 'index'])->name('browse');

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/movie/{id}', [DetailController::class, 'movie'])->name('detail.movie');
Route::get('/tv/{id}', [DetailController::class, 'tv'])->name('detail.tv');

Route::middleware('auth')->group(function () {
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist');
    Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
    Route::patch('/watchlist/{watchlist}', [WatchlistController::class, 'update'])->name('watchlist.update');
    Route::delete('/watchlist/{watchlist}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

    // Recommendations
    Route::get('/for-you', [RecommendationController::class, 'index'])->name('for-you');
    Route::post('/rating', [RecommendationController::class, 'storeRating'])->name('rating.store');
    Route::delete('/rating', [RecommendationController::class, 'deleteRating'])->name('rating.delete');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
