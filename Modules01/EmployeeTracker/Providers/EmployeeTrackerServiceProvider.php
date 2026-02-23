<?php

namespace Modules\EmployeeTracker\Providers;

use App\Business;
use App\BusinessLocation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class EmployeeTrackerServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'EmployeeTracker';
    protected string $moduleNameLower = 'employeetracker';

    protected $middleware = [
        'EmployeeTracker' => [
            'EmployeeTrackerLanguage' => 'ModuleLanguageMiddleware',
        ],
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerBusinessInfoComposer();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->registerMiddleware($this->app['router']);
    }

    protected function getDefaultBusinessInfo(): array
    {
        return [
            'name' => '',
            'logo_url' => null,
            'logo_exists' => false,
            'location' => 'N/A',
            'business_id' => null,
            'business' => null,
            'location_object' => null
        ];
    }

    /**
     * Register middleware.
     *
     * @param Router $router
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        foreach ($this->middleware as $module => $middlewares) {
            foreach ($middlewares as $name => $middleware) {
                $class = "Modules\\{$module}\\Http\Middleware\\{$middleware}";
                $router->aliasMiddleware($name, $class);
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands()
    {
        // Register any commands if needed.
        // $this->commands([]);
    }

    /**
     * Register command schedules.
     */
    protected function registerCommandSchedules()
    {
        // Register any command schedules if needed.
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    protected function registerBusinessInfoComposer(): void
    {
        View::composer('*', function ($view) {
            $businessId = session('user.business_id');
            
            if (!$businessId) {
                $view->with(['businessInfo' => $this->getDefaultBusinessInfo()]);
                return;
            }

            // Get business with first location in single query
            $business = Business::with(['locations' => function($query) {
                $query->orderBy('id', 'asc')->limit(1);
            }])->find($businessId);

            $user_name = auth()->check() ? auth()->user()->username : null;
            $tax_number = $business->tax_number_1;

            if (!$business) {
               $view->with(['businessInfo' => $this->getDefaultBusinessInfo()]);
                return;
            }

            $first_location = $this->getFirstLocationByPattern($business->locations);

            $businessInfo = [
                'name' => $business->name ?? '',
                'logo_url' => $this->getBusinessLogoUrl($business),
                'logo_exists' => $this->hasBusinessLogo($business),
                'location' =>   $this->formatLocationAddress($first_location),
                'business_id' => $businessId,
                'business' => $business,
                'location_object' => $business->locations->first(),
                'user_name' => $user_name,
                'tax_number' => $tax_number

            ];

            $view->with(['businessInfo' => $businessInfo]);
        });
    }

    private function getFirstLocationByPattern($locations)
    {
        if ($locations->isEmpty()) {
            return null;
        }

        // First, try to find locations starting with "00"
        $zeroLocations = $locations->filter(function($location) {
            return str_starts_with(strtolower($location->location_id ?? ''), '00');
        });

        if ($zeroLocations->isNotEmpty()) {
            // Sort by location_id and get the first one (e.g., 0010 before 0011)
            return $zeroLocations->sortBy('location_id')->first();
        }

        // If no "00" locations, try locations starting with "bl"
        $blLocations = $locations->filter(function($location) {
            return str_starts_with(strtolower($location->location_id ?? ''), 'bl');
        });

        if ($blLocations->isNotEmpty()) {
            // Sort by location_id and get the first one (e.g., bl0010 before bl0011)
            return $blLocations->sortBy('location_id')->first();
        }

        // Fallback: return first location by database ID if no pattern matches
        return $locations->sortBy('id')->first();
    }

    protected function getBusinessLogoUrl(?Business $business): ?string
    {
        if (!$business || !$business->logo) {
            return null;
        }

        $logoPath = 'uploads/business_logos/' . $business->logo;
        
        return file_exists(public_path($logoPath)) ? asset($logoPath) : null;
    }

    protected function hasBusinessLogo(?Business $business): bool
    {
        return !is_null($this->getBusinessLogoUrl($business));
    }

    protected function formatLocationAddress(?BusinessLocation $location): string
    {
        if (!$location || !$location->location_address) {
            return 'N/A';
        }

        $address = $location->location_address;
        
        return str_replace('<br>', ', ', $address);
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/'.$this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower.'.php')
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

        $componentNamespace = str_replace('/', '\\', config('modules.namespace').'\\'.$this->moduleName.'\\'.config('modules.paths.generator.component-class.path'));
        Blade::componentNamespace($componentNamespace, $this->moduleNameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Get the view paths to be published.
     *
     * @return array
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }
}

