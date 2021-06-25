<?php

namespace App\Providers;

use App\Models\User;
use App\Service\Animal\Animal;
use App\Service\Animal\DogAnimal;
use App\Service\UserService;
use Illuminate\Support\ServiceProvider;

class AnimalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->bind('DogAnimal', DogAnimal::class); // 1.基础容器绑定

        /*$this->app->singleton('cat', CatAnimal::class);
        $this->app->when(CatAnimal::class)
            ->needs('$color')
            ->give('红色');*/ // 2.条件绑定

        // $this->app->bind(Animal::class, DogAnimal::class); // 3.绑定接口到实现

        /*$this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(User::class)->first()); // 注入子依赖
        });*/ // 4.基础绑定 注入子依赖到绑定进来的服务

        /*$dog = new DogAnimal();
        $this->app->instance('dog', $dog);*/
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
