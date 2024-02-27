<?php

namespace Modules\Contract\Models\contract_template;


use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;

class ContractItemTempModel extends Model
{
    use SortableTrait;
    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    protected $table = "t_contract_item";

    public function buildSortQuery()
    {
        return static::query()->where('item_cat_id', $this->item_cat_id);
    }


    public function contents()
    {
        return $this->hasMany(ContractItemContentTempModel::class,"item_id","id")->orderBy("sort_order");
    }


//    public function cats()
//    {
//        return $this->hasMany()
//    }

}
