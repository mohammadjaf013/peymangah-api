<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libs\Service\KycSystem;
use Illuminate\Http\JsonResponse;
use Modules\User\App\Http\Requests\UpdateDataRequest;
use Modules\User\Models\UserValidationModel;

class UserController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function updateData(UpdateDataRequest $request): JsonResponse
    {

        $user = auth()->user();
       /* $kycSystem = new KycSystem("itsaz");

        $validateData = UserValidationModel::query()->where("user_id", $user->id)->first();
        if (!$validateData) {
            $validateData = new UserValidationModel();
            $validateData->user_id = $user->id;
        }
        if (!$validateData->shahkar) {
            $ssnResult = $kycSystem->shahkar($request->post('nationalCode'), $user->mobile, $user->id);
            if ($ssnResult == false) {
                return response()->json(['error' => "خطا در دریافت اطلاعات", 'status' => false], 405);
            }
            if ($ssnResult['match'] === false) {
                $validateData->ssn = 0;
                $validateData->update();
                return response()->json(['errors' => ['nationalCode' => ['کد ملی شما با شماره موبایل ثبت شده در سیستم مطابقت ندارد.']], 'status' => false], 422);
            } else {
                $validateData->shahkar = true;
                $validateData->shahkar_data = $ssnResult;
                $validateData->save();
            }
        }*/

      /*  if (!$validateData->shahkar == 0) {
            $result = $kycSystem->civil($request->post('nationalCode'), $request->post('birthDay'),time());
            if($result === 0){
                return response()->json(['errors' => ['nationalCode' => ['اطلاعات کد ملی و تاریخ تولد شما مطابقت ندارد.']], 'status' => false], 422);

            }
            if($result === -1){
                return response()->json(['error' => "خطا در دریافت اطلاعات", 'status' => false], 405);
            }

            $resultImage = $kycSystem->personPhoto($request->post('nationalCode'), $request->post('birthDay'));
            $validateData->inquery = true;
            $validateData->inquery_data = $result;
            $validateData->image= $resultImage;
            $validateData->save();


            $user->real_first_name= $result->data->firstName;
            $user->real_last_name= $result->data->lastName;
            $user->ssn= $result->data->nationalCode;
            $user->is_verified= true;
            $user->email= $request->post("email");
            $user->save();



        }*/


        $user->birthDay= $request->post("birthDay");
        $user->nationalCode= $request->post("nationalCode");
        $user->email= $request->post("email");
        $user->save();

        return response()->json();

    }
}
