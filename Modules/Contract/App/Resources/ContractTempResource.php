<?php

namespace Modules\Contract\App\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Contract\App\Resources\Template\ContractCatItemTempResource;
use Modules\Contract\Models\contract_template\ContractItemTempModel;

class ContractTempResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'cats'=>ContractCatItemTempResource::collection($this->items)
        ];
    }
}
