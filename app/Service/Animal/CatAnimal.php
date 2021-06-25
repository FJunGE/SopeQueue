<?php
namespace App\Service\Animal;

use Illuminate\Support\Facades\Log;

class CatAnimal implements Animal
{
    private $color;
    public function __construct($color)
    {
        $this->color = $color;
    }

    public function color() : string
    {
        Log::info('Dog 颜色: '. $this->color);
        return '这是颜色'.$this->color;
    }

}
