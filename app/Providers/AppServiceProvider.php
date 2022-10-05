<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

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
        view()->composer('*', function ($view){
            $setting = Setting::find(1);
            //$notifications = Notification::where('receiver_id', 1)->where('read_at', null)->orderBy('id', 'DESC')->get();;
            $notifications = array();
            $view->with(['setting' => $setting, 'notifications' => $notifications]);
        });
    }
}
