<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearRouteCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:clear-all {--force : Force clear in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all route-related caches and rebuild them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Clearing all route-related caches...');

        // Clear route cache
        $this->info('   • Clearing route cache...');
        Artisan::call('route:clear');
        
        // Clear config cache
        $this->info('   • Clearing config cache...');
        Artisan::call('config:clear');
        
        // Clear view cache
        $this->info('   • Clearing view cache...');
        Artisan::call('view:clear');
        
        // Clear application cache
        $this->info('   • Clearing application cache...');
        Artisan::call('cache:clear');

        // In production, also rebuild optimized caches
        if (app()->environment('production') || $this->option('force')) {
            $this->info('   • Rebuilding optimized caches for production...');
            
            // Cache routes
            Artisan::call('route:cache');
            $this->info('     ✓ Routes cached');
            
            // Cache config
            Artisan::call('config:cache');
            $this->info('     ✓ Config cached');
            
            // Cache views
            Artisan::call('view:cache');
            $this->info('     ✓ Views cached');
        }

        $this->info('✅ All caches cleared and rebuilt successfully!');
        
        // Show current route list for verification
        if ($this->option('verbose')) {
            $this->info('📋 Current routes:');
            Artisan::call('route:list', ['--name' => 'api.patients']);
            $this->line(Artisan::output());
        }

        return 0;
    }
}
