<?php

namespace Modules\Contract\App\Http\Controllers;



use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Auth\App\Events\OtpEvent;
use Modules\Contract\App\Resources\ContractItemResource;
use Modules\Contract\App\Resources\ContractResource;
use Modules\Contract\App\Resources\ContractUserResource;
use Modules\Contract\App\Resources\List\ContractListResource;
use Modules\Contract\App\Resources\List\ContractListUResource;
use Modules\Contract\Models\ContractModel;
use Modules\Contract\Models\ContractUserModel;
use Modules\Contract\Models\Sign\SignAuthModel;

class DashboardController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {

        $user = auth()->user();
        $contract = ContractModel::query()->where("user_id", $user->id)->count();
        $completed = ContractModel::query()
            ->where("user_id", $user->id)
            ->where("status", "completed")
            ->count();
        $signing = ContractModel::query()
            ->where("user_id", $user->id)
            ->where("status", "signing")
            ->count();


        $contracts = ContractModel::query()->where("user_id", $user->id)->latest("id")->limit(5)->get();


        return response()->json(['status' => true,
            'contracts'=>ContractListUResource::collection($contracts),
            'counter'=>[
                'contracts'=>$contract,
                'signing'=>$signing,
                'completed'=>$completed,
            ]

        ]);
    }


}
