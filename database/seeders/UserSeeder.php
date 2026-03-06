<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Teacher One',
            'email' => 'teacher@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
        ]);

        User::create([
            'name' => 'Student One',
            'email' => 'student@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student2 One',
            'email' => 'student2@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student3 One',
            'email' => 'student3@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student4 One',
            'email' => 'student4@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student5 One',
            'email' => 'student5@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student6 One',
            'email' => 'student6@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student7 One',
            'email' => 'student7@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student8 One',
            'email' => 'student8@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student9 One',
            'email' => 'student9@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student10 One',
            'email' => 'student10gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Student11 One',
            'email' => 'student11@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

    }
}
