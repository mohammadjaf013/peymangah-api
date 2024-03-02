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

        SignAuthModel::query()->delete(["user_id"=>$user->id]);
        $auth = new SignAuthModel();
        $auth->user_id = $user->id;
        $auth->contract_id = $contract->id;
        $auth->otp = rand(11234,99999);
        $auth->expired_at =Carbon::now()->subMinutes(45);
        $auth->save();


        return response()->json(['status' => true,'code'=>$auth->code,'mobile'=>$user->mobile,'name'=>$user->first_name . ' ' . $user->last_name]);
    }
   public function otp(Request $request): JsonResponse
    {
        $auth = SignAuthModel::query()->where("code",$request->post("code"))->first();
        if (!$auth) {
            return response()->json(['message' => 'کد کاربر اشتباه هست.'], 404);
        }
        $auth->otp = rand(11234,99999);
        $auth->save();
//       event(new OtpEvent($auth->user->mobile,$auth->otp));
        return response()->json(['status' => true]);
    }
   public function verify(Request $request): JsonResponse
    {
        $auth = SignAuthModel::query()
            ->where("code",$request->post("code"))
            ->where("otp",$request->post("otp"))
            ->first();
        if (!$auth) {
            return response()->json(['message' => 'کد احراز هویت اشتباه میباشد.'], 404);
        }
        $auth->reference = Str::random(60);
        $auth->save();
//       event(new OtpEvent($auth->user->mobile,$auth->otp));
        return response()->json(['status' => true,'token'=>$auth->reference]);
    }

   public function details(Request $request): JsonResponse
    {
        $auth = SignAuthModel::query()
            ->where("reference",$request->post("token"))
            ->first();
        if (!$auth) {
            return response()->json(['message' => 'فرایند ورود شما موفقیت آمیز نبوده است..'], 404);
        }
        return response()->json(['contract' => new ContractResource($auth->contract),
            'items' => ContractItemResource::collection($auth->contract->items),
            'users' => ContractUserResource::collection($auth->contract->users),
            'status' => true]);

    }


}
