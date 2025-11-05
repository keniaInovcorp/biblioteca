<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('publishers', PublisherController::class);
    Route::resource('authors', AuthorController::class);
    Route::get('books/export', [BookController::class, 'export'])->name('books.export');
    Route::resource('books', BookController::class);
    Route::resource('admins', AdminController::class)->except(['edit', 'update']);
    Route::resource('submissions', SubmissionController::class);
});
