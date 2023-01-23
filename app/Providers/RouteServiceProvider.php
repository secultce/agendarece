<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/programacao';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::model('sector', \App\Models\Sector::class);
        Route::model('user', \App\Models\User::class);
        Route::model('space', \App\Models\Space::class);
        Route::model('category', \App\Models\Category::class);
        Route::model('role', \App\Models\Role::class);
        Route::model('programmation', \App\Models\Programmation::class);
        Route::model('link', \App\Models\ProgrammationLink::class);
        Route::model('note', \App\Models\ProgrammationNote::class);
        Route::model('comment', \App\Models\ProgrammationComment::class);
        Route::model('configuration', \App\Models\Configuration::class);
        Route::model('schedule', \App\Models\Schedule::class);
        Route::model('customHoliday', \App\Models\CustomHoliday::class);

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
