<?php

namespace App\Admin\Controllers;

use App\FeedSocials;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\PostalCode;

class FeedSocialsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Feed sociales';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new FeedSocials());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('url', __('Link'));
        $grid->column('description', __('Description'));
        $grid->column('id_municipality', __('Id municipality'));
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
        $show = new Show(FeedSocials::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('id_municipality', __('Id municipality'));
        $show->field('url', __('Link'));
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
        $form = new Form(new FeedSocials());

        $form->text('name', __('Nombre'));
        $form->text('description', __('Descripción'));
        $form->text('url', __('Link'));
        $form->select('id_municipality',"Municipio")->ajax("/admin/api/findmunicipality")->options(function($id){
          $id_cp = PostalCode::find($id);

          if($id_cp){
            return [$id_cp->id => $id_cp->d_municipio];
          }
        });

        return $form;
    }
}
