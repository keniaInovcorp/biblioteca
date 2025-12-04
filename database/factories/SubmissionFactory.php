<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'request_number' => Submission::generateRequestNumber(),
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'request_date' => now(),
            'expected_return_date' => now()->addDays(5),
            'status' => 'created',
            'notes' => $this->faker->sentence(),
        ];
    }

    /**
     * Indicate that the submission is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'request_date' => now()->subDays(10),
            'expected_return_date' => now()->subDays(5),
        ]);
    }

    /**
     * Indicate that the submission is returned.
     */
    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'returned',
            'received_at' => now(),
            'days_elapsed' => 4,
        ]);
    }
}

