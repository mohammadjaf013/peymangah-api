<?php

namespace Modules\Contract\App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Contract\App\Resources\List\ContractListResource;
use Modules\Contract\Models\ContractModel;


class ContractsController extends Controller
{
    public array $data = [];


    /**
     * Display a listing of the resource.
     */
    public function list(Request $request): JsonResponse
    {

        $contracts = ContractModel::query()
            ->where("user_id", auth()->id())
            ->paginate(15);


        return response()->json(['contracts' => ContractListResource::collection($contracts),'page'=>$contracts->currentPage(),'total'=>$contracts->total(),'perPage'=>$contracts->perPage(), 'status' => true]);

    }

}
