<?php

namespace Modules\Contract\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Contract\Models\contract_template\ContractCatItemTempModel;

class ContractModel extends Model
{

    protected $table = "contract";

    protected static function boot()
    {
        parent::boot();

        $creationCallback = function ($model) {
            $model->code = Str::random(24);
        };

        static::creating($creationCallback);
    }


    public function users(){
        return $this->hasMany(ContractUserModel::class,"contract_id","id");
    }


    public function items(){
        return $this->hasMany(ContractCatItemModel::class,"contract_id","id");
    }
    public function category(){
        return $this->hasOne(ContractCategoryModel::class,"id","category_id");
    }

    public function item(){
        return $this->hasOne(ContractCatItemTempModel::class,"id","category_item_id");
    }

    public function attaches(){
        return $this->hasMany(ContractAttacheModel::class,"contract_id","id");
    }

}
