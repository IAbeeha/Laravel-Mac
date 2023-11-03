<?php

namespace Database\Factories;
use App\Models\Post;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $faker = Faker\Factory::create();

        return [
            'title' => $this->faker->words(3, true),
            'body' => $this->faker->sentence(50),
            'user_id' => $this->faker->numberBetween(25, 50),
            'image_url' => $this->faker->imageUrl($width = 640, $height = 480),
            //
        ];
    }
}
