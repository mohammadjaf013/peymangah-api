<?php

namespace Modules\Auth\App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $obj = response()->json(['status' => false, 'errors' => $validator->errors()->toArray()], 422);
        throw new HttpResponseException($obj);;
    }

    public function rules()
    {
        return [
            'username' => 'required|ir_mobile:zero',
            'password' =>'required',
        ];
    }
}
