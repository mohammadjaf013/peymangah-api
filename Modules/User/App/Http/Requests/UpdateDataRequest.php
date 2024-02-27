<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDataRequest extends FormRequest
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
            'nationalCode' => 'required',
            'birthDay' => 'required|shamsi_date',

        ];
    }
    public function attributes()
    {
        return [
            'nationalCode' => 'کد ملی',
            'birthDay' => 'تاریخ تولد',
        ];
    }

}
