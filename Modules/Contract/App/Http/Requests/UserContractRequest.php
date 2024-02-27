<?php

namespace Modules\Contract\App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserContractRequest extends FormRequest
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
            'mobile' => 'required|ir_mobile:zero',
            'birthDay' => 'required|shamsi_date',
            'address' => 'required',
            'phone' => 'required|ir_phone_with_code',

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
