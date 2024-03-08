<?php

namespace Modules\Contract\Models;


use Illuminate\Database\Eloquent\Model;
use Modules\Contract\Models\contract_template\ContractCatItemTempModel;
use Modules\Contract\Models\contract_template\ContractItemTempModel;

class ContractCategoryModel extends Model
{

    protected $table = "t_contract_category";


    public function subs(){

        return $this->hasMany(ContractCatItemTempModel::class,"cat_id","id");
    }

}
