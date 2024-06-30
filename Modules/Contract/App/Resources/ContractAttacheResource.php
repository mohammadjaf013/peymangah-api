<?php

namespace Modules\Contract\App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Contract\App\Resources\ContractItemContentResource;

class ContractAttacheResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        return [
            'id'=>$this->code,
            'file'=>$this->fileUrl("file"),
        ];
    }
}
