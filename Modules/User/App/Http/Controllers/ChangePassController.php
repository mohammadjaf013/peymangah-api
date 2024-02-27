<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\App\Http\Requests\ChangePassRequest;

class ChangePassController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(ChangePassRequest $request): JsonResponse
    {

        $user = auth()->user();

        if(!\Hash::check($request->post("currentPassword"), $user->password)){
            return response()->json(['status'=>false,'errors'=>[ 'currentPassword'=>['کلمه عبور فعلی شما صحیح نمیباشد.']]],422);
        }
        $user->password = $request->post("password");
        $user->update();
        return response()->json(['status'=>true]);
    }

}
