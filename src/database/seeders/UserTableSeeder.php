<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users_dummy = [
            [
                'name' => 'user123',
                'email' => 'user@user.com',
                'password' => bcrypt('password'),
                'role' => 2,
            ],
            [
                'name' => 'admin123',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
                'role' => 1,
            ]
        ];
        foreach($users_dummy as $user){
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => bcrypt('password'),
                'role' => $user['role'],
            ]);
        }
    }
}
