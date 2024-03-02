<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\App\Http\Requests\LoginRequest;
use Modules\Auth\App\Http\Requests\RegisterOtpRequest;
use Modules\Auth\App\Http\Requests\RegisterRequest;
use Modules\User\App\Resources\UserPublicResource;
use Modules\User\Models\UserModel;

class AuthController extends Controller
{
    public array $data = [];


    /**
     * Display a listing of the resource.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = UserModel::query()->where('mobile', $request->post('username', null))->first();

        if (!$user || !(Hash::check($request->post('password'), $user->password))) {
            return  response()->json(['message' => __('api.login_incorrect')],422);
        }
        $tokenResult = $user->createToken('user');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(54);
        $token->save();
        return response()->json([
            'user'=>new UserPublicResource($user),
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]);

    }

    public function registerOtp(RegisterOtpRequest $request): JsonResponse
    {

        $otpData = (new Otp)->generate($request->post("mobile"), 'numeric', 5, 60);


//        event(new OtpEvent($request->mobile,$otpData->token));

        return response()->json([]);
    }

    /**
     * Display a listing of the resource.
     */
    public function registerVerify(RegisterOtpRequest $request): JsonResponse
    {

        $validate = (new Otp)->validate($request->post("mobile"), $request->post("otp"));

        if ($validate->status) {
            return response()->json([]);
        }

        return response()->json(["message" => "کد فعال سازی شما اشتباه میباشد."], 422);
    }

    /**
     * Display a listing of the resource.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validate = (new Otp)->validate($request->post("mobile"), $request->post("otp"));

        if ($validate->status) {
            return response()->json(['message' => 'کد فعال سازی صحیح نمیباشد.']);
        }


        $user = UserModel::create($request->all());

        return response()->json([], 422);
    }

}
