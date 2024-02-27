<?php

namespace Modules\Contract\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Contract\App\Resources\ContractCategoryResource;
use Modules\Contract\Models\ContractCategoryModel;

class CategoryController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {

        $categories = ContractCategoryModel::query()->where("is_activated",1)->get();

        return response()->json(ContractCategoryResource::collection($categories));
    }

}
