<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table.
     * Requires roles to already exist (run RolesAndPermissionsSeeder first).
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $userRole       = Role::where('name', 'user')->first();

        $users = [
            // Super Admins
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@admin.com',
                'password' => Hash::make('password'),
                'role_id'  => $superAdminRole?->id,
            ],
            [
                'name'     => 'Sabir Shaikh',
                'email'    => 'sabir@admin.com',
                'password' => Hash::make('password'),
                'role_id'  => $superAdminRole?->id,
            ],

            // Normal Users
            [
                'name'     => 'Dummy user',
                'email'    => 'dummy@user.com',
                'password' => Hash::make('password'),
                'role_id'  => $userRole?->id,
            ],
            [
                'name'     => 'John Doe',
                'email'    => 'john@user.com',
                'password' => Hash::make('password'),
                'role_id'  => $userRole?->id,
            ],
            [
                'name'     => 'Jane Smith',
                'email'    => 'jane@user.com',
                'password' => Hash::make('password'),
                'role_id'  => $userRole?->id,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Users seeded successfully.');
    }
}
