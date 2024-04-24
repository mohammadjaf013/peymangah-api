<?php

namespace Modules\Contract\Models\Receipt;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Contract\Models\contract_template\ContractCatItemTempModel;
use Modules\Contract\Models\ContractCategoryModel;
use Modules\Contract\Models\ContractCatItemModel;
use Modules\Contract\Models\ContractUserModel;

class ReceiptModel extends Model
{

    protected $table = "receipt";

    protected static function boot()
    {
        parent::boot();

        $creationCallback = function ($model) {
            $model->code = Str::uuid()->toString();
        };

        static::creating($creationCallback);
    }


    public function users(){
        return $this->hasMany(ReceiptUserModel::class,"receipt_id","id");
    }


}
