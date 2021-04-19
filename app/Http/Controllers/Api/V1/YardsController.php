<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Yard;
use Illuminate\Http\Request;

class YardsController extends Controller
{
    public function index(Request $request)
    {
        $resource = Yard::query()
            ->when($request->filled('expand'), function ($builder) use ($request) {
                $builder->with(['sheep']);
            })
            ->orderBy('id');

        return response()->json([
            'data' => $resource->get()
        ]);
    }
}
