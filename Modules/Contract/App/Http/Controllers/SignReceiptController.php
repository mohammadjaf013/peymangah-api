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
use Modules\Contract\App\Resources\Receipt\ReceiptDetailsPubResource;
use Modules\Contract\App\Resources\Receipt\ReceiptUserResource;
use Modules\Contract\Models\ContractLogModel;
use Modules\Contract\Models\ContractModel;
use Modules\Contract\Models\ContractUserModel;
use Modules\Contract\Models\Receipt\ReceiptLogModel;
use Modules\Contract\Models\Receipt\ReceiptModel;
use Modules\Contract\Models\Receipt\ReceiptUserModel;
use Modules\Contract\Models\Sign\SignAuthModel;
use Modules\Contract\Models\Sign\SignAuthReceiptModel;

class SignReceiptController extends Controller
{
    public array $data = [];

    public function check(Request $request, string $id, string $code): JsonResponse
    {

        $contract = ReceiptModel::query()->where("code", $id)->first();
        $user = ReceiptUserModel::query()
            ->where("code", $code)->first();


        if (!$contract || !$user) {
            return response()->json(['message' => 'رسید یافت نشد.'], 404);
        }

        SignAuthReceiptModel::query()->delete(["user_id"=>$user->id]);
        $auth = new SignAuthReceiptModel();
        $auth->user_id = $user->id;
        $auth->receipt_id = $contract->id;
        $auth->otp = rand(11234,99999);
        $auth->expired_at =Carbon::now()->subMinutes(45);
        $auth->save();


        return response()->json(['status' => true,'code'=>$auth->code,'mobile'=>$user->mobile,'name'=>$user->first_name . ' ' . $user->last_name]);
    }

    public function otp(Request $request): JsonResponse
    {
        $auth = SignAuthReceiptModel::query()->where("code",$request->post("code"))->first();
        if (!$auth) {
            return response()->json(['message' => 'کد کاربر اشتباه هست.'], 404);
        }
        $auth->otp = rand(11234,99999);
        $auth->save();
        event(new OtpEvent($auth->user->mobile,$auth->otp));
        return response()->json(['status' => true]);
    }
    public function verify(Request $request): JsonResponse
    {
        $auth = SignAuthReceiptModel::query()
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
        $auth = SignAuthReceiptModel::query()
            ->where("reference",$request->post("token"))
            ->first();
        if (!$auth) {
            return response()->json(['message' => 'فرایند ورود شما موفقیت آمیز نبوده است..'], 404);
        }

        $log = new ReceiptLogModel();
        $log->receipt_id = $auth->receipt->id;
        $log->ip = $request->getClientIp();
        $log->user_agent = $request->userAgent();
        $log->event = "SIGN";
        $log->message = "مشاهده رسید" . $auth->id;
        $log->save();


        return response()->json([
            'receipt' => new ReceiptDetailsPubResource($auth->receipt),
            'users' => ReceiptUserResource::collection($auth->receipt->users),
            'user' => new ReceiptUserResource($auth->user),
            'status' => true]);

    }
    public function face(Request $request): JsonResponse
    {
        $auth = SignAuthReceiptModel::query()
            ->where("reference",$request->post("token"))
            ->first();
        if (!$auth) {
            return response()->json(['message' => 'فرایند ورود شما موفقیت آمیز نبوده است..'], 404);
        }

        $user = ReceiptUserModel::query()->where("id",$auth->user_id)->first();
        $user->step=1;
        $user->update();

        return response()->json([
            'status' => true]);

    }

    public function signature(Request $request): JsonResponse
    {
        $auth = SignAuthReceiptModel::query()
            ->where("reference",$request->post("token"))
            ->first();
        if (!$auth) {
            return response()->json(['message' => 'فرایند ورود شما موفقیت آمیز نبوده است..'], 404);
        }

        $user = ReceiptUserModel::query()->where("id",$auth->user_id)->first();
        $user->is_signed=1;
        $user->sign_at=Carbon::now();
        $user->update();

        $csing =ReceiptUserModel::query()->where("contract_id",$user->contract_id)->where("is_signed",0)->count();

        if($csing ==0){
            $contract = ReceiptModel::query()->where("id",$user->contract_id)->first();
            $contract->status="completed";
            $contract->update();
        }

        $log = new ReceiptLogModel();
        $log->contract_id = $contract->id;
        $log->ip = $request->getClientIp();
        $log->user_agent = $request->userAgent();
        $log->event = "SIGN";
        $log->message = "امضای رسید " . $user->first_name . " " . $user->last_name;
        $log->save();

        return response()->json([
            'status' => true]);

    }

}
