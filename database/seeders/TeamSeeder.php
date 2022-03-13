<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Team::truncate();

        for ($i=1; $i < 6; $i++) { 
            Team::create([
                'name' => 'Team '.$i,
                'uuid' => 'TM-'.uniqid(),
            ]);
        }
    }
}
