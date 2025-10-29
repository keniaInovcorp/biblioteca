<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    protected $model = Author::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'photo_path' => null,
        ];
    }

    public function withPhoto(): static
    {
        return $this->state(fn (array $attributes) => [
            'photo_path' => 'authors/photos/' . $this->faker->uuid() . '.jpg',
        ]);
    }

    public function famous(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement([
                'Stephen King',
                'J.K. Rowling',
                'George R.R. Martin',
                'Agatha Christie',
                'William Shakespeare',
                'Jane Austen',
                'Mark Twain',
                'Ernest Hemingway',
                'F. Scott Fitzgerald',
                'Harper Lee',
                'John Steinbeck',
                'Ray Bradbury',
                'Isaac Asimov',
                'Arthur Conan Doyle',
                'Virginia Woolf'
            ]),
        ]);
    }
}
