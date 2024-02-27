<?php

namespace Modules\Contract\App\Resources;



use Illuminate\Http\Resources\Json\JsonResource;

class ContractItemContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'=>$this->code,
            'content'=>$this->content,
            'params'=>$this->params,
            'sort_order'=>$this->sort_order,
        ];
    }
}
