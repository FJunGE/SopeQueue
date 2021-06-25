<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\Animal\Animal;
use App\Service\Animal\CatAnimal;
use App\Service\Animal\DogAnimal;
use App\Service\UserService;
use Illuminate\Container\Container;
use Illuminate\Foundation\Mix;
use Illuminate\Http\Request;
use Illuminate\Log\LogServiceProvider;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        // user service 自动注入（UserService 子依赖也自动注入）
        $this->userService = $userService;
    }
    public function animal()
    {
        // dd(app()->make('DogAnimal')); // 1. 容器解析 app('xx') == app()->make('xx')

        // dd(app('cat')->color()); // 2. 解析容器实例并调用方法

        // dd(app()->makeWith(CatAnimal::class, ['color' => 'ju色'])); // makeWith 可附带参数注入

        // dd(app()->make(Animal::class)->color()); // 3. Animal接口类 与 dog实现类绑定，所以调用的color是实现类（DogAnimal）的方法
    }

    public function userList()
    {
        // UserService已经绑定到容器中，调用相关绑定名称时 会优先调用容器
        return $this->userService->userList(); // 4. 容器优先级 手动绑定 > 自动注入
    }

    public function pipelineUpdateUser()
    {
        $userLists =  $this->userList()->where('id', '<', '15')->pluck('email', 'id');
        return $this->userService->pipelineUpdateUser($userLists);
    }

    public function ioc()
    {
        $this->userService->testIoc();
    }
}
