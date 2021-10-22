<?php

namespace App\Admin\Controllers;

use App\DescriptionPostalCodes;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\PostalCode;

class DescriptionPostalCodesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Descripciones de codigos postales';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DescriptionPostalCodes());

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('id_municipality',__('Municipio'))->display(function($id_state){
          $state =PostalCode::find($id_state);
          if(isset($state->d_municipio)){
            return $state->d_municipio;
          }else{
            return "";
          }
        });
        $grid->column('id_state',__('Estado'))->display(function($id_state){
          $state =PostalCode::find($id_state);
          if(isset($state->d_estado)){
            return $state->d_estado;
          }else{
            return "";
          }
        });
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
        $show = new Show(DescriptionPostalCodes::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('title', __('Title'));
        $show->field('id_municipality', __('Id municipality'));
        $show->field('id_state', __('Id state'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new DescriptionPostalCodes());

        $form->text('title', __('Title'));
        $form->select('id_municipality',"Municipio")->ajax("/admin/api/findmunicipality")->options(function($id){
          $id_cp = PostalCode::find($id);

          if($id_cp){
            return [$id_cp->id => $id_cp->d_municipio];
          }
        });
        $form->select('id_state',"Estado")->ajax("/admin/api/findstate")->options(function($id){
          $id_cp = PostalCode::find($id);

          if($id_cp){
            return [$id_cp->id => $id_cp->d_estado];
          }
        });
        $form->text('description', __('Descripción'));
        $form->multipleImage('images', __('Imagenes'));

        return $form;
    }
}
