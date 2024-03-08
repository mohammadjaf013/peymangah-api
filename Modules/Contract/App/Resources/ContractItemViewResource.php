<?php

namespace Modules\Contract\App\Resources;



use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Contract\App\Resources\ContractItemContentResource;

class ContractItemViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        return [
            'id'=>$this->id,
            'title'=>$this->title,
//            'sort_order'=>$this->sort_order,
//            'contents'=>ContractItemContentResource::collection($this->contents)
        ];
    }
}
