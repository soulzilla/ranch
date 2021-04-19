<?php

namespace App\Services;

use App\Models\Sheep;
use App\Models\Yard;
use Illuminate\Support\Facades\Log;

class SheepService
{
    // генерация овечек для загона
    public function generate(Yard $yard, $count = 10)
    {
        for ($i = 1; $i <= $count; $i++) {
            $sheep = new Sheep();
            $sheep->yard_id = $yard->id;
            $sheep->save();
            Log::info('Sheep created');
        }
    }

    // случайную овечку увозят на убой
    public function killRandom()
    {
        $sheep = Sheep::query()
            ->inRandomOrder()
            ->limit(1)
            ->first();

        $sheep->delete();
        Log::info('Sheep deleted');

        return $sheep->id;
    }
}
