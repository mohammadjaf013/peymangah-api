<?php

namespace Modules\Contract\Models\Receipt;




use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Contract\Models\ContractModel;
use QCod\ImageUp\HasImageUploads;

class ReceiptUserModel extends Model
{
    use HasImageUploads;

    protected $table = "receipt_users";

    protected static $imageFields = [


    ];
    protected static $fileFields  = [
        'video'=>[
            'path' => 'videos',
        ],
        'face'=>[
            'path' => 'faces',

        ],
        'signature'=>[
            'path' => 'signature',

        ]

    ];
    public const UPDATED_AT = null;

    protected static function boot()
    {
        parent::boot();

        $creationCallback = function ($model) {
            $model->code = Str::uuid()->toString();
        };

        static::creating($creationCallback);
    }


    public function receipt()
    {
        return $this->hasOne(ReceiptModel::class,"id","receipt_id");
    }

    protected $casts =[
        'photo'=>'encrypted',
        'birthday'=>'encrypted',
        'data'=>'encrypted:array',
        'is_signed'=>'boolean',
    ];


}
