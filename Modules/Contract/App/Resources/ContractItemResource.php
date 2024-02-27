<?php

namespace Modules\Contract\App\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ContractItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'=>$this->code,
            'title'=>$this->title,
            'sort_order'=>$this->sort_order,
            'contents'=>ContractItemContentResource::collection($this->contents)
        ];
    }
}
