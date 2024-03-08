<?php

namespace Modules\User\App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTransactionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'price'=>$this->price,
            'id'=>$this->code,
            'contract_id'=>($this->contract)  ?  $this->contract->code : "",
            'contract_title'=> ($this->contract)  ?  $this->contract->title : "",
            'is_paid'=>(boolean)$this->is_paid,
            'reference'=>$this->reference,
            'receipt_id'=>$this->receipt_id,
            'created_at'=>$this->created_at,
        ];
    }
}
