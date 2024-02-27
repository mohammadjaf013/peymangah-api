<?php

namespace Modules\User\App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPublicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'photo'=>null,//$this->email,
        ];
    }
}
