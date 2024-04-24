<?php

namespace Modules\Contract\Models\Sign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Contract\Models\ContractModel;
use Modules\Contract\Models\ContractUserModel;
use Modules\Contract\Models\Receipt\ReceiptModel;
use Modules\Contract\Models\Receipt\ReceiptUserModel;

class SignAuthReceiptModel extends Model
{

    protected $table = "sign_auth_receipt";

    public const UPDATED_AT = null;
    protected static function boot()
    {
        parent::boot();

        $creationCallback = function ($model) {
            $model->code = Str::uuid()->toString();
        };

        static::creating($creationCallback);
    }


    public function user(){

        return $this->hasOne(ReceiptUserModel::class,"id","user_id");
    }

    public function receipt(){

        return $this->hasOne(ReceiptModel::class,"id","receipt_id");
    }


}
