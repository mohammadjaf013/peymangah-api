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

        $query = ContractModel::query()
            ->where("user_id", auth()->id())
            ->latest();

        if($request->has("status") && $request->get("status") != "all"){
            $query->where("status",$request->get("status"));
        }
        if($request->has("title") &&  !empty($request->get("title"))){
            $query->where("title","like", "%".$request->get("title")."%");
        }
        $contracts =$query->paginate(15);


        return response()->json(['contracts' => ContractListResource::collection($contracts),'page'=>$contracts->currentPage(),'total'=>$contracts->total(),'perPage'=>$contracts->perPage(), 'status' => true]);

    }

}
