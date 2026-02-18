<?php

namespace Modules\CustomDomainAddon\App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     */
    protected string $moduleNamespace = 'Modules\CustomDomainAddon\App\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        // Only load routes if module is enabled
        if (!$this->isModuleEnabled()) {
            return;
        }
        
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Check if module is enabled
     */
    private function isModuleEnabled(): bool
    {
        $statusFile = base_path('modules_statuses.json');
        
        if (!file_exists($statusFile)) {
            return false;
        }
        
        $moduleStatuses = json_decode(file_get_contents($statusFile), true);
        
        return isset($moduleStatuses['CustomDomainAddon']) && $moduleStatuses['CustomDomainAddon'] === true;
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('CustomDomainAddon', '/routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('CustomDomainAddon', '/routes/api.php'));
    }
}
