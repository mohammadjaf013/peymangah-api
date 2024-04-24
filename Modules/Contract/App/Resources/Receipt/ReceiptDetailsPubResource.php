<?php

namespace Modules\Contract\App\Resources\Receipt;



use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Contract\App\Resources\Receipt\ReceiptUserResource;
use Modules\Contract\App\Resources\Template\ContractCatItemTempResource;
use Modules\Contract\Models\contract_template\ContractItemTempModel;

class ReceiptDetailsPubResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {


        return [
            'id'=>$this->code,
            'title'=>$this->title,
            'body'=>$this->body,
            'created_at'=>$this->created_at,
            'users'=>ReceiptUserResource::collection($this->users),
        ];
    }
}
