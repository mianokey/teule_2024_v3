<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role as ModelsRole;

class CustomServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Example: Sharing data with all views

        if (Schema::hasTable('roles')) {
            View::share('roles', ModelsRole::all());  // Share roles with all views
        }

       

        // // Example: View Composer (this is for injecting data into a specific view)
        // View::composer('admin.roles.index', function ($view) {
        //     // Pass roles to the admin.roles.index view
        //     $view->with('roles', Role::all());
        // });

        // Example: You could add custom logic or event listeners here
        // Event::listen('some.event', function () {
        //     // Your event handling code here
        // });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // // Example: Register a custom class or service
        // $this->app->singleton('SomeCustomService', function ($app) {
        //     return new \App\Services\SomeCustomService();
        // });

        // Example: Register a custom command
        // $this->commands([
        //     \App\Console\Commands\SomeCommand::class,
        // ]);
    }
}
