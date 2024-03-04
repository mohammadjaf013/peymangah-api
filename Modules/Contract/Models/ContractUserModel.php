<?php

namespace Modules\Contract\Models;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use QCod\ImageUp\HasImageUploads;

class ContractUserModel extends Model
{
    use HasImageUploads;

    protected $table = "contract_users";

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


    public function contract()
    {
        return $this->hasOne(ContractModel::class,"id","contract_id");
    }

    protected $casts =[
        'photo'=>'encrypted',
        'birthday'=>'encrypted',
        'data'=>'encrypted:array',
        'is_signed'=>'boolean',
    ];


}
