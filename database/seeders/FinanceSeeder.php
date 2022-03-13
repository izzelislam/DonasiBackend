<?php

namespace Database\Seeders;

use App\Models\Finance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Finance::truncate();
        Finance::factory()->count(6000)->create();
    }
}
