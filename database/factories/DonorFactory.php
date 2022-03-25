<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donor>
 */
class DonorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

     


    public function definition()
    {
        $team_ids = Team::all()->pluck('id')->toArray();
        $province_ids = Province::all()->pluck('id')->toArray();
        $regency_ids  = Regency::all()->pluck('id')->toArray();
        $district_ids = District::all()->pluck('id')->toArray();
        $qr= [
            'qr/DNR-623d1b854dbb5.svg',
            'qr/DNR-623d1ba4dd202.svg',
            'qr/DNR-623d1bdcaf9c4.svg',
            'qr/DNR-623d1c2e9711b.svg',
        ];

        $faker = \Faker\Factory::create('id_ID');

        return [
            'team_id' => $faker->randomElement($team_ids),
            'uuid' => 'DNR-' . uniqid(),
            'name' => $faker->name,
            'phone_number' => $faker->phoneNumber,
            'province_id' => $faker->randomElement($province_ids),
            'regency_id' => $faker->randomElement($regency_ids),
            'district_id' => $faker->randomElement($district_ids),
            'address' => $faker->address,
            'lat' => $faker->latitude,
            'lng' => $faker->longitude,
            'qr' => $faker->randomElement($qr),
        ];
    }
}
