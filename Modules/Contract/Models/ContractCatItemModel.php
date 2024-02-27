<?php

namespace Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Contract\Models\ContractUserModel;
use Spatie\EloquentSortable\SortableTrait;

class ContractCatItemModel extends Model
{
    use SortableTrait;
    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    public function buildSortQuery()
    {
        return static::query()->where('contract_id', $this->item_id);
    }

    protected $table = "contract_cat_item";

    protected static function boot()
    {
        parent::boot();

        $creationCallback = function ($model) {
            $model->code = Str::uuid()->toString();
        };

        static::creating($creationCallback);
    }


    public function contents(){
        return $this->hasMany(ContractItemModel::class,"contract_id","id");
    }


}
