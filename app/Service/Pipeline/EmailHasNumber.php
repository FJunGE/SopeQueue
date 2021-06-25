<?php

namespace App\Service\Pipeline;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EmailHasNumber implements Pipe
{
    /**
     * 包含数值的邮箱
     * @param $content
     * @param Closure $next
     * @return mixed
     */
    public function handle($content, Closure $next)
    {
        $filtered = '';
        if ($content instanceof Collection) {
            $filtered = $content->filter(function ($email, $id) {
               return preg_match("/\d+/", $email);
            });
        }
        return $next($filtered);
    }
}
