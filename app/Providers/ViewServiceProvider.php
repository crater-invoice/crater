<?php

namespace Crater\Providers;

use Crater\View\AdminComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::share('login_page_logo', get_login_page_logo());
        View::share('login_page_heading', get_login_page_heading());
        View::share('login_page_description', get_login_page_description());
    }
}
