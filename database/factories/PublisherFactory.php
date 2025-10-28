<?php

namespace Database\Factories;

use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Publisher>
 */
class PublisherFactory extends Factory
{
    protected $model = Publisher::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Publishing',
            'logo_path' => null,
        ];
    }

     public function withLogo(): static
    {
        return $this->state(fn (array $attributes) => [
            'logo_path' => 'publishers/logos/' . $this->faker->uuid() . '.jpg',
        ]);
    }

     public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement([
                'Penguin Random House',
                'HarperCollins Publishers',
                'Simon & Schuster',
                'Hachette Book Group',
                'Macmillan Publishers',
                'Scholastic Corporation',
                'Pearson Education',
                'Cengage Learning',
                'Oxford University Press',
                'Cambridge University Press'
            ]),
        ]);
    }
}
