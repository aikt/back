<?php

namespace App\Admin\Controllers;

use App\Medios;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\PostalCode;

class MediosController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Medios';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Medios());

        $grid->column('id', __('Id'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('image', __('Image'));
        $grid->column('title', __('Title'));
        $grid->column('description', __('Description'));
        $grid->column('id_municipality', __('Id municipality'));
        $grid->column('id_state', __('Id state'));

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
        $show = new Show(Medios::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('image', __('Image'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
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
        $form = new Form(new Medios());

        $form->text('title', __('Titulo'));
        $form->text('description', __('Descripción'));
        $form->text('url', __('Url sitio'));
        $form->image('image', __('Imagen'));
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

        return $form;
    }
}
