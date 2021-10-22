<?php

namespace App\Admin\Controllers;

use App\CopoManagers;
use App\CopoMessages;

use Encore\Admin\Controllers\AdminController;
use App\PostalCode;
use App\AdminUsers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CopoManagersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Manejadores de copo';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CopoManagers());

        $grid->column('id', __('Id'));
        $grid->column('id_admin_user',__('Manager'))->display(function($id_admin_user){
            $admin_user = AdminUsers::find($id_admin_user);
            if(isset($admin_user->id)){
                return $admin_user->name . $admin_user->last_name;
            }
            else
            {
                return "";
            }
        });

        $grid->column('id_municipality',__('Municipio'))->display(function($id_municipality){
          $state = PostalCode::find($id_municipality);
          if(isset($state->d_municipio)){
            return $state->d_municipio;
          }else{
            return "";
          }
        });

        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(CopoManagers::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('id_admin_user', __('Id CopoManager'));

        $show->field('id_municipality', __('Id municipality'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CopoManagers());

        $form->select('id_admin_user',__("Manager"))->options(AdminUsers::all()->pluck('name', 'id'));

        $form->select('id_municipality',"Municipio")->ajax("/admin/api/findmunicipality")->options(function($id){
          $id_cp = PostalCode::find($id);

          if($id_cp){
            return [$id_cp->id => $id_cp->d_municipio];
          }
        });

        $form->multipleSelect('managerhasmessages', __('Manager Message'))->options(CopoMessages::all()->pluck('title', 'id'));


        $form->display('created_at', __('Created At'));
        $form->display('updated_at', __('Updated At'));

        return $form;
    }
}
