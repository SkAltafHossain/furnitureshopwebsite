<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@localshop.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);
    }
}
