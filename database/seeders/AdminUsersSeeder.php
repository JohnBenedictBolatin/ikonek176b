<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AdminCredential;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates 3 admin users with their credentials:
     * - System Admin (John Macalalad)
     * - Barangay Secretary/Approver (Elma Lamano Ejar)
     * - Barangay Treasurer (Mercy Sagadal Alpano)
     */
    public function run(): void
    {
        // Role IDs based on role_categories table
        // 3 = Barangay Secretary
        // 4 = Barangay Treasurer
        // 9 = System Admin

        $admins = [
            [
                'user' => [
                    'first_name' => 'John',
                    'middle_name' => null,
                    'last_name' => 'Macalalad',
                    'suffix' => null,
                    'fk_role_id' => 9, // System Admin
                    'house_number' => 'Admin',
                    'street' => 'Office',
                    'phase' => 'Phase 2',
                    'package' => 'Package 1',
                    'barangay' => 'Barangay 176B',
                    'city' => 'Caloocan',
                    'province' => 'Metro Manila',
                    'sex' => 'Male',
                    'birthdate' => '1980-01-01', // Placeholder date
                    'place_of_birth' => 'Caloocan City',
                    'religion' => 'Catholic',
                    'nationality' => 'Filipino',
                    'occupation' => 'System Administrator',
                    'civil_status' => 'Single',
                ],
                'credential' => [
                    'username' => 'SystemAdmin1',
                    'password' => '123456',
                ],
            ],
            [
                'user' => [
                    'first_name' => 'Elma',
                    'middle_name' => 'Lamano',
                    'last_name' => 'Ejar',
                    'suffix' => null,
                    'fk_role_id' => 3, // Barangay Secretary
                    'house_number' => 'Admin',
                    'street' => 'Office',
                    'phase' => 'Phase 2',
                    'package' => 'Package 1',
                    'barangay' => 'Barangay 176B',
                    'city' => 'Caloocan',
                    'province' => 'Metro Manila',
                    'sex' => 'Female',
                    'birthdate' => '1980-01-01', // Placeholder date
                    'place_of_birth' => 'Caloocan City',
                    'religion' => 'Catholic',
                    'nationality' => 'Filipino',
                    'occupation' => 'Barangay Secretary',
                    'civil_status' => 'Single',
                ],
                'credential' => [
                    'username' => 'Approver1',
                    'password' => '123456',
                ],
            ],
            [
                'user' => [
                    'first_name' => 'Mercy',
                    'middle_name' => 'Sagadal',
                    'last_name' => 'Alpano',
                    'suffix' => null,
                    'fk_role_id' => 4, // Barangay Treasurer
                    'house_number' => 'Admin',
                    'street' => 'Office',
                    'phase' => 'Phase 2',
                    'package' => 'Package 1',
                    'barangay' => 'Barangay 176B',
                    'city' => 'Caloocan',
                    'province' => 'Metro Manila',
                    'sex' => 'Female',
                    'birthdate' => '1980-01-01', // Placeholder date
                    'place_of_birth' => 'Caloocan City',
                    'religion' => 'Catholic',
                    'nationality' => 'Filipino',
                    'occupation' => 'Barangay Treasurer',
                    'civil_status' => 'Single',
                ],
                'credential' => [
                    'username' => 'PaymentHandler1',
                    'password' => '123456',
                ],
            ],
        ];

        foreach ($admins as $adminData) {
            // Check if user already exists
            $existingUser = User::where('first_name', $adminData['user']['first_name'])
                ->where('last_name', $adminData['user']['last_name'])
                ->first();

            if ($existingUser) {
                echo "User {$adminData['user']['first_name']} {$adminData['user']['last_name']} already exists. Skipping...\n";
                $user = $existingUser;
            } else {
                // Create user
                $user = User::create($adminData['user']);
                echo "Created user: {$user->first_name} {$user->last_name} (Role ID: {$user->fk_role_id})\n";
            }

            // Check if admin credential already exists
            $existingCred = AdminCredential::where('username', $adminData['credential']['username'])->first();

            if ($existingCred) {
                echo "Admin credential {$adminData['credential']['username']} already exists. Updating...\n";
                $existingCred->password = Hash::make($adminData['credential']['password']);
                $existingCred->fk_user_id = $user->user_id;
                $existingCred->save();
            } else {
                // Create admin credential
                AdminCredential::create([
                    'username' => $adminData['credential']['username'],
                    'password' => Hash::make($adminData['credential']['password']),
                    'fk_user_id' => $user->user_id,
                ]);
                echo "Created admin credential: {$adminData['credential']['username']}\n";
            }
        }

        echo "\n✅ Admin users and credentials created successfully!\n";
        echo "\nLogin credentials:\n";
        echo "1. System Admin - Username: SystemAdmin1, Password: 123456\n";
        echo "2. Barangay Secretary - Username: Approver1, Password: 123456\n";
        echo "3. Barangay Treasurer - Username: PaymentHandler1, Password: 123456\n";
    }
}
