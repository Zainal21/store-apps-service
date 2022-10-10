<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SettingApplication;

class SettingApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       SettingApplication::create([
            'ppn_persentage' => 11,
            'insurace_price' => 5000,
            'is_active' => 1
       ]);
    }
}
