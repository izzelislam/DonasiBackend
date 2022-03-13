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
            'qr/DNR-622a1c3134bf9.png',
            'qr/DNR-622a114ed7594.png',
            'qr/DNR-622a14231bdb9.png',
            'qr/DNR-622b183c71130.png',
            'qr/DNR-622cd1271bdcb.png',
            'qr/DNR-622d65b344d95.png',
            'qr/DNR-6229ac35d4cfb.png',
            'qr/DNR-6229d1ad6a4a0.png',
            'qr/DNR-6229d1868a1c3.png',
            'qr/DNR-6229d16536e1f.png',
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
