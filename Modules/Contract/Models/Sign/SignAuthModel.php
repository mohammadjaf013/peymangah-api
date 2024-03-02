<?php

namespace Modules\Contract\Models\Sign;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SignAuthModel extends Model
{

    protected $table = "sign_auth";

    public const UPDATED_AT = null;
    protected static function boot()
    {
        parent::boot();

        $creationCallback = function ($model) {
            $model->code = Str::uuid()->toString();
        };

        static::creating($creationCallback);
    }


//    public function users(){
//
//        return $this->hasMany(ContractUserModel::class,"contract_id","id");
//    }
//
//
//    public function items(){
//        return $this->hasMany(ContractCatItemModel::class,"contract_id","id");
//    }
//    public function category(){
//        return $this->hasOne(ContractCategoryModel::class,"id","category_id");
//    }
//
//    public function item(){
//        return $this->hasOne(ContractCatItemTempModel::class,"id","category_item_id");
//    }

}
