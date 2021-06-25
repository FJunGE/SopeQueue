<?php
namespace App\Service\Pipeline;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EmailHasDownLine implements Pipe
{
    /**
     * 包含下划线邮箱
     * @param $content
     * @param Closure $next
     * @return mixed
     */
    public function handle($content, Closure $next)
    {
        $filtered = '';
        if ($content instanceof Collection) {
            $filtered = $content->filter(function ($email, $id) {
                return Str::contains($email, '_');
            });
        }

        return $next($filtered);
    }

}
