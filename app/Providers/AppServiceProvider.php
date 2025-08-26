<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

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
        // Load production configuration when in production
        if (App::environment('production')) {
            $this->loadProductionConfig();
        }
    }

    /**
     * Load production-specific configuration
     */
    protected function loadProductionConfig(): void
    {
        $productionConfig = config_path('production.php');
        
        if (file_exists($productionConfig)) {
            $config = require $productionConfig;
            
            foreach ($config as $key => $value) {
                if (is_array($value)) {
                    config([$key => array_merge(config($key, []), $value)]);
                } else {
                    config([$key => $value]);
                }
            }
        }
    }
}
