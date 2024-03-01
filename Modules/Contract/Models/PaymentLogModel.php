<?php

namespace Modules\Contract\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentLogModel extends Model
{

    protected $table = "payment_log";

    protected static function boot()
    {
        parent::boot();

        $creationCallback = function ($model) {
            $model->code = Str::uuid()->toString();
        };

        static::creating($creationCallback);
    }

    protected $casts=[
        'is_paid'=>'boolean'
    ];

}
