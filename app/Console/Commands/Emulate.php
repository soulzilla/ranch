<?php

namespace App\Console\Commands;

use App\Models\History;
use App\Models\Sheep;
use App\Models\Yard;
use App\Services\HistoryService;
use App\Services\SheepService;
use App\Services\YardService;
use Illuminate\Console\Command;

class Emulate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emulate:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ranch emulation';

    private $yardService;
    private $sheepService;
    private $historyService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(YardService $yardService, SheepService $sheepService, HistoryService $historyService)
    {
        parent::__construct();
        $this->yardService = $yardService;
        $this->sheepService = $sheepService;
        $this->historyService = $historyService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $yards = $this->collectYards();

        // если загонов нет, генерируем их
        if ($yards->count() === 0) {
            $this->generateYards();
            $yards = $this->collectYards();
        }

        // получаем текущий день
        $currentDay = $this->findCurrentDay();

        if (!$currentDay) {
            $currentDay = $this->generateFirstDay($yards);
        }

        // запускаем ферму
        while (true) {

            // замеряем начало дня
            $startTime = time();

            // получаем случайный загон, где овечек больше 1
            $randomYard = $this->getRandomYardWithSheep($yards);

            // добавляем в этот загон 1 овечку
            $this->sheepService->generate($randomYard, 1);

            // каждый десятый день увозят одну случайную овечку
            if ($currentDay->id % 10 === 0) {
                $this->sheepService->killRandom();
            }

            // пересчитываем овечек и перераспределяем
            $this->populateSheep();

            // замеряем время, потраченное на автоматические процедуры
            $endTime = time();

            // если прошло недостаточно времени, ждём окончания дня
            if ($endTime - $startTime < 10) {
               sleep(10 - ($endTime - $startTime));
            }

            // записываем результаты дня
            $this->historyService->makeSummary($currentDay);

            // переходим ко следующему дню
            $currentDay = $this->historyService->makeDay();

        }
    }

    /**
     * Получаем коллекцию загонов
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function collectYards()
    {
        return Yard::query()->with(['sheep'])->get();
    }

    // генерация загонов при первом запуске
    private function generateYards()
    {
        $this->yardService->generate(4);
    }

    /**
     * Получение текущего дня
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    private function findCurrentDay()
    {
        return History::query()->orderBy('id', 'desc')->first();
    }

    /**
     * При первом запуске генерируем первый день
     * @param $yards
     * @return History
     */
    private function generateFirstDay($yards): History
    {
        return $this->historyService->generate($yards);
    }

    /**
     * Получить случайный загон, где количество овечек больше 1
     * @param $yards
     * @return Yard
     */
    private function getRandomYardWithSheep($yards) : Yard
    {
        /** @var Yard $yard */
        $yard = $yards->random();
        if (sizeof($yard->sheep) <= 1) {
            // рекурсивно ищем нужный загон
            return $this->getRandomYardWithSheep($yards);
        }

        return $yard;
    }

    /**
     * Перераспределение овечек
     */
    private function populateSheep()
    {
        // ищем загоны где количество овечек 1
        $yardsWithSingleSheep = Yard::query()->with(['sheep'])->has('sheep', '<',  2)->get();

        if (!sizeof($yardsWithSingleSheep)) {
            return;
        }

        foreach ($yardsWithSingleSheep as $yard) {
            // находим загон, где овечек больше всего
            $mostPopulated = Yard::query()->withCount(['sheep'])->orderBy('sheep_count', 'desc')->first();

            // получаем одну овечку из загона с наибольшим количеством овечек
            $sheep = Sheep::query()->where('yard_id', $mostPopulated->id)->inRandomOrder()->first();

            // переопределяем её в загон к одинокой овечке
            $sheep->yard_id = $yard->id;
            $sheep->save();
        }
    }
}
