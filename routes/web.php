<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialController;

// Social authentication routes
Route::prefix('auth')->group(function () {
    Route::get('{provider}', [SocialController::class, 'redirect'])->name('social.redirect');
    Route::get('{provider}/callback', [SocialController::class, 'callback'])->name('social.callback');
});

// Fallback for root
Route::get('/', function() {
    return view('application');
})->name('home');

// Catch-all route for Vue Router (SPA) - exclude build assets, api routes, and auth routes
Route::get('/{any}', function() {
    return view('application');
})->where('any', '^(?!api|build|auth).*$')->name('spa');
