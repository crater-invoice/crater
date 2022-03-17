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
            View::share('login_page_logo', get_app_setting('login_page_logo'));
            View::share('login_page_heading', get_app_setting('login_page_heading'));
            View::share('login_page_description', get_app_setting('login_page_description'));
            View::share('admin_page_title', get_app_setting('admin_page_title'));
            View::share('copyright_text', get_app_setting('copyright_text'));
        }
    }
}
