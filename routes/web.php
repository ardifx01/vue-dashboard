<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialController;

// Social authentication routes
Route::prefix('auth')->group(function () {
    Route::get('{provider}', [SocialController::class, 'redirect'])->name('social.redirect');
    Route::get('{provider}/callback', [SocialController::class, 'callback'])->name('social.callback');
});

// Catch-all route for Vue Router (SPA)
Route::get('/{any}', function() {
    return view('application');
})->where('any', '^(?!api).*$')->name('spa');

// Fallback for root
Route::get('/', function() {
    return view('application');
})->name('home');
