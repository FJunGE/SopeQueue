<?php
namespace App\Service\Pipeline;

use Closure;

interface Pipe
{
    public function handle($content, Closure $next);
}
