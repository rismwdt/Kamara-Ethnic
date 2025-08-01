<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'], 
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );

        if (!$user->hasRole('admin')) {
            $role = Role::find(1);
            $user->assignRole($role);
        }
    }
}
