<?php

namespace App\Admin\Controllers;

use App\Feed;
use App\Author;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FeedController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Feed';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Feed());

        $grid->column('id', __('Id'));
        $grid->column('url', __('url'));
        $grid->column('id_author', __('Id author'));
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
        $show = new Show(Feed::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('url', __('url'));
        $show->field('id_author', __('Id author'));
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
        $form = new Form(new Feed());

        $form->text('url', __('URL Feed'));
        $form->text('domain', __('Dominio'));
        $form->select('id_author',__("Author"))->options(Author::all()->pluck('name', 'id'));
        $form->select('type',__("Tipo Feed"))->options([1 => "Intro", 2 => "Completo"]);

        return $form;
    }
}
