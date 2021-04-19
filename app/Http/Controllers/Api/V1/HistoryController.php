<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\HistoryResource;
use Illuminate\Database\Eloquent\Builder;
use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        [$column, $order] = explode(',', $request->input('sortBy', 'id,asc'));
        $pageSize = (int) $request->input('pageSize', 10);

        $resource = History::query()
            ->when($request->filled('limit'), function (Builder $builder) use ($request) {
                $builder->limit($request->input('limit'));
            })
            ->when($request->get('from'), function (Builder $builder) use ($request) {
                $builder->where('id', '>=', $request->get('from'));
            })
            ->when($request->get('to'), function (Builder $builder) use ($request) {
                $builder->where('id', '<=', $request->get('to'));
            })
            ->orderBy($column, $order)->paginate($pageSize);

        return HistoryResource::collection($resource);
    }
}
