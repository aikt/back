<?php

namespace App\Admin\Controllers;

use App\Cars;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\PostalCode;
use Encore\Admin\Show;

class CarsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Carros';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Cars());

        $grid->filter(function($filter){
            // Add a column filter
            $filter->like('title', 'titulo');
        });

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('price', __('Price'));
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
        $show = new Show(Cars::findOrFail($id));

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
        $form = new Form(new Cars());

        $form->text('title', __('Titulo'))->disable();
        $form->image('image',__('Imagen'))->disable();
        $form->select('status',"Status")->options([1 => "Publicada", 2 => "Borrador"])->default(1);

        return $form;
    }
}
