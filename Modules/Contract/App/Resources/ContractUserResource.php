<?php

namespace Modules\Contract\App\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ContractUserResource extends JsonResource
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
        ];
    }
}
