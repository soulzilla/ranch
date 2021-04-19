<?php

namespace App\Services;

use App\Models\Yard;

class YardService
{
    private $sheepService;

    public function __construct(SheepService $sheepService)
    {
        $this->sheepService = $sheepService;
    }

    public function generate($count = 1)
    {
        // изначально загонов 4, а овечек 10, их нужно распределить в случайном порядке
        $sheepTotal = 10;
        for ($i = 1; $i <= $count; $i++) {
            $yard = new Yard();
            $yard->save();
            // случайное количество овечек для одного загона
            $sheepCount = rand(1, $sheepTotal);
            $sheepTotal -= $sheepCount;
            $this->generateSheep($yard, $sheepCount);
        }
    }

    private function generateSheep(Yard $yard, int $count = 10)
    {
        $this->sheepService->generate($yard, $count);
    }
}
