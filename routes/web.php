<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarToggleController;

Route::get('/', function () {
    return view('welcome');
})->name('landing');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/calendar', function () {
        return view('dashboard');
    })->name('calendar');
    Route::get('/adding', function () {
        return view('adding');
    })->name('adding');

    Route::post('/calendar/toggle', CalendarToggleController::class)->name('calendar.toggle');

});
