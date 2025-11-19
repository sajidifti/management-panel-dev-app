<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Skip all view composers for management panel routes
        $managementPrefix = env('MANAGEMENT_ROUTE_PREFIX', 'management/secret');
        if (request()->is($managementPrefix) || request()->is($managementPrefix . '/*')) {
            return;
        }
        
        // Share global category list with all views
        View::share('globalCategoryList', function () {
            return Category::active()
                ->roots()
                ->with('descendants')
                ->orderBy('order')
                ->get();
        });

        // Share cart count with all views
        View::composer('*', \App\Http\ViewComposers\CartComposer::class);
        
        // Share CMS pages with footer and front pages
        View::composer(['partials.footer', 'front.pages.*'], \App\Http\ViewComposers\CmsPagesComposer::class);
        
        // Share settings with all views (navbar, footer, layouts, etc.)
        View::composer('*', \App\Http\ViewComposers\SettingsComposer::class);
        
        // Share random product image with navbar
        View::composer('partials.navbar', \App\Http\ViewComposers\RandomProductImageComposer::class);
        
        // Share navigation menu items with navbar
        View::composer('partials.navbar', \App\Http\ViewComposers\NavMenuComposer::class);
    }
}
