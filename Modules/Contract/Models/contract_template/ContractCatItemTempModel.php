<?php

namespace Modules\Contract\Models\contract_template;



use Illuminate\Database\Eloquent\Model;
use Modules\Contract\Models\ContractCategoryModel;

class ContractCatItemTempModel extends Model
{


    protected $table = "t_contract_cat_item";


    public function items()
    {
        return $this->hasMany(ContractItemTempModel::class,"item_cat_id","id")->orderBy("sort_order");
    }

    public function mainCat()
    {
        return $this->hasOne(ContractCategoryModel::class,"id","cat_id");
    }

}
