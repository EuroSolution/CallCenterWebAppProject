<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'title' => 'Call Center',
            'email' => 'admin@admin.com',
            'phone' => '1234567890',
            'address' => 'Lorem Street, Abc road',
        ]);
    }
}
