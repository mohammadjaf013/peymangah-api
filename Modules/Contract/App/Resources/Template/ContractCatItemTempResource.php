<?php

namespace Modules\Contract\App\Resources\Template;


use Illuminate\Http\Resources\Json\JsonResource;

class ContractCatItemTempResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'sort_order' => $this->sort_order,
            'contents'=>ContractContentTempResource::collection($this->contents)
        ];
    }
}
