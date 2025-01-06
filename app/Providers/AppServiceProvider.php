<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // dd();
        $host = request()->getHost();
        if($host === "hris.wsystem.online")
        {
            URL::forceScheme('https');
        }
        //  if(env('FORCE_HTTPS',false)) { // Default value should be false for local server\
        //      URL::forceScheme('https');
        //  }
        //  \URL::forceScheme('https');
        // URL::forceScheme('https');

       
    }
}
