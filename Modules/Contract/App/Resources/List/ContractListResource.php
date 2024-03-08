<?php
namespace Modules\Contract\App\Resources\List;


use Illuminate\Http\Resources\Json\JsonResource;

class ContractListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'=>$this->code,
            'title'=>$this->title,
            'category_id'=>$this->category_id ,
            'category'=>$this->category->title,
            'category_item_id'=>$this->category_item_id,
//            'category_item'=>$this->item->title,
            'is_paid'=>(bool) $this->is_paid,
            'is_locked'=>(bool) $this->is_locked,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'status'=>$this->status,
            'price'=>$this->price,
            'singCount'=>$this->users->count(),
            'isSignedCount'=>$this->users->where("is_signed",1)->count(),
        ];
    }
}
