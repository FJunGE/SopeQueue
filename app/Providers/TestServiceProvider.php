<?php

namespace App\Providers;

use App\Service\Animal;
use App\Service\UserAnimal;
use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //$this->app->bind(Animal::class, UserAnimal::class);
        $this->app->
        $this->app->singleton();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
