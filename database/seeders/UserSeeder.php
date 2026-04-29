<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'first_name' => 'System',
            'last_name' => 'Admin',
            'gender' => 'female',
            'birthdate' => '2000-01-01',
            'age' => 25,
            'marital_status' => 'single',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123123'),
            'contact_number' => '09123456789',
            'address' => 'Admin Address',
            'profile_image' => null,
            'role' => 'admin',
            'status' => 'active',
        ]);

        User::create([
            'username' => 'cat',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'female',
            'birthdate' => '2002-05-10',
            'age' => 23,
            'marital_status' => 'single',
            'email' => 'petlover1@gmail.com',
            'password' => Hash::make('qweqwe'),
            'contact_number' => '09111111111',
            'address' => 'Davao City',
            'profile_image' => null,
            'role' => 'pet_lover',
            'status' => 'active',
        ]);

        User::create([
            'username' => 'dog',
            'first_name' => 'John',
            'last_name' => 'Smith',
            'gender' => 'male',
            'birthdate' => '2001-08-15',
            'age' => 24,
            'marital_status' => 'single',
            'email' => 'petlover2@gmail.com',
            'password' => Hash::make('asdasd'),
            'contact_number' => '09222222222',
            'address' => 'Davao City',
            'profile_image' => null,
            'role' => 'pet_lover',
            'status' => 'active',
        ]);
    }
}