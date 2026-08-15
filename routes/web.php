<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarToggleController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\SocialiteLoginController;

Route::get('/', function () {
    return view('welcome');
})->name('landing');

Route::get('/auth/github/redirect', [SocialiteLoginController::class, 'githubRedirect'])->name('github.redirect');
Route::get('/auth/github/callback', [SocialiteLoginController::class, 'githubCallback'])->name('github.callback');
Route::get('/auth/google/redirect', [SocialiteLoginController::class, 'googleRedirect'])->name('google.redirect');
Route::get('/auth/google/callback', [SocialiteLoginController::class, 'googleCallback'])->name('google.callback');
Route::get('/auth/facebook/redirect', [SocialiteLoginController::class, 'facebookRedirect'])->name('facebook.redirect');
Route::get('/auth/facebook/callback', [SocialiteLoginController::class, 'facebookCallback'])->name('facebook.callback');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/calendar', function () {
        return view('dashboard');
    })->name('calendar');

    Route::post('/calendar/toggle', CalendarToggleController::class)->name('calendar.toggle');

    Route::get('/data', function () {
        return view('data');
    })->name('data');

    #Route::get('/adding', function () {
    #    return view('adding');
    #})->name('adding');

});
