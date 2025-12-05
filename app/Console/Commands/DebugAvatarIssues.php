<?php

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DebugAvatarIssues extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'debug:avatar-issues';

    /**
     * The console command description.
     */
    protected $description = 'Debug avatar upload and display issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Debugging Avatar Issues...');

        // Check storage configuration
        $this->line('');
        $this->info('📁 Storage Configuration:');
        $this->line('   Default disk: '.config('filesystems.default'));
        $this->line('   Public disk root: '.config('filesystems.disks.public.root'));
        $this->line('   Public disk URL: '.config('filesystems.disks.public.url'));

        // Check if storage link exists
        $this->line('');
        $this->info('🔗 Storage Link Check:');
        $publicStoragePath = public_path('storage');
        $linkExists = is_link($publicStoragePath);
        $linkTarget = $linkExists ? readlink($publicStoragePath) : 'N/A';

        $this->line('   Storage link exists: '.($linkExists ? '✅ Yes' : '❌ No'));
        if ($linkExists) {
            $this->line('   Link target: '.$linkTarget);
            $this->line('   Target exists: '.(file_exists($linkTarget) ? '✅ Yes' : '❌ No'));
        }

        // Check avatars directory
        $this->line('');
        $this->info('📂 Avatars Directory Check:');
        $avatarsPath = storage_path('app/public/avatars');
        $avatarsExists = is_dir($avatarsPath);
        $this->line('   Avatars directory exists: '.($avatarsExists ? '✅ Yes' : '❌ No'));
        $this->line('   Path: '.$avatarsPath);

        if ($avatarsExists) {
            $files = glob($avatarsPath.'/*');
            $this->line('   Files in avatars: '.count($files));
        }

        // Check users with avatars
        $this->line('');
        $this->info('👤 Users with Avatars:');
        $usersWithAvatars = User::whereNotNull('avatar_url')->get();

        if ($usersWithAvatars->isEmpty()) {
            $this->line('   No users with avatars found');
        } else {
            foreach ($usersWithAvatars as $user) {
                $this->line("   {$user->name}: {$user->avatar_url}");

                // Check if file exists
                $filePath = storage_path('app/public/'.$user->avatar_url);
                $fileExists = file_exists($filePath);
                $this->line('     File exists: '.($fileExists ? '✅ Yes' : '❌ No'));

                if ($fileExists) {
                    $fileSize = human_filesize(filesize($filePath));
                    $this->line("     File size: {$fileSize}");
                }

                // Test URL generation
                try {
                    $url = asset('storage/'.$user->avatar_url);
                    $this->line("     Generated URL: {$url}");
                } catch (Exception $e) {
                    $this->error('     URL generation error: '.$e->getMessage());
                }
            }
        }

        // Test file upload simulation
        $this->line('');
        $this->info('🧪 Test Avatar URL Generation:');

        $testPaths = [
            'avatars/test.jpg',
            'avatars/user-1.png',
            'test-avatar.jpg',
        ];

        foreach ($testPaths as $testPath) {
            try {
                $url = asset('storage/'.$testPath);
                $this->line("   {$testPath} → {$url}");
            } catch (Exception $e) {
                $this->error("   {$testPath} → Error: ".$e->getMessage());
            }
        }

        // Recommendations
        $this->line('');
        $this->info('💡 Recommendations:');

        if (! $linkExists) {
            $this->warn('   ⚠️  Run: php artisan storage:link');
        }

        if (! $avatarsExists) {
            $this->warn('   ⚠️  Create avatars directory or upload a test file');
        }

        $this->line('   ✅ Upload a test avatar via Filament to verify');
        $this->line('   ✅ Check browser network tab for 404 errors');
        $this->line('   ✅ Verify .env APP_URL matches your local URL');

        return Command::SUCCESS;
    }
}

/**
 * Helper function to convert bytes to human readable format
 */
function human_filesize($size, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }

    return round($size, $precision).' '.$units[$i];
}
