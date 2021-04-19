<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sheep;
use App\Models\Yard;
use App\Services\SheepService;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SheepController extends Controller
{
    private $sheepService;

    public function __construct(SheepService $sheepService)
    {
        $this->sheepService = $sheepService;
    }

    public function store(Request $request)
    {
        /** @var Yard $yard */
        $yard = Yard::query()->where('id', $request->post('yard_id'))->first();

        if (!$yard) {
            throw new NotFound();
        }

        $this->sheepService->generate($yard, 1);
        Log::info('Sheep created');

        return response()->json([
            'status' => 'created'
        ]);
    }

    public function update(Request $request)
    {
        /** @var Sheep $sheep */
        $sheep = Sheep::query()->where('id', $request->input('id'))->first();

        if (!$sheep) {
            throw new NotFound();
        }

        /** @var Yard $yard */
        $yard = Yard::query()->where('id', $request->post('yard_id'))->first();
        if (!$yard) {
            throw new NotFound();
        }

        $sheep->yard_id = $yard->id;
        $sheep->save();
        Log::info('Sheep updated');

        return response()->json([
            'status' => 'ok'
        ]);
    }

    public function destroy(Request $request)
    {
        if ($request->filled('random')) {
            $id = $this->sheepService->killRandom();
            return response()->json([
                'status' => 'ok',
                'id' => $id
            ]);
        }

        $model = Sheep::query()->where('id', $request->input('id'))->first();
        $model->delete();
        Log::info('Sheep deleted');

        return response()->json([
            'status' => 'ok',
            'id' => $model->id
        ]);
    }
}
