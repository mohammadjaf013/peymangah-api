<?php

namespace Modules\Contract\App\Http\Controllers;


use App\Events\SendSmsSignEvent;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Auth\App\Events\OtpEvent;
use Modules\Contract\App\Resources\ContractItemResource;
use Modules\Contract\App\Resources\ContractResource;
use Modules\Contract\App\Resources\ContractUserResource;
use Modules\Contract\Models\ContractLogModel;
use Modules\Contract\Models\ContractModel;
use Modules\Contract\Models\ContractUserModel;
use Modules\Contract\Models\Sign\SignAuthModel;
use Modules\User\Models\UserModel;

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

        SignAuthModel::query()->delete(["user_id" => $user->id]);
        $auth = new SignAuthModel();
        $auth->user_id = $user->id;
        $auth->contract_id = $contract->id;
        $auth->otp = rand(11234, 99999);
        $auth->expired_at = Carbon::now()->subMinutes(45);
        $auth->save();


        return response()->json(['status' => true, 'code' => $auth->code, 'mobile' => $user->mobile, 'name' => $user->first_name . ' ' . $user->last_name]);
    }

    public function otp(Request $request): JsonResponse
    {
        $auth = SignAuthModel::query()->where("code", $request->post("code"))->first();
        if (!$auth) {
            return response()->json(['message' => 'کد کاربر اشتباه هست.'], 404);
        }
        $auth->otp = rand(11234, 99999);
        $auth->save();
        event(new OtpEvent($auth->user->mobile, $auth->otp));
        return response()->json(['status' => true]);
    }

    public function verify(Request $request): JsonResponse
    {
        $auth = SignAuthModel::query()
            ->where("code", $request->post("code"))
            ->where("otp", $request->post("otp"))
            ->first();
        if (!$auth) {
            return response()->json(['message' => 'کد احراز هویت اشتباه میباشد.'], 404);
        }
        $auth->reference = Str::random(60);
        $auth->save();
//       event(new OtpEvent($auth->user->mobile,$auth->otp));
        return response()->json(['status' => true, 'token' => $auth->reference]);
    }

    public function details(Request $request): JsonResponse
    {
        $auth = SignAuthModel::query()
            ->where("reference", $request->post("token"))
            ->first();
        if (!$auth) {
            return response()->json(['message' => 'فرایند ورود شما موفقیت آمیز نبوده است..'], 404);
        }

        $log = new ContractLogModel();
        $log->contract_id = $auth->contract->id;
        $log->ip = $request->getClientIp();
        $log->user_agent = $request->userAgent();
        $log->event = "SIGN";
        $log->message = "مشاهده قرارداد" . $auth->id;
        $log->save();


        return response()->json(['contract' => new ContractResource($auth->contract),
            'items' => ContractItemResource::collection($auth->contract->items),
            'users' => ContractUserResource::collection($auth->contract->users),
            'user' => new ContractUserResource($auth->user),
            'status' => true]);

    }

    public function preview(Request $request): JsonResponse
    {
        $auth = SignAuthModel::query()
            ->where("reference", $request->post("token"))
            ->first();
        if (!$auth) {
            return response()->json(['message' => 'فرایند ورود شما موفقیت آمیز نبوده است..'], 404);
        }

        $log = new ContractLogModel();
        $log->contract_id = $auth->contract->id;
        $log->ip = $request->getClientIp();
        $log->user_agent = $request->userAgent();
        $log->event = "SIGN";
        $log->message = "مشاهده پیش نمایش" . $auth->id;
        $log->save();


        return response()->json(['contract' => new ContractResource($auth->contract),
            'items' => ContractItemResource::collection($auth->contract->items),
            'users' => ContractUserResource::collection($auth->contract->users),
            'user' => new ContractUserResource($auth->user),
            'status' => true]);

    }

    public function face(Request $request): JsonResponse
    {
        $auth = SignAuthModel::query()
            ->where("reference", $request->post("token"))
            ->first();
        if (!$auth) {
            return response()->json(['message' => 'فرایند ورود شما موفقیت آمیز نبوده است..'], 404);
        }

        $user = ContractUserModel::query()->where("id", $auth->user_id)->first();
        $user->step = 1;
        $user->update();

        return response()->json([
            'status' => true]);

    }

    public function signature(Request $request): JsonResponse
    {
        $auth = SignAuthModel::query()
            ->where("reference", $request->post("token"))
            ->first();
        if (!$auth) {
            return response()->json(['message' => 'فرایند ورود شما موفقیت آمیز نبوده است..'], 404);
        }

        $user = ContractUserModel::query()->where("id", $auth->user_id)->first();
        $user->is_signed = 1;
        $user->sign_at = Carbon::now();
        $user->update();

        $csing = ContractUserModel::query()->where("contract_id", $user->contract_id)->where("is_signed", 0)->count();

        if ($csing == 0) {
            $contract = ContractModel::query()->where("id", $user->contract_id)->first();
            $contract->status = "completed";

            $contract->update();
        }

        $log = new ContractLogModel();
        $log->contract_id = $contract->id;
        $log->ip = $request->getClientIp();
        $log->user_agent = $request->userAgent();
        $log->event = "SIGN";
        $log->message = "امضای قرارداد " . $user->first_name . " " . $user->last_name;
        $log->save();

        $contract = ContractModel::query()->where("id", $user->contract_id)->first();
        $userC = UserModel::query()->where("id", $contract->user_id)->first();
        event(new SendSmsSignEvent($contract, $userC, $user));


        return response()->json([
            'status' => true]);

    }


}
