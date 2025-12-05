<?php

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixAvatarPaths extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'avatar:fix-paths';

    /**
     * The console command description.
     */
    protected $description = 'Fix avatar paths to use proper avatars/ directory structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing Avatar Paths...');

        $users = User::whereNotNull('avatar_url')->get();
        $fixed = 0;
        $moved = 0;
        $errors = 0;

        foreach ($users as $user) {
            $this->line("Processing: {$user->name}");
            $currentPath = $user->avatar_url;

            // Jika path sudah benar (dimulai dengan avatars/), skip
            if (str_starts_with($currentPath, 'avatars/')) {
                $this->line("  ✅ Path already correct: {$currentPath}");

                continue;
            }

            // Path yang salah - tidak dimulai dengan avatars/
            $correctPath = 'avatars/'.basename($currentPath);

            try {
                $oldFullPath = storage_path('app/public/'.$currentPath);
                $newFullPath = storage_path('app/public/'.$correctPath);

                // Cek apakah file lama ada
                if (file_exists($oldFullPath)) {
                    // Pindahkan file ke directory yang benar
                    if (File::move($oldFullPath, $newFullPath)) {
                        $this->line("  📁 Moved file: {$currentPath} → {$correctPath}");
                        $moved++;
                    } else {
                        $this->error("  ❌ Failed to move file: {$currentPath}");
                        $errors++;

                        continue;
                    }
                }

                // Update database
                $user->update(['avatar_url' => $correctPath]);
                $this->line("  ✅ Updated database: {$correctPath}");
                $fixed++;

            } catch (Exception $e) {
                $this->error("  ❌ Error processing {$user->name}: ".$e->getMessage());
                $errors++;
            }
        }

        $this->line('');
        $this->info('📊 Summary:');
        $this->line("  Users processed: {$users->count()}");
        $this->line("  Paths fixed: {$fixed}");
        $this->line("  Files moved: {$moved}");
        $this->line("  Errors: {$errors}");

        if ($errors === 0) {
            $this->info('✅ All avatar paths have been fixed!');
        } else {
            $this->warn("⚠️  {$errors} errors encountered. Check logs for details.");
        }

        return Command::SUCCESS;
    }
}
