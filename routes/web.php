<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/books-view', function(){
        return Inertia::render('Books/Books');
    })->name('books-view');
    Route::get('/add-books', function(){
        return Inertia::render('Books/AddBook');
    })->name('add-books');

    Route::post('/books-update/{book}', [BookController::class, 'update']);
});

//Books API
Route::apiResource('books', BookController::class)->middleware('auth');

require __DIR__.'/auth.php';