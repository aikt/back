<?php

namespace App\Admin\Controllers;

use App\Houses;
use App\PostalCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class HousesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Casas';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Houses());

        $grid->filter(function($filter){
            // Add a column filter
            $filter->like('title', 'titulo');
        });

        $grid->column('id', __('Id'));
        $grid->column('title', __('Titulo'));
        $grid->column('detail', __('Detalle'));
        $grid->column('price', __('Precio'));
        $grid->column('status', __('Status'));

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
        $show = new Show(Houses::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('price', __('Price'));
        $show->field('status', __('Status'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Houses());

        $form->text('title', __('Titulo'))->disable();
        $form->text('detail', __('Detalle'))->disable();
        $form->image('image',__('Imagen'))->disable();
        $form->select('status',"Status")->options([1 => "Publicada", 2 => "Borrador"])->default(1);

        return $form;
    }
}
