<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Contract\Models\contract_template\ContractCatItemTempModel;
use Modules\Contract\Models\ContractCategoryModel;
use Modules\Contract\Models\ContractCatItemModel;
use Modules\Contract\Models\ContractUserModel;
use Morilog\Jalali\Jalalian;

class ContractAdminModel extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'contract';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function users(){
        return $this->hasMany(ContractUserModel::class,"contract_id","id");
    }


    public function items(){
        return $this->hasMany(ContractCatItemModel::class,"contract_id","id");
    }
    public function category(){
        return $this->hasOne(ContractCategoryModel::class,"id","category_id");
    }

    public function item(){
        return $this->hasOne(ContractCatItemTempModel::class,"id","category_item_id");
    }

    public function persian_date($attr)
    {

        if(is_null($this->{$attr}))
            return $this->{$attr};

        return Jalalian::fromCarbon(Carbon::parse($this->{$attr}))->format("Y/m/d H:i");
    }


}
