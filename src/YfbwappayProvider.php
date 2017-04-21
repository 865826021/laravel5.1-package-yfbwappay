<?php

namespace Yuxiaoyang\YfbwapPay;

use Illuminate\Support\ServiceProvider;

class YfbwapPayProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('yfbwappay',function(){
            return new YfbwapPay();
        });//app('yfbwappay')
    }
}
