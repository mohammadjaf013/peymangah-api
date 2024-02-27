<?php

namespace Modules\Contract\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Contract\App\Http\Requests\ContractCreateRequest;
use Modules\Contract\App\Resources\ContractItemResource;
use Modules\Contract\App\Resources\ContractTempResource;
use Modules\Contract\App\Resources\ContractUserResource;
use Modules\Contract\Models\contract_template\ContractCatItemTempModel;
use Modules\Contract\Models\ContractCategoryModel;
use Modules\Contract\Models\ContractCatItemModel;
use Modules\Contract\Models\ContractModel;

class ContractController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function create(ContractCreateRequest $request): JsonResponse
    {

        $category = ContractCategoryModel::query()->where("alias", $request->post("category"))->first();
        if (!$category) {
            return response()->json(['status' => false, 'message' => 'دسته بندی پیدا نشد.'], 404);
        }
        $contract = new ContractModel();
        $contract->user_id = auth()->id();
        $contract->title = $request->post('title');
        $contract->category_id = $category->id;
        $contract->save();
        return response()->json(['status' => true, 'contract_id' => $contract->code]);
    }

    /**
     * Display a listing of the resource.
     */
    public function details(Request $request, $id): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }

        $tempData = ContractCatItemTempModel::query()->where("id", $contract->category_item_id)->first();


        return response()->json(['template' => new ContractTempResource($tempData),'items'=>ContractItemResource::collection($contract->items), 'users' => ContractUserResource::collection($contract->users), 'status' => true]);

    }


}
