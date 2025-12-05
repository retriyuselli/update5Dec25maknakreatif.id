<?php

namespace App\Listeners;

use App\Models\AccountManagerTarget;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class GenerateAccountManagerTargetsOnUserCreated
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        // Check if this is for User creation/update
        $user = null;

        if ($event instanceof Registered) {
            $user = $event->user;
        } elseif (property_exists($event, 'user') && $event->user instanceof User) {
            $user = $event->user;
        }

        if (! $user) {
            return;
        }

        // For now, generate targets for all users.
        // Role checking can be added later or handled at the command level
        Log::info("Auto-generating targets for user: {$user->name}");

        // Generate targets for current year (12 months)
        $this->generateTargetsForUser($user);
    }

    /**
     * Generate 12 months targets for specific user
     */
    private function generateTargetsForUser(User $user)
    {
        $currentYear = date('Y');

        try {
            for ($month = 1; $month <= 12; $month++) {
                // Skip if target already exists
                $existingTarget = AccountManagerTarget::where([
                    'user_id' => $user->id,
                    'year' => $currentYear,
                    'month' => $month,
                ])->first();

                if ($existingTarget) {
                    continue; // Skip if already exists
                }

                // Calculate achieved amount for this month (if any orders exist)
                $achievedAmount = $user->orders()
                    ->whereYear('closing_date', $currentYear)
                    ->whereMonth('closing_date', $month)
                    ->sum('grand_total') ?? 0;

                // Set monthly target amount
                $targetAmount = $this->getMonthlyTargetAmount($month);

                // Create target
                AccountManagerTarget::create([
                    'user_id' => $user->id,
                    'year' => $currentYear,
                    'month' => $month,
                    'target_amount' => $targetAmount,
                    'achieved_amount' => $achievedAmount,
                    'status' => $achievedAmount >= $targetAmount ? 'achieved' : 'pending',
                ]);

                Log::info("Created target for {$user->name} - {$currentYear}/{$month}");
            }

            Log::info("Successfully generated 12 months targets for Account Manager: {$user->name}");

        } catch (Exception $e) {
            Log::error("Failed to generate targets for Account Manager {$user->name}: ".$e->getMessage());
        }
    }

    /**
     * Get target amount for specific month
     */
    private function getMonthlyTargetAmount($month)
    {
        $baseTarget = 1000000000; // 1 billion base

        $multipliers = [
            1 => 1.0,    // January
            2 => 1.0,    // February
            3 => 1.2,    // March (peak)
            4 => 1.1,    // April
            5 => 1.3,    // May (peak)
            6 => 1.4,    // June (highest peak - wedding season)
            7 => 1.2,    // July
            8 => 1.1,    // August
            9 => 1.0,    // September
            10 => 1.3,   // October (peak)
            11 => 1.4,   // November (highest peak - wedding season)
            12 => 1.5,   // December (holiday peak)
        ];

        return $baseTarget * ($multipliers[$month] ?? 1.0);
    }
}
