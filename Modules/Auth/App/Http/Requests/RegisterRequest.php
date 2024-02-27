<?php

namespace Modules\Auth\App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'mobile' => 'required|ir_mobile:zero|unique:users,mobile',
            'first_name' => 'required|persian_alpha|string|max:255',
            'last_name' => 'required|persian_alpha|string|max:255',
//            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',

            ],
        ];
    }
}
