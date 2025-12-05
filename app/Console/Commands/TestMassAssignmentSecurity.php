<?php

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class TestMassAssignmentSecurity extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:test-mass-assignment';

    /**
     * The console command description.
     */
    protected $description = 'Test mass assignment vulnerability protection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Mass Assignment Security...');

        try {
            // Test 1: Simulasi attack dengan data berbahaya
            $this->info('');
            $this->info('🎯 Test 1: Simulasi Mass Assignment Attack');

            $maliciousData = [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('legitimate_password'), // Password diperlukan
                // 🚨 Attempt to mass assign protected fields
                'role' => ['super_admin'],
                'status' => 'active',
                'annual_leave_quota' => 999,
                'department' => 'Hacker Department',
            ];

            $this->info('📝 Membuat user dengan data berbahaya...');
            $this->line('   Data attack: '.json_encode(array_keys($maliciousData)));

            // Attempt mass assignment
            $user = User::create($maliciousData);

            $this->line('');
            $this->info('🔍 Hasil setelah mass assignment:');

            // Check what actually got assigned
            $safeFields = ['name', 'email', 'phone_number', 'address', 'date_of_birth', 'gender'];
            $protectedFields = ['role', 'status', 'annual_leave_quota', 'department'];

            $this->info('✅ FIELD AMAN (boleh di-assign):');
            foreach ($safeFields as $field) {
                $value = $user->$field ?? 'null';
                $this->line("   {$field}: {$value}");
            }

            $this->line('');
            $this->info('🛡️  FIELD PROTECTED (harus ditolak):');
            $vulnerabilityFound = false;

            foreach ($protectedFields as $field) {
                $value = $user->$field ?? 'null';
                $wasAssigned = isset($maliciousData[$field]) && $value == $maliciousData[$field];

                if ($wasAssigned) {
                    $this->error("   ❌ {$field}: {$value} (VULNERABLE!)");
                    $vulnerabilityFound = true;
                } else {
                    $this->info("   ✅ {$field}: {$value} (PROTECTED)");
                }
            }

            // Test 2: Secure update methods
            $this->line('');
            $this->info('🔒 Test 2: Secure Update Methods');

            $admin = User::where('email', 'admin@example.com')->first();
            if (! $admin) {
                $admin = User::create([
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                    'password' => bcrypt('admin123'),
                ]);
                // Assign role if exists
                if (class_exists(Role::class)) {
                    try {
                        $admin->assignRole('super_admin');
                    } catch (Exception $e) {
                        // Role might not exist, create manually
                        $admin->role = ['super_admin'];
                        $admin->save();
                    }
                }
            }

            // Test secure role update
            try {
                $user->updateRole('manager', $admin);
                $this->info('   ✅ Secure role update: berhasil');
            } catch (Exception $e) {
                $this->error('   ❌ Secure role update error: '.$e->getMessage());
            }

            // Test secure employment info update
            try {
                $user->updateEmploymentInfo([
                    'department' => 'IT Department',
                    'annual_leave_quota' => 12,
                ], $admin);
                $this->info('   ✅ Secure employment update: berhasil');
            } catch (Exception $e) {
                $this->error('   ❌ Secure employment update error: '.$e->getMessage());
            }

            // Test 3: Unauthorized access
            $this->line('');
            $this->info('🚫 Test 3: Unauthorized Access Protection');

            $regularUser = User::create([
                'name' => 'Regular User',
                'email' => 'regular@example.com',
                'password' => bcrypt('regular123'),
            ]);

            try {
                $user->updateRole('admin', $regularUser); // Should fail
                $this->error('   ❌ Authorization bypass detected!');
            } catch (Exception $e) {
                $this->info('   ✅ Authorization properly blocked: '.substr($e->getMessage(), 0, 50).'...');
            }

            // Overall result
            $this->line('');
            if (! $vulnerabilityFound) {
                $this->info('🎉 HASIL: MASS ASSIGNMENT PROPERLY PROTECTED ✅');
                $this->info('   Model User sudah aman dari mass assignment attack!');
            } else {
                $this->error('❌ HASIL: MASS ASSIGNMENT VULNERABILITY DETECTED');
                $this->error('   Perlu perbaikan di Model User!');
            }

            // Cleanup
            $this->line('');
            $this->info('🧹 Membersihkan data test...');
            $user->delete();
            $regularUser->delete();
            if ($admin->email === 'admin@example.com') {
                $admin->delete();
            }

            $this->info('✅ Cleanup selesai');

        } catch (Exception $e) {
            $this->error('❌ Error during test: '.$e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
