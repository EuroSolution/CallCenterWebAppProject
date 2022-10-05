<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = array(
            'admin' => 'Admin',
            'call_center' => 'Call Center',
            'restaurants' => 'Restaurant',
            'customer' => 'Customer'
        );
        foreach ($roles as $key => $role){
            Role::create([
                'name' => $role,
                'slug' => $key
            ]);
        }
    }
}
