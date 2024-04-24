<?php

namespace Modules\Contract\App\Resources\Receipt;

use App\Libs\Helper\UrlHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptUserResource extends JsonResource
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
            'email'=>$this->email,
            'is_signed'=>$this->is_signed,
            'signature'=>$this->fileUrl("signature"),

            'link'=>UrlHelper::url("receipt/".$this->receipt->code."/".$this->code),
        ];
    }
}
