<?php
namespace App\Service\Animal;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class DogAnimal implements Animal
{
    public function color() : string
    {
        return '黄色的狗狗';
    }
}
