<?php

namespace Modules\Contract\App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'=>$this->alias,
            'title'=>$this->title,
            'hint'=>$this->hint,
            'description'=>$this->description,
        ];
    }
}
