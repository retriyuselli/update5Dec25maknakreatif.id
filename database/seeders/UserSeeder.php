<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeder ini bertanggung jawab untuk membuat pengguna utama sistem dan pengguna sampel.
     *
     * Dependencies:
     * - RoleSeeder
     * - StatusSeeder
     */
    public function run(): void
    {
        // 1. Cek dependensi
        if (Role::count() === 0) {
            $this->command->error('❌ No roles found. Please run RoleSeeder first.');

            return;
        }
        if (Status::count() === 0) {
            $this->command->error('❌ No statuses found. Please run StatusSeeder first.');

            return;
        }

        $this->command->info('🔄 Creating users...');

        // 2. Ambil semua data role dan status sekaligus untuk efisiensi
        $roles = Role::all()->keyBy('name');
        $statuses = Status::all()->keyBy('status_name');

        // 3. Definisikan data pengguna utama
        // Data ini disinkronkan dengan EmployeeSeeder dan AccountManagerTargetSeeder
        $usersToCreate = [
            // Super Admins & Founders
            [
                'email' => 'superadmin@maknaonline.com',
                'name' => 'Super Admin',
                'role' => 'super_admin',
                'status' => 'Admin',
                'phone_number' => '+62-812-3456-7890',
                'address' => 'Jakarta Pusat',
                'gender' => 'male',
                'department' => 'operasional',
                'hire_date' => '2020-01-01',
            ],
            [
                'email' => 'sarah.wijaya@maknaonline.com',
                'name' => 'Sarah Wijaya',
                'role' => 'super_admin',
                'status' => 'Admin',
                'phone_number' => '+62-821-2345-6789',
                'address' => 'Jakarta Selatan',
                'gender' => 'female',
                'department' => 'operasional',
                'hire_date' => '2020-02-01',
            ],
            [
                'email' => 'michael.chen@maknaonline.com',
                'name' => 'Michael Chen',
                'role' => 'super_admin',
                'status' => 'Admin',
                'phone_number' => '+62-813-3456-7890',
                'address' => 'Jakarta Barat',
                'gender' => 'male',
                'department' => 'operasional',
                'hire_date' => '2020-03-01',
            ],

            // Admins
            [
                'email' => 'qoyyum@maknaonline.com',
                'name' => 'Qoyyum',
                'role' => 'admin',
                'status' => 'Admin',
                'phone_number' => '+62-814-4567-8901',
                'address' => 'Jakarta Timur',
                'gender' => 'male',
                'department' => 'operasional',
                'hire_date' => '2021-01-15',
            ],
            [
                'email' => 'sinta.maharani@maknaonline.com',
                'name' => 'Sinta Maharani',
                'role' => 'admin',
                'status' => 'Finance',
                'phone_number' => '+62-815-5678-9012',
                'address' => 'Jakarta Utara',
                'gender' => 'female',
                'department' => 'operasional',
                'hire_date' => '2021-02-01',
            ],
            [
                'email' => 'kartika.dewi@maknaonline.com',
                'name' => 'Kartika Dewi',
                'role' => 'admin',
                'status' => 'HRD',
                'phone_number' => '+62-816-6789-0123',
                'address' => 'Bekasi',
                'gender' => 'female',
                'department' => 'operasional',
                'hire_date' => '2021-03-01',
            ],

            // Account Managers (from AccountManagerTargetSeeder)
            [
                'email' => 'rama.dhona@maknaonline.com',
                'name' => 'Rama Dhona Utama',
                'role' => 'Account Manager',
                'status' => 'Account Manager',
                'phone_number' => '+62-817-7890-1234',
                'address' => 'Tangerang',
                'gender' => 'male',
                'department' => 'bisnis',
                'hire_date' => '2022-01-01',
            ],
            [
                'email' => 'rina.mardiana@maknaonline.com',
                'name' => 'Rina Mardiana',
                'role' => 'Account Manager',
                'status' => 'Account Manager',
                'phone_number' => '+62-818-8901-2345',
                'address' => 'Depok',
                'gender' => 'female',
                'department' => 'bisnis',
                'hire_date' => '2022-02-01',
            ],
            [
                'email' => 'adel@maknaonline.com',
                'name' => 'Adel',
                'role' => 'Account Manager',
                'status' => 'Account Manager',
                'phone_number' => '+62-819-9012-3456',
                'address' => 'Bogor',
                'gender' => 'female',
                'department' => 'bisnis',
                'hire_date' => '2022-03-01',
            ],
            [
                'email' => 'sari.ananda@maknaonline.com',
                'name' => 'Sari Ananda',
                'role' => 'Account Manager',
                'status' => 'Account Manager',
                'phone_number' => '+62-820-0123-4567',
                'address' => 'Jakarta Selatan',
                'gender' => 'female',
                'department' => 'bisnis',
                'hire_date' => '2022-04-01',
            ],
            [
                'email' => 'devi.kartika@maknaonline.com',
                'name' => 'Devi Kartika',
                'role' => 'Account Manager',
                'status' => 'Account Manager',
                'phone_number' => '+62-821-1234-5678',
                'address' => 'Jakarta Pusat',
                'gender' => 'female',
                'department' => 'bisnis',
                'hire_date' => '2022-05-01',
            ],

            // Employees / Staff (from EmployeeSeeder)
            [
                'email' => 'luna.kartika@maknaonline.com',
                'name' => 'Luna Kartika',
                'role' => 'employee',
                'status' => 'Staff',
                'phone_number' => '+62-822-2345-6789',
                'address' => 'Jakarta Barat',
                'gender' => 'female',
                'department' => 'operasional',
                'hire_date' => '2023-01-01',
            ],
            [
                'email' => 'agus.hermawan@maknaonline.com',
                'name' => 'Agus Hermawan',
                'role' => 'employee',
                'status' => 'Staff',
                'phone_number' => '+62-823-3456-7890',
                'address' => 'Jakarta Timur',
                'gender' => 'male',
                'department' => 'operasional',
                'hire_date' => '2023-02-01',
            ],
            [
                'email' => 'eko.prasetyo@maknaonline.com',
                'name' => 'Eko Prasetyo',
                'role' => 'employee',
                'status' => 'Staff',
                'phone_number' => '+62-824-4567-8901',
                'address' => 'Tangerang',
                'gender' => 'male',
                'department' => 'operasional',
                'hire_date' => '2023-03-01',
            ],
        ];

        // 4. Buat pengguna utama dan tetapkan role
        $this->command->info('👤 Creating/updating predefined users...');
        $progressBar = $this->command->getOutput()->createProgressBar(count($usersToCreate));
        $progressBar->start();

        foreach ($usersToCreate as $userData) {
            $status = $statuses->get($userData['status']);
            if (! $status) {
                $this->command->error("\nStatus '{$userData['status']}' not found for user {$userData['email']}. Skipping.");

                continue;
            }

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'), // Gunakan password yang konsisten
                    'status_id' => $status->id,
                    'email_verified_at' => now(),
                    'phone_number' => $userData['phone_number'] ?? null,
                    'address' => $userData['address'] ?? null,
                    'gender' => $userData['gender'] ?? null,
                    'department' => $userData['department'] ?? 'operasional',
                    'hire_date' => isset($userData['hire_date']) ? Carbon::parse($userData['hire_date']) : null,
                ]
            );

            $role = $roles->get($userData['role']);
            if ($role) {
                $user->syncRoles([$role]);
            } else {
                $this->command->error("\nRole '{$userData['role']}' not found for user {$userData['email']}. Role not assigned.");
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine(2);

        // 5. Buat pengguna sampel tambahan menggunakan factory
        $staffStatus = $statuses->get('Staff');
        $employeeRole = $roles->get('employee');

        if ($staffStatus && $employeeRole) {
            $this->command->info('🏭 Creating 10 sample staff users using factory...');
            User::factory(10)->create([
                'status_id' => $staffStatus->id,
            ])->each(function ($user) use ($employeeRole) {
                $user->syncRoles([$employeeRole]);
            });
        } else {
            $this->command->warn('⚠️  Skipping factory users - Staff status or employee role not found.');
        }

        // 6. Informasi hasil akhir
        $this->command->info('✅ User seeder completed successfully!');
        $this->command->info('📊 Created/updated '.count($usersToCreate).' predefined users.');
        $this->command->info('🏭 Created additional factory-generated users.');
        $this->command->info('🔍 Total users in database: '.User::count());
        $this->command->newLine();
    }
}
