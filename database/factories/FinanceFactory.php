<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Finance>
 */
class FinanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $type = ['income', 'expense'];

        $faker = \Faker\Factory::create('id_ID');

        return [
            'title' => $faker->text(80),
            'type' => $faker->randomElement($type),
            'amount' => $faker->randomFloat(1000, 5000000),
            'note' => $faker->text(180),
            'receipt_uid' => 'TRX-'.uniqid().date('YmdHis'),
            'created_at' => $faker->dateTimeBetween('-4 years', 'now'),
        ];
    }
}
