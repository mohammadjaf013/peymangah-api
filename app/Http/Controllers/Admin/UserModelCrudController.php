<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserModelRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ModulesUserModelsUserModelCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserModelCrudController extends CrudController
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
        CRUD::setModel(\App\Models\UserAdminModel::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/users');
        CRUD::setEntityNameStrings('کاربران', 'کاربران');
        CRUD::enableExportButtons();

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
//        CRUD::setFromDb();
        $this->crud->addColumn([
            'name'      => 'row_number',
            'type'      => 'row_number',
            'label'     => '#',
            'orderable' => false,
        ])->makeFirstColumn();
        CRUD::column('first_name')->type('Text')->label("نام");
        CRUD::column('last_name')->type('Text')->label("نام خانوادگی");
        CRUD::column('mobile')->type('Text')->label("موبایل");
        $this->crud->query->withCount('contracts');

        $this->crud->addColumn([
            'name'      => 'contracts',
            'type'      => 'relationship_count',
            'label'     => 'تعداد قرارداد',
            'suffix' => ' قرارداد',
             'orderable' => true,

        ]);
//        CRUD::filter('mobile')->label('Name');
//        CRUD::column('')->type('string');
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
        CRUD::setValidation(UserModelRequest::class);
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
        $this->setupCreateOperation();
    }

    protected function autoSetupShowOperation()
    {

        CRUD::column('id')->type('number')->label("شناسه");
        CRUD::column('first_name')->type('Text')->label("نام");
        CRUD::column('last_name')->type('Text')->label("نام خانوادگی");
        CRUD::column('mobile')->type('Text')->label("موبایل");
        $this->crud->query->withCount('contracts');
        $this->crud->addColumn([
            'name'      => 'contracts',
            'type'      => 'relationship_count',
            'label'     => 'تعداد قرارداد',
            'suffix' => ' قرارداد',
            'orderable' => true,

        ]);

        CRUD::column('real_first_name')->type('Text')->label("نام شناسنامه");
        CRUD::column('real_last_name')->type('Text')->label("نام خانوادگی شناسنامه");
        CRUD::column('create')->type('Text')->label("تاریخ عصویت");


    }


}
