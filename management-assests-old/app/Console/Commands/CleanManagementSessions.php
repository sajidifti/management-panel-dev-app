<?php

namespace App\Console\Commands;

use App\Http\Middleware\ManagementSessionHandler;
use Illuminate\Console\Command;

class CleanManagementSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'management:clean-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired management panel session files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Cleaning expired management sessions...');
        
        $cleaned = ManagementSessionHandler::cleanExpiredSessions();
        
        if ($cleaned > 0) {
            $this->info("✓ Cleaned {$cleaned} expired session file(s)");
        } else {
            $this->info('✓ No expired sessions found');
        }

        return self::SUCCESS;
    }
}
