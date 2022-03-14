<?php

namespace Database\Factories;

use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DonationFactory>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $donor_ids = Donor::all()->pluck('id')->toArray();
        $receipent_names = User::all()->pluck('name')->toArray();
        $type = [
            'zakat', 'infaq', 'shodaqoh', 'wakaf'
        ];

        $fake = \Faker\Factory::create('id_ID');

        return [
            'donor_id' => $fake->randomElement($donor_ids),
            'receipt_uid' => 'INV-'.uniqid().date('dmY'),
            'recipient' => $fake->randomElement($receipent_names),
            'type' => $fake->randomElement($type),
            'amount' => $fake->randomFloat(1000, 5000000),
            'note' => $fake->text(100),
            'created_at' => $fake->dateTimeBetween('-5 years', 'now'),
        ];
    }
}
