<?php

use App\Models\User;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('user can create a book request', function () {
    /** @var \Tests\TestCase $this */

    Role::create(['name' => 'citizen', 'guard_name' => 'web']);
    Role::create(['name' => 'admin', 'guard_name' => 'web']);

    $user = User::factory()->create();
     /** @var \App\Models\User $user */
    $user->assignRole('citizen');

    $publisher = Publisher::factory()->create();
    $book = Book::factory()->create(['publisher_id' => $publisher->id]);

    $response = $this->actingAs($user)
        ->post(route('submissions.store'), [
            'book_id' => $book->id
        ]);

    $response->assertRedirect(route('submissions.index'));

    $this->assertDatabaseHas('submissions', data: [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status' => 'created'
    ]);

    $submission = Submission::where('user_id', $user->id)->latest()->first();
    expect($submission->expected_return_date->format('Y-m-d'))
        ->toBe(now()->addDays(5)->format('Y-m-d'));
});

test('cannot create a request without a valid book', function () {
    /** @var \Tests\TestCase $this */

    Role::create(['name' => 'citizen', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole('citizen');

    // Try to create with not valid book_id
    /** @var \App\Models\User $user */
    $response = $this->actingAs($user)
        ->post(route('submissions.store'), [
            'book_id' => 99999 // ID que não existe
        ]);

    // Assert: Check validation error in session
    $response->assertSessionHasErrors(['book_id']);
});