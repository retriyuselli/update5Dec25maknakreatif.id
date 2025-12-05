<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixInternalMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:internal-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix null values in internal_messages table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing internal messages data...');

        // Fix null thread_count values
        $threadCountFixed = DB::table('internal_messages')
            ->whereNull('thread_count')
            ->update(['thread_count' => 0]);

        $this->info("Fixed {$threadCountFixed} records with null thread_count");

        // Fix null recipient_ids
        $recipientIdsFixed = DB::table('internal_messages')
            ->whereNull('recipient_ids')
            ->update(['recipient_ids' => '[]']);

        $this->info("Fixed {$recipientIdsFixed} records with null recipient_ids");

        // Fix null tags
        $tagsFixed = DB::table('internal_messages')
            ->whereNull('tags')
            ->update(['tags' => '[]']);

        $this->info("Fixed {$tagsFixed} records with null tags");

        // Fix null read_by
        $readByFixed = DB::table('internal_messages')
            ->whereNull('read_by')
            ->update(['read_by' => '[]']);

        $this->info("Fixed {$readByFixed} records with null read_by");

        $this->info('All internal messages data fixed successfully!');

        return 0;
    }
}
