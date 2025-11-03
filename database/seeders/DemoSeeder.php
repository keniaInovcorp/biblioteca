<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 publishers
        $publishers = Publisher::factory()->count(10)->create();
        
        // Create 20 authors
        $authors = Author::factory()->count(20)->create();
        
        // Create 50 books and relate them with publishers and authors
        for ($i = 0; $i < 50; $i++) {
            $book = Book::factory()->create([
                'publisher_id' => $publishers->random()->id,
            ]);
            
            // Assign 1 to 3 random authors
            $bookAuthors = $authors->random(rand(1, 3));
            $book->authors()->attach($bookAuthors->pluck('id'));
        }
    }
}
