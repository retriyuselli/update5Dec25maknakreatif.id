<?php

namespace Database\Seeders;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaveManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create leave types
        $annualLeave = LeaveType::firstOrCreate([
            'name' => 'Annual Leave',
        ], [
            'keterangan' => 'Yearly vacation leave',
            'max_days_per_year' => 21,
        ]);

        $sickLeave = LeaveType::firstOrCreate([
            'name' => 'Sick Leave',
        ], [
            'keterangan' => 'Medical leave for illness',
            'max_days_per_year' => 12,
        ]);

        $emergencyLeave = LeaveType::firstOrCreate([
            'name' => 'Emergency Leave',
        ], [
            'keterangan' => 'Emergency family leave',
            'max_days_per_year' => 5,
        ]);

        // Get all users or create sample users
        $users = User::all();

        if ($users->isEmpty()) {
            // Create sample users if none exist
            $users = collect([
                User::create([
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'password' => bcrypt('password'),
                    'status' => 'active',
                ]),
                User::create([
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'password' => bcrypt('password'),
                    'status' => 'active',
                ]),
                User::create([
                    'name' => 'Bob Johnson',
                    'email' => 'bob@example.com',
                    'password' => bcrypt('password'),
                    'status' => 'active',
                ]),
            ]);
        }

        $currentYear = now()->year;

        // Create leave balances for each user
        foreach ($users as $user) {
            // Annual leave balance
            LeaveBalance::firstOrCreate([
                'user_id' => $user->id,
                'leave_type_id' => $annualLeave->id,
                'year' => $currentYear,
            ], [
                'allocated_days' => 21,
                'used_days' => rand(0, 10),
                'remaining_days' => 21 - rand(0, 10),
            ]);

            // Sick leave balance
            LeaveBalance::firstOrCreate([
                'user_id' => $user->id,
                'leave_type_id' => $sickLeave->id,
                'year' => $currentYear,
            ], [
                'allocated_days' => 12,
                'used_days' => rand(0, 5),
                'remaining_days' => 12 - rand(0, 5),
            ]);

            // Emergency leave balance
            LeaveBalance::firstOrCreate([
                'user_id' => $user->id,
                'leave_type_id' => $emergencyLeave->id,
                'year' => $currentYear,
            ], [
                'allocated_days' => 5,
                'used_days' => rand(0, 2),
                'remaining_days' => 5 - rand(0, 2),
            ]);
        }

        // Create some sample leave requests for chart data
        $this->createSampleLeaveRequests($users, [$annualLeave, $sickLeave, $emergencyLeave], $currentYear);

        $this->command->info('Leave types, balances, and sample requests seeded successfully!');
    }

    private function createSampleLeaveRequests($users, $leaveTypes, $year)
    {
        foreach ($users->take(3) as $user) { // Only for first 3 users to avoid too much data
            foreach ($leaveTypes as $leaveType) {
                // Create 1-3 requests per user per leave type
                $requestCount = rand(1, 3);

                for ($i = 0; $i < $requestCount; $i++) {
                    $startDate = now()->startOfYear()->addDays(rand(1, 300));
                    $totalDays = rand(1, 5);
                    $endDate = $startDate->copy()->addDays($totalDays - 1);

                    LeaveRequest::firstOrCreate([
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ], [
                        'total_days' => $totalDays,
                        'reason' => 'Sample leave request for '.$leaveType->name,
                        'status' => collect(['pending', 'approved', 'approved'])->random(), // More approved requests
                        'approved_by' => $user->id, // Self-approved for sample data
                    ]);
                }
            }
        }
    }
}
