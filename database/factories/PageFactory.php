<?php

namespace Database\Factories;
use App\Models\Chapter;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_number' => $this->faker->numberBetween(1, 20),
            'content' => $this->faker->paragraphs(3, true),
            'chapter_id' => Chapter::factory(),
        ];
    }
}
