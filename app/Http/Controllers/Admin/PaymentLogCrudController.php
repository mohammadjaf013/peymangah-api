<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ModulesContractModelsPaymentLogModelRequest;
use App\Models\UserAdminModel;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ModulesContractModelsPaymentLogModelCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PaymentLogCrudController extends CrudController
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
        $this->crud->denyAccess(['update', 'create', 'delete']);

        CRUD::setModel(\App\Models\PaymentLogAdminModel::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/payment-log');
        CRUD::setEntityNameStrings('تراکنش ها', 'تراکنش ها');
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
        CRUD::column('receipt_id')->type('Text')->label("شماره رسید");
        CRUD::column('reference')->type('Text')->label("کد پرداخت");
        CRUD::column('price')->type('float')->label("مبلغ");

        $this->crud->addColumn([
            // any type of relationship
            'name'         => 'user.full_name',
            'type'         => 'text',
            'label'        => 'کاربر',
        ]);
        $this->crud->addColumn([
            'name'      => 'is_paid',
            'type'      => 'boolean',
            'label'     => 'پرداخت شده',
            'options' => [0 => 'پرداخت نشده', 1 => 'پرداخت شده']
        ]);
        $this->crud->addColumn([
            'name'      => 'paid_at',
            'type'      => 'model_function',
            'label'     => 'تاریخ پرداخت',
            'function_parameters' =>['paid_at'],
            'function_name' => 'persian_date',
        ]);
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
//        CRUD::setFromDb(); // set fields from db columns.


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
        $this->setupCreateOperation();

    }

    protected function autoSetupShowOperation()
    {

        CRUD::column('id')->type('Text')->label("شناسه");
        CRUD::column('receipt_id')->type('Text')->label("شماره رسید");
        CRUD::column('reference')->type('Text')->label("کد پرداخت");
        CRUD::column('price')->type('float')->label("مبلغ");

        $this->crud->addColumn([
            // any type of relationship
            'name'         => 'user.full_name',
            'type'         => 'text',
            'label'        => 'کاربر',
        ]);
        $this->crud->addColumn([
            'name'      => 'is_paid',
            'type'      => 'boolean',
            'label'     => 'پرداخت شده',
            'options' => [0 => 'پرداخت نشده', 1 => 'پرداخت شده']
        ]);
        $this->crud->addColumn([
            'name'      => 'paid_at',
            'type'      => 'model_function',
            'label'     => 'تاریخ پرداخت',
            'function_parameters' =>['paid_at'],
            'function_name' => 'persian_date',
        ]);
        $this->crud->addColumn([
            'name'      => 'created_at',
            'type'      => 'model_function',
            'label'     => 'تاریخ ایجاد',
            'function_parameters' =>['created_at'],
            'function_name' => 'persian_date',
        ]);

        $this->crud->addColumn([
            'name'      => 'error_msg',
            'type'      => 'text',
            'label'     => 'پیام خطا',
        ]);


    }


}
