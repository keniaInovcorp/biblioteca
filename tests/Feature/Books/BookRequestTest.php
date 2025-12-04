<?php

use App\Models\User;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Livewire\Livewire;
use App\Livewire\SubmissionsTable;

uses(RefreshDatabase::class);

test('user can create a book request', function () {
    /** @var \Tests\TestCase $this */

    Role::create(['name' => 'citizen', 'guard_name' => 'web']);
    Role::create(['name' => 'admin', 'guard_name' => 'web']);

    /** @var \App\Models\User $user */
    $user = User::factory()->create();
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

    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $user->assignRole('citizen');

    // Try to create with not valid book_id
    $response = $this->actingAs($user)
        ->post(route('submissions.store'), [
            'book_id' => 99999 // ID that does not exist
        ]);

    // Assert: Check validation error in session
    $response->assertSessionHasErrors(['book_id']);
});

test('admin can confirm a book return', function () {
    /** @var \Tests\TestCase $this */

    Role::create(['name' => 'citizen', 'guard_name' => 'web']);
    Role::create(['name' => 'admin', 'guard_name' => 'web']);

    /** @var \App\Models\User $user */
    $user = User::factory()->create();
    $user->assignRole('citizen');

    /** @var \App\Models\User $admin */
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $publisher = Publisher::factory()->create();
    $book = Book::factory()->create(['publisher_id' => $publisher->id]);

    // Create submissions using the Factory.
    $submission = Submission::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'request_date' => now()->subDays(2),
        'expected_return_date' => now()->addDays(3),
    ]);

    // Admin confirms the return.
    $response = $this->actingAs($admin)
        ->post(route('submissions.confirm-return', $submission));

    $response->assertRedirect();

    $this->assertDatabaseHas('submissions', [
        'id' => $submission->id,
        'status' => 'returned',
    ]);

    $updatedSubmission = $submission->fresh();
    expect($updatedSubmission->received_at)->not->toBeNull();
});

test('user can only see their own requests', function () {
    /** @var \Tests\TestCase $this */

    Role::create(['name' => 'citizen', 'guard_name' => 'web']);
    Role::create(['name' => 'admin', 'guard_name' => 'web']);

    /** @var \App\Models\User $userA */
    $userA = User::factory()->create();
    $userA->assignRole('citizen');

    // $userB
    $userB = User::factory()->create();
    $userB->assignRole('citizen');

    $publisher = Publisher::factory()->create();
    $book = Book::factory()->create(['publisher_id' => $publisher->id]);

    // Create submission for User A
    $submissionA = Submission::factory()->create([
        'user_id' => $userA->id,
        'book_id' => $book->id,
        'request_number' => 'REQ-MY-BOOK',
    ]);

    // Create submission for User B - Should not see
    $submissionB = Submission::factory()->create([
        'user_id' => $userB->id,
        'book_id' => $book->id,
        'request_number' => 'REQ-OTHER-BOOK',
    ]);

    // Act
    Livewire::actingAs($userA)
        ->test(SubmissionsTable::class)
        ->assertSee('REQ-MY-BOOK')     // Must see their own
        ->assertDontSee('REQ-OTHER-BOOK'); // Must NOT see others user
});
