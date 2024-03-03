<?php

namespace Modules\Contract\App\Resources\Sign;


use App\Libs\Helper\UrlHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class SignUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'=>$this->code,
            'mobile'=>$this->mobile,
            'ssn'=>$this->ssn,
            'birthday'=>$this->birthday,
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'address'=>$this->address,
            'phone'=>$this->phone,
            'title'=>$this->title,
            'is_signed'=>$this->is_signed,
            'sign_draw'=>$this->sign_draw,
            'link'=>UrlHelper::url("sign/".$this->contract->code."/".$this->code),
        ];
    }
}
