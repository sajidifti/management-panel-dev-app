<?php

namespace Sajidifti\ManagementPanel\Console;

use Illuminate\Console\Command;

class PublishAssetsCommand extends Command
{
    protected $signature = 'management-panel:publish-assets';
    
    protected $description = 'Publish management panel assets (always overwrites existing files)';

    public function handle(): int
    {
        $this->info('Publishing management panel assets...');
        
        $this->call('vendor:publish', [
            '--tag' => 'management-panel-assets',
            '--force' => true,
        ]);
        
        $this->info('✓ Assets published successfully!');
        
        return self::SUCCESS;
    }
}

