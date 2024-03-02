<?php

namespace Modules\Contract\Models\Sign;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Contract\Models\ContractModel;
use Modules\Contract\Models\ContractUserModel;

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


    public function user(){

        return $this->hasOne(ContractUserModel::class,"id","user_id");
    }

    public function contract(){

        return $this->hasOne(ContractModel::class,"id","contract_id");
    }


}
