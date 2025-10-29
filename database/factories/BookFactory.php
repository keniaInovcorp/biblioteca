<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'isbn' => $this->faker->unique()->isbn13(),
            'name' => $this->faker->sentence(3),
            'publisher_id' => Publisher::factory(),
            'bibliography' => $this->faker->paragraph(),
            'cover_image_path' => null,
            'price' => $this->faker->randomFloat(2, 5, 100),
        ];
    }

    public function withCover(): static
    {
        return $this->state(fn (array $attributes) => [
            'cover_image_path' => 'books/covers/' . $this->faker->uuid() . '.jpg',
        ]);
    }

    public function withAuthors(int $count = 1): static
    {
        return $this->afterCreating(function (Book $book) use ($count) {
            $authors = Author::factory()->count($count)->create();
            $book->authors()->attach($authors->pluck('id'));
        });
    }
}
