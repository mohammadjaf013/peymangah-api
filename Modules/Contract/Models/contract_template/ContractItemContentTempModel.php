<?php

namespace Modules\Contract\Models\contract_template;



use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;

class ContractItemContentTempModel extends Model
{

    use SortableTrait;
    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    protected $table = "t_contract_content";
    public function buildSortQuery()
    {
        return static::query()->where('item_id', $this->item_id);
    }



}
