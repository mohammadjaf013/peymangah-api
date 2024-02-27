<?php

namespace Modules\Contract\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Contract\Models\ContractUserModel;
use Spatie\EloquentSortable\SortableTrait;

class ContractItemModel extends Model
{
    use SortableTrait;
    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    public function buildSortQuery()
    {
        return static::query()->where('contract_catitem_id', $this->contract_catitem_id);
    }
    protected $table = "contract_content";

    protected static function boot()
    {
        parent::boot();

        $creationCallback = function ($model) {
            $model->code = Str::uuid()->toString();
        };

        static::creating($creationCallback);
    }


}
