<?php

namespace Modules\Contract\App\Resources\Receipt;


use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Contract\App\Resources\Template\ContractCatItemTempResource;
use Modules\Contract\Models\contract_template\ContractItemTempModel;

class ReceiptDetailsResource extends JsonResource
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
            'updated_at'=>$this->updated_at,
            'is_paid'=>$this->is_paid,
            'is_locked'=>$this->is_locked,
            'price'=>$this->price,
            'step'=>$this->step,
            'status'=>$this->status,
            'users'=>ReceiptUserResource::collection($this->users),
            'singCount'=>$this->users->count(),
            'isSignedCount'=>$this->users->where("is_signed",1)->count(),
        ];
    }
}
