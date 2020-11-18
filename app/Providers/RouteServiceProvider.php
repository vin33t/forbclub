<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

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
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

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

        $this->mapUserRoutes();

        $this->mapSettingsRoutes();

      $this->mapEmployeeRoutes();

      $this->mapClientRoutes();

      $this->mapSearchRoutes();

      $this->mapTransactionRoutes();

      $this->mapBookingRoutes();

      $this->mapEmailRoutes();


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

    protected function mapUserRoutes()
    {
      Route::middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/app/user.php'));
    }

    protected function mapSettingsRoutes()
    {
      Route::middleware('web')
        ->namespace($this->namespace)
        ->group(base_path('routes/app/settings.php'));
    }

  protected function mapEmployeeRoutes()
  {
    Route::middleware('web')
      ->namespace($this->namespace)
      ->prefix('employee')
      ->group(base_path('routes/app/employee.php'));
  }

  protected function mapClientRoutes()
  {
    Route::middleware('web')
      ->namespace($this->namespace)
      ->prefix('client')
      ->group(base_path('routes/app/client.php'));
  }

  protected function mapSearchRoutes()
  {
    Route::middleware('web')
      ->namespace($this->namespace)
      ->prefix('search')
      ->group(base_path('routes/app/search.php'));
  }

  protected function mapTransactionRoutes()
  {
    Route::middleware('web')
      ->namespace($this->namespace)
      ->prefix('transaction')
      ->group(base_path('routes/app/transaction.php'));
  }

  protected function mapBookingRoutes()
  {
    Route::middleware('web')
      ->namespace($this->namespace)
      ->prefix('booking')
      ->group(base_path('routes/app/booking.php'));
  }


  protected function mapEmailRoutes()
  {
    Route::middleware('web')
      ->namespace($this->namespace)
      ->prefix('email')
      ->group(base_path('routes/app/emails.php'));
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
