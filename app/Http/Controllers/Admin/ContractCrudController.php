<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ContractModelRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ModulesContractModelsContractModelCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ContractCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ContractAdminModel::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/contracts');
        CRUD::setEntityNameStrings('قراردادها', 'قراردادها');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
//        CRUD::setFromDb(); // set columns from db columns.
         $this->crud->addColumn([
             'name'      => 'row_number',
             'type'      => 'row_number',
             'label'     => '#',
             'orderable' => false,
         ])->makeFirstColumn();
              $this->crud->addColumn([
                  // any type of relationship
                  'name'         => 'title',
                  'type'         => 'text',
                  'label'        => 'عنوان',
              ]);
              $this->crud->addColumn([
                  // any type of relationship
                  'name'         => 'category.title',
                  'type'         => 'text',
                  'label'        => 'دسته بندی',
              ]);
              $this->crud->addColumn([
                  // any type of relationship
                  'name'         => 'item.title',
                  'type'         => 'text',
                  'label'        => 'زیر دسته',
              ]);
              $this->crud->addColumn([
                  // any type of relationship
                  'name'         => 'status',
                  'type'         => 'select_from_array',
                  'label'        => 'وضعیت',
                  'options' => ['draft' => 'پیش نویش', 'signing' => 'در انتظار امضا', 'completed'=>'اتمام یافته','cancel'=>'کنسل شده',],
              ]);
        $this->crud->addColumn([
            'name'      => 'is_paid',
            'type'      => 'boolean',
            'label'     => 'وضعیت پرداخت ',
            'options' => [0 => 'پرداخت نشده', 1 => 'پرداخت شده']
        ]);
        CRUD::column('price')->type('number')->label("قیمت");
        $this->crud->addColumn([
            'name'      => 'created_at',
            'type'      => 'model_function',
            'label'     => 'تاریخ ایجاد',
            'function_parameters' =>['created_at'],
            'function_name' => 'persian_date',
        ]);

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ContractModelRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
//        $this->setupCreateOperation();
        $this->crud->addField("title");
        $this->crud->addField([
            'name' => 'title',
            'label' => 'عنوان',
            'type' => 'text',
            'placeholder' => 'عنوان قرارداد',
            'validationRules' => 'required|min:10',
//            'validationMessages' => [
//                'required' => 'You gotta write smth man.',
//                'min' => 'More than 10 characters, bro. Wtf... You can do this!',
//            ]
        ]);
        CRUD::field('price')->prefix('تومان')->label("قیمت");
        CRUD::field([
            'name'        => 'status',
            'label'       => "وضعیت",
            'type'        => 'select_from_array',
            'options'     =>  ['draft' => 'پیش نویش', 'signing' => 'در انتظار امضا', 'completed'=>'اتمام یافته','cancel'=>'کنسل شده',],
            'allows_null' => false,
            'default'     => 'draft',
        ]);
        CRUD::field([
            'name'        => 'is_paid',
            'label'       => "وضعیت پرداخت",
            'type'        => 'select_from_array',
            'options'     =>  [0 => 'پرداخت نشده', 1 => 'پرداخت شده'],
            'allows_null' => false,
            'default'     => '0',
        ]);




        // CAREFUL! This MUST be called AFTER the fields are defined, NEVER BEFORE
        $this->crud->setValidation();

//        $this->crud->addField("category_id");
    }

    protected function autoSetupShowOperation()
    {

        $this->crud->addColumn([
            // any type of relationship
            'name'         => 'title',
            'type'         => 'text',
            'label'        => 'عنوان',
        ]);
        $this->crud->addColumn([
            // any type of relationship
            'name'         => 'category.title',
            'type'         => 'text',
            'label'        => 'دسته بندی',
        ]);
        $this->crud->addColumn([
            // any type of relationship
            'name'         => 'item.title',
            'type'         => 'text',
            'label'        => 'زیر دسته',
        ]);
        $this->crud->addColumn([
            // any type of relationship
            'name'         => 'status',
            'type'         => 'select_from_array',
            'label'        => 'وضعیت',
            'options' => ['draft' => 'پیش نویش', 'signing' => 'در انتظار امضا', 'completed'=>'اتمام یافته','cancel'=>'کنسل شده',],
        ]);
        $this->crud->addColumn([
            'name'      => 'is_paid',
            'type'      => 'boolean',
            'label'     => 'وضعیت پرداخت ',
            'options' => [0 => 'پرداخت نشده', 1 => 'پرداخت شده']
        ]);
        CRUD::column('price')->type('number')->label("قیمت");
        $this->crud->addColumn([
            'name'      => 'created_at',
            'type'      => 'model_function',
            'label'     => 'تاریخ ایجاد',
            'function_parameters' =>['created_at'],
            'function_name' => 'persian_date',
        ]);

    }


}
