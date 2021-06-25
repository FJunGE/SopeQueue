<?php
namespace App\Service;

use App\Models\User;
use App\Service\Ioc\IoContainer;
use App\Service\Pipeline\EmailHasDownLine;
use App\Service\Pipeline\EmailHasNumber;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $user;

    /**
     * construct 声明的依赖实际是运用了容器中的自动注入
     * UserService constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function userList()
    {
        return $this->user->all();
    }

    public function pipelineUpdateUser(Collection $userList)
    {
        $pipes = [
            EmailHasNumber::class,
            EmailHasDownLine::class,
        ];

        // 这里运用了laravel 管道
        // 传入的请求符合上面的两种
        return app(Pipeline::class)->send($userList)->through($pipes)->then(function ($userList){
            User::query()->whereIn('id', $userList->keys())->update([
                'password' => Hash::make('huan0579')
            ]);
        });
    }

    public function testIoc()
    {
        $ioc = new IoContainer();
        $aa = $ioc->bind('user', 'UserService');

    }

}
