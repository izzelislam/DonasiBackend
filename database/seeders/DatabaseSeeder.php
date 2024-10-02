<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call(IndoRegionProvinceSeeder::class);
        $this->call(IndoRegionRegencySeeder::class);
        $this->call(IndoRegionDistrictSeeder::class);


        $this->call(TeamSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(FinanceSeeder::class);
        $this->call(DonorSeeder::class);
        $this->call(DonationSeeder::class);
        $this->call(ContentSeeder::class);
    }
}
