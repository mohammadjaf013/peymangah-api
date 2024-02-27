<?php

namespace Modules\Contract\Models\contract_template;



use Illuminate\Database\Eloquent\Model;

class ContractCatItemTempModel extends Model
{


    protected $table = "t_contract_cat_item";


    public function items()
    {
        return $this->hasMany(ContractItemTempModel::class,"item_cat_id","id")->orderBy("sort_order");
    }

}
