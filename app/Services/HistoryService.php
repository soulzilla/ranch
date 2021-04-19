<?php

namespace App\Services;

use App\Models\History;
use App\Models\Sheep;
use App\Models\Yard;

class HistoryService
{
    // генерация первого дня
    public function generate($yards)
    {
        $firstDay = new History();
        $firstDay->total_sheep_count = 10; // по условию в первый день имеем 10 овечек
        $firstDay->killed_sheep_count = 0; // убивают только на 10й день, поэтому 0
        $firstDay->alive_sheep_count = 10; // в первый день все овечки живые

        $mostPopulatedCount = 0; // счётчик для вычисления наибольшего количества овечек в одном загоне
        $mostPopulatedYardId = null;
        $lessPopulatedCount = 10; // счётчик для наименьщего
        $lessPopulatedYardId = null;

        /** @var Yard $yard */
        foreach ($yards as $yard) {
            if (sizeof($yard->sheep) > $mostPopulatedCount) {
                $mostPopulatedCount = sizeof($yard->sheep);
                $mostPopulatedYardId = $yard->id;
            }
            if (sizeof($yard->sheep) < $lessPopulatedCount) {
                $lessPopulatedCount = sizeof($yard->sheep);
                $lessPopulatedYardId = $yard->id;
            }
        }

        if ($mostPopulatedYardId && $lessPopulatedYardId) {
            $firstDay->most_populated_yard_id = $mostPopulatedYardId;
            $firstDay->less_populated_yard_id = $lessPopulatedYardId;
            $firstDay->save();
        }

        return $firstDay;
    }

    public function makeSummary(History $history)
    {
        $this->populateInfo($history);
    }

    public function makeDay()
    {
        $history = new History();
        return $this->populateInfo($history);
    }

    private function populateInfo(History $history)
    {
        // так как день длится 10 секунд, вычисляем овечек, которые были убиты в этом промежутке времени
        $historyTime = $history->created_at ? strtotime($history->created_at) : time();
        $historyDay = $history->created_at ?? date('Y-m-d H:i:s');
        $nextDay = date('Y-m-d H:i:s', $historyTime + 10);

        $history->total_sheep_count = Sheep::withTrashed()->count();
        $history->killed_sheep_count = Sheep::withTrashed()->whereBetween('deleted_at', [$historyDay, $nextDay])->count();
        $history->alive_sheep_count = Sheep::query()->count();

        $yards = Yard::query()->with(['sheep'])->get();

        $mostPopulatedCount = 0;
        $mostPopulatedYardId = null;
        $lessPopulatedCount = $history->total_sheep_count;
        $lessPopulatedYardId = null;

        /** @var Yard $yard */
        foreach ($yards as $yard) {
            if (sizeof($yard->sheep) > $mostPopulatedCount) {
                $mostPopulatedCount = sizeof($yard->sheep);
                $mostPopulatedYardId = $yard->id;
            }
            if (sizeof($yard->sheep) < $lessPopulatedCount) {
                $lessPopulatedCount = sizeof($yard->sheep);
                $lessPopulatedYardId = $yard->id;
            }
        }

        if ($mostPopulatedYardId && $lessPopulatedYardId) {
            $history->most_populated_yard_id = $mostPopulatedYardId;
            $history->less_populated_yard_id = $lessPopulatedYardId;
            $history->save();
        }

        return $history;
    }
}
