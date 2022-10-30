<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        // faker
        $faker = \Faker\Factory::create('id_ID');

        User::create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'password' => bcrypt('secret'),
            'phone_number' => '+380991234567',
            'role' => 'admin',
            'status' => 'active',
        ]);


        $team_ids = Team::all()->pluck('id')->toArray();
        $image= [
            'images/profile/CjZApufiQpf6iLyRMlyUm69UuXEwp8D95iLXgIR1.webp',
            'images/profile/jtEosZMfXliBMSJxi1y0INL7fDIfd0Vgx7tbQSSu.png',
            'images/profile/YS8OFAnX33rbkpMlj49zqUBTvP0FuDkt7uQu2KEo.png',
            'images/profile/ZG32jdSPXW1NCofMfGm0qirzZuVjTK1YnN0eM2Jg.png'
        ];

        // for ($i = 0; $i < 30; $i++) {
        //     User::create([
        //         'name' => $faker->name,
        //         'email' => $faker->unique()->safeEmail,
        //         'password' => bcrypt('secret'),
        //         'phone_number' => $faker->phoneNumber,
        //         'image' =>$faker->randomElement($image),
        //         'team_id' => $faker->randomElement($team_ids),
        //         'role' => 'fundriser',
        //         'status' => $i % 2 == 0 ? 'active' : 'inactive',
        //     ]);
        // }
        
    }
}
