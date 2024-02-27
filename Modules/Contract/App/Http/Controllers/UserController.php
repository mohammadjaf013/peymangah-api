<?php

namespace Modules\Contract\App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Libs\Service\KycSystem;
use Illuminate\Http\JsonResponse;
use Modules\Contract\App\Http\Requests\UserContractRequest;
use Modules\Contract\App\Resources\ContractUserResource;
use Modules\Contract\Models\ContractModel;
use Modules\Contract\Models\ContractUserModel;
use Modules\User\Models\UserModel;
use Illuminate\Http\Request;
class UserController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function remove(Request $request, string $id): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }

        $user = ContractUserModel::query()->where("code",$request->post("user_id"))
            ->where("contract_id",$contract->id)->delete();
        return response()->json([ 'status' => true]);
    }

    /**
     * Display a listing of the resource.
     */
    public function list(Request $request, string $id): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $id)->first();
        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }
        return response()->json(['users' => ContractUserResource::collection($contract->users), 'status' => true]);
    }

    public function add(UserContractRequest $request): JsonResponse
    {

        $contract = ContractModel::query()->where("user_id", auth()->id())
            ->where("code", $request->post("contract_id"))->first();


        if (!$contract) {
            return response()->json(['message' => 'قراردادی یافت نشد.'], 404);
        }

        $user = ContractUserModel::query()->where("contract_id", $contract->id)
            ->where("ssn", $request->post('nationalCode'))->first();

        if ($user) {
            return response()->json(['message' => 'این کد ملی قبلاً ثبت شده است.'], 404);
        }

        $kycSystem = new KycSystem("itsaz");

        if (cache()->has("key_shahkar_" . $request->post('nationalCode') . "-" . $request->post("mobile"))) {
            $ssnResult = cache()->get("key_shahkar_" . $request->post('nationalCode') . "-" . $request->post("mobile"));
        } else {
            $ssnResult = $kycSystem->shahkar($request->post('nationalCode'), $request->post("mobile"), $contract->id . "-" . time());
            cache()->set("key_shahkar_" . $request->post('nationalCode') . "-" . $request->post("mobile"), $ssnResult);
        }
        if ($ssnResult == false || $ssnResult['match'] === false) {
            return response()->json(['error' => "خطا در دریافت اطلاعات", 'status' => false], 405);
        }

        if (cache()->has("key_civil_" . $request->post('nationalCode') . "-" . $request->post("birthDay"))) {
            $result = cache()->get("key_civil_" . $request->post('nationalCode') . "-" . $request->post("birthDay"));
        } else {
            $result = $kycSystem->civil($request->post('nationalCode'), $request->post('birthDay'), time());
            cache()->set("key_civil_" . $request->post('nationalCode') . "-" . $request->post("birthDay"), $result);
        }
        if ($result === 0) {
            return response()->json(['errors' => ['nationalCode' => ['اطلاعات کد ملی و تاریخ تولد شما مطابقت ندارد.']], 'status' => false], 422);

        }
        if ($result === -1) {
            return response()->json(['error' => "خطا در دریافت اطلاعات", 'status' => false], 405);
        }
        if (cache()->has("key_photo_" . $request->post('nationalCode') . "-" . $request->post("birthDay"))) {
            $resultImage = cache()->get("key_photo_" . $request->post('nationalCode') . "-" . $request->post("birthDay"));

        } else {
            $resultImage = $kycSystem->personPhoto($request->post('nationalCode'), $request->post('birthDay'));
            cache()->set("key_photo_" . $request->post('nationalCode') . "-" . $request->post("birthDay"), $resultImage);

        }

        $userExist = UserModel::query()->where("mobile", $request->post("mobile"))->first();

        $user = new ContractUserModel();
        if ($userExist) {
            $user->user_id = $userExist->id;
        }
        $user->mobile = $request->post("mobile");
        $user->ssn = $request->post("nationalCode");
        $user->birthday = $request->post("birthDay");
        $user->title = $request->post("title");
        $user->first_name = $result->data->firstName;
        $user->last_name = $result->data->lastName;
        $user->contract_id = $contract->id;
        $user->data = $result->data;
        $user->photo = $resultImage;
        $user->address = $request->post("address");
        $user->phone = $request->post("phone");

        $user->save();

        return response()->json(['user' => new ContractUserResource($user), 'status' => true]);
    }


}
