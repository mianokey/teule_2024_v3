<?php

namespace App\Providers;

use App\Models\Child;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

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
        Paginator::defaultView('custom_pagination');
         // Set timezone to Nairobi, Kenya
         Carbon::setLocale('en_KE');

         // Share birthday data with all views
         View::composer('*', function ($view) {
             $today = Carbon::now('Africa/Nairobi')->format('m-d');
 
             $children_dob = Child::with('details')
                 ->where('status', '!=', 'inactive')
                 ->whereRaw('DATE_FORMAT(dob, "%m-%d") = ?', [$today])
                 ->get();
 
             $view->with('children_dob', $children_dob);
         });
    }
}
