<?php

namespace Modules\Contract\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContractModel extends Model
{

    protected $table = "contract";

    protected static function boot()
    {
        parent::boot();

        $creationCallback = function ($model) {
            $model->code = Str::uuid()->toString();
        };

        static::creating($creationCallback);
    }


    public function users(){

        return $this->hasMany(ContractUserModel::class,"contract_id","id");
    }


    public function items(){
        return $this->hasMany(ContractCatItemModel::class,"contract_id","id");
    }

}
