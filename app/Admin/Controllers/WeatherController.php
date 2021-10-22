<?php

namespace App\Admin\Controllers;

use App\Weather;
use Encore\Admin\Controllers\AdminController;
use App\PostalCode;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;

class WeatherController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Clima';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Weather());

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('id_municipality',__('Municipio'))->display(function($id_municipality){
          $state =PostalCode::find($id_municipality);
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
        $show = new Show(Weather::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
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
        $form = new Form(new Weather());

        $form->text('title', __('Title'));
        $form->ckeditor('content', __('Contenido HTML'));
        $form->select('id_municipality',"Municipio")->ajax("/admin/api/findmunicipality")->options(function($id){
          $id_cp = PostalCode::find($id);

          if($id_cp){
            return [$id_cp->id => $id_cp->d_municipio];
          }
        });

        return $form;
    }
}
