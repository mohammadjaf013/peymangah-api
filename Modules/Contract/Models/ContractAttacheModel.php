<?php

namespace Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Contract\Models\ContractModel;
use QCod\ImageUp\HasImageUploads;

class ContractAttacheModel extends Model
{
    use HasImageUploads;

    protected $table = "contract_attache";

    protected static $imageFields = [


    ];
    protected static $fileFields  = [
        'file'=>[
            'path' => 'file',
        ],

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
}
