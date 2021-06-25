<?php
namespace App\Service\Ioc;

class IoContainer
{
    private $binding = [];

    public function bind($abstract, $concrete)
    {
        $greet = function ($ioc) use ($concrete) {
            return $ioc . '_' . $concrete;
        };

        return $greet($abstract);
        /*$this->binding[$abstract]['concrete'] = function ($ioc) use ($concrete) {
            return $ioc;
        };

        return $this->binding;*/
    }
}
