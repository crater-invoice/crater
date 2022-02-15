<?php

namespace Crater\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Schema;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (\Storage::disk('local')->has('database_created') && Schema::hasTable('settings')) {
            View::share('login_page_logo', get_login_page_logo());
            View::share('login_page_heading', get_login_page_heading());
            View::share('login_page_description', get_login_page_description());
        }
    }
}
