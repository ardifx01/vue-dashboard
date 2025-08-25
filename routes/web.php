<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialController;

// Social authentication routes
Route::prefix('auth')->group(function () {
    Route::get('{provider}', [SocialController::class, 'redirect'])->name('social.redirect');
    Route::get('{provider}/callback', [SocialController::class, 'callback'])->name('social.callback');
});

Route::get('{any?}', function() {
    return view('application');
})->where('any', '.*');
