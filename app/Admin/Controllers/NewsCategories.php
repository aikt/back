<?php

namespace App\Admin\Controllers;

use App\NewsSections;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class NewsCategories extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Secciones para noticias';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new NewsSections());

        $grid->column('id', __('Id'));
        $grid->column('category', __('Category'));
        $grid->column('message_suscription', __('Message suscription'));
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
        $show = new Show(NewsSections::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category', __('Category'));
        $show->field('message_suscription', __('Message suscription'));
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
        $form = new Form(new NewsSections());

        $form->text('category', __('Category'));
        $form->text('message_suscription', __('Message suscription'));

        return $form;
    }
}
