<?php

namespace Database\Factories;
use App\Models\Book;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chapter>
 */
class ChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'title' => $this->faker->sentence(4),
            'chapter_number' => $this->faker->numberBetween(1, 10),
             'book_id' => Book::factory(),
        ];
    }
}
