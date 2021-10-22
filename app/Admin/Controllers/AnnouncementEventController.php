<?php

namespace App\Admin\Controllers;

use App\AnnouncementEvent;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\PostalCode;

class AnnouncementEventController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Eventos';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AnnouncementEvent());

        $grid->column('id', __('Id'));
        $grid->column('title', __('Titulo'));
        $grid->column('description', __('Descripción'));
        $grid->column('content', __('Contenido'));
        $grid->column('contact', __('Contacto'));
        $grid->column('place', __('Lugar'));
        $grid->column('schedule', __('Horario'));
        $grid->column('room', __('Sala'));
        $grid->column('url_visit', __('Url evento'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('id_municipality',__('Municipio'))->display(function($id_state){
          $state =PostalCode::find($id_state);
          if(isset($state->d_municipio)){
            return $state->d_municipio;
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
        $show = new Show(AnnouncementEvent::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('content', __('Content'));
        $show->field('contact', __('Contact'));
        $show->field('place', __('Place'));
        $show->field('schedule', __('Schedule'));
        $show->field('room', __('Room'));
        $show->field('url_visit', __('Url visit'));
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
        $form = new Form(new AnnouncementEvent());

        $form->text('title', __('Titulo'));
        $form->text('description', __('Descripción'));
        $form->textarea('content', __('Resumen'));
        $form->text('contact', __('Correo contacto'));
        $form->text('place', __('Lugar'));

        $form->date('expiration_date', 'Fecha Termino');

        $form->text('schedule', __('Horario'));
        $form->text('room', __('Sala'));
        $form->text('url_visit', __('Url evento'));
        $form->image('image', __('Imagen'));
        $form->select('id_municipality',"Municipio")->ajax("/admin/api/findmunicipality")->options(function($id){
          $id_cp = PostalCode::find($id);

          if($id_cp){
            return [$id_cp->id => $id_cp->d_municipio];
          }
        });

        return $form;
    }
}
