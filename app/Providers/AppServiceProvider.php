<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if(env('FORCE_HTTPS',false)) { // Default value should be false for local server
<<<<<<< HEAD
            URL::forceScheme('https');
        }
        URL::forceScheme('https');
=======
            // URL::forceScheme('https');
        }
        // URL::forceScheme('https');
>>>>>>> 16f7e913aa54ff7c93427c7edcf6737e814fc063
    }
}
