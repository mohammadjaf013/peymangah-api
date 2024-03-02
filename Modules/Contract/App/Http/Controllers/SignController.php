<?php

namespace Modules\Contract\App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Contract\Models\ContractModel;
use Modules\Contract\Models\ContractUserModel;
use Modules\Contract\Models\Sign\SignAuthModel;

class SignController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function check(Request $request, string $id, string $code): JsonResponse
    {

        $contract = ContractModel::query()->where("code", $id)->first();
        $user = ContractUserModel::query()
            ->where("code", $code)->first();


        if (!$contract || !$user) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }


        $auth = new SignAuthModel();
        $auth->user_id = $user->id;
        $auth->contract_id = $contract->id;
        $auth->otp = rand(112345,999999);
        $auth->expired_at =Carbon::now()->subMinutes(45);
        $auth->save();


        return response()->json(['status' => true,'code'=>$auth->code,'mobile'=>$user->mobile]);
    }


}
