<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class CustomValidationProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('currency_code', 'App\Validator\CurrencyValidator@currency');
    }
}
