<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\GoogleBooksController;
use App\Models\Book;
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

    Route::post('submissions/{submission}/confirm-return', [SubmissionController::class, 'confirmReturn'])->name('submissions.confirm-return');

    Route::resource('submissions', SubmissionController::class);

    // Google Books - protegidas por auth e com rate limit
    Route::get('google-books/search', [GoogleBooksController::class, 'search'])
        ->middleware('throttle:20,1')
        ->name('google-books.search');

    Route::get('google-books/show', [GoogleBooksController::class, 'show'])
        ->middleware('throttle:20,1')
        ->name('google-books.show');

    Route::post('google-books/import-one', [GoogleBooksController::class, 'importOne'])
        ->middleware(['throttle:5,1', 'can:create,' . Book::class])
        ->name('google-books.import-one');

    Route::post('google-books/import-by-query', [GoogleBooksController::class, 'importByQuery'])
        ->middleware(['throttle:5,1', 'can:create,' . Book::class])
        ->name('google-books.import-by-query');
});
