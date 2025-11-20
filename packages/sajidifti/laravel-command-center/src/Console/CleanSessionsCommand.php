<?php

namespace Sajidifti\LaravelCommandCenter\Console;

use Illuminate\Console\Command;

class CleanSessionsCommand extends Command
{
    protected $signature = 'management:clean-sessions';

    protected $description = 'Clean expired management panel sessions';

    public function handle()
    {
        $this->info('Cleaning expired management sessions...');

        $sessionPath = config('management.session.path');
        $lifetime = config('management.session.lifetime', 120) * 60;
        $files = glob($sessionPath . '/*');
        $cleaned = 0;

        foreach ($files as $file) {
            if (is_file($file) && time() - filemtime($file) > $lifetime) {
                @unlink($file);
                $cleaned++;
            }
        }

        $this->info("✓ Cleaned {$cleaned} expired session(s)");

        return self::SUCCESS;
    }
}
