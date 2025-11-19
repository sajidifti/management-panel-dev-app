<?php

namespace Sajidifti\ManagementPanel\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'management-panel:install';
    
    protected $description = 'Install the Management Panel package';

    public function handle()
    {
        $this->info('Installing Laravel Management Panel...');

        // Publish configuration
        $this->call('vendor:publish', [
            '--tag' => 'management-panel-config',
            '--force' => true,
        ]);

        // Publish assets
        $this->call('vendor:publish', [
            '--tag' => 'management-panel-assets',
            '--force' => true,
        ]);

        // Create session directory
        $sessionPath = storage_path('framework/management_sessions');
        if (!File::exists($sessionPath)) {
            File::makeDirectory($sessionPath, 0755, true);
            File::put($sessionPath . '/.gitignore', "*\n!.gitignore\n");
            $this->info('Created management sessions directory');
        }

        // Check if .env has management settings
        $envFile = base_path('.env');
        $envContent = File::get($envFile);
        
        if (!str_contains($envContent, 'MANAGEMENT_ROUTE_PREFIX')) {
            $this->warn('Adding management panel configuration to .env...');
            
            $managementConfig = "\n# Management Panel Configuration\n";
            $managementConfig .= "MANAGEMENT_ROUTE_PREFIX=management/secret\n";
            $managementConfig .= "MANAGEMENT_USERNAME=admin\n";
            $managementConfig .= "MANAGEMENT_PASSWORD=password\n";
            $managementConfig .= "MANAGEMENT_SESSION_LIFETIME=120\n";
            
            File::append($envFile, $managementConfig);
            $this->info('Added management panel configuration to .env');
        }

        $this->newLine();
        $this->info('✓ Management Panel installed successfully!');
        $this->newLine();
        
        $this->warn('⚠ IMPORTANT SECURITY STEPS:');
        $this->line('1. Change MANAGEMENT_ROUTE_PREFIX in .env to something unique');
        $this->line('2. Update MANAGEMENT_USERNAME and MANAGEMENT_PASSWORD');
        $this->line('3. Clear your config cache: php artisan config:clear');
        $this->newLine();
        
        $prefix = config('management.route_prefix', 'management/secret');
        $this->info("Access your management panel at: " . url($prefix));
        
        return self::SUCCESS;
    }
}
