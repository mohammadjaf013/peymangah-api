<?php

namespace Modules\Contract\Models;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContractUserModel extends Model
{

    protected $table = "contract_users";

    public const UPDATED_AT = null;

    protected static function boot()
    {
        parent::boot();

        $creationCallback = function ($model) {
            $model->code = Str::uuid()->toString();
        };

        static::creating($creationCallback);
    }


    protected $casts =[
        'photo'=>'encrypted',
        'birthday'=>'encrypted',
        'data'=>'encrypted:array',
    ];


}
