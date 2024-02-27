<?php

namespace Modules\Contract\App\Resources\Template;


use Illuminate\Http\Resources\Json\JsonResource;

class ContractContentTempResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'sort_order' => $this->sort_order,
            'params' => $this->params,
        ];
    }
}
