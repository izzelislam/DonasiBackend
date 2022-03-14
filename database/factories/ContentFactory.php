<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $images = [
            'images/content/1.png',
            'images/content/2.png',
            'images/content/3.png',
            'images/content/4.jpg',
        ];

        return [
            'title' => $this->faker->sentence,
            'image' => $this->faker->randomElement($images),
            'content' => $this->faker->text(1000),
        ];
    }
}
