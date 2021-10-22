<?php

namespace App\Admin\Controllers;

use App\Copos;
use App\PostalCode;
use App\News;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;

class CoposController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Copos';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Copos());

        $grid->filter(function($filter){

            // Remove the default id filter
            $filter->disableIdFilter();

            // Add a column filter
            $filter->like('title', 'titulo');
        });

        $grid->column('title', __('Title'));
        // $grid->column('postalcodes')->display(function ($listCopos) {
        //   if(!empty($listCopos)){
        //     $list_copos = "";
        //     foreach ($listCopos as $key => $value) {
        //       dump($value);
        //       $list_copos.= $value["d_municipio"].",";
        //     }
        //     return $list_copos;
        //   }
        // });

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
        $show = new Show(Copos::findOrFail($id));

        $show->field('title', __('Title'));
        $show->field('postalcodes', PostalCode::class)->as(function ($roles) {
            return $roles->pluck('d_asenta');
        })->label();

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Copos());

        $form->hidden("id");
        $form->text('title', __('Title'));
        $form->multipleSelect('postalcodes',"Codigo Postal")->ajax("/admin/api/postalcodescopos")->options(function($listCopos){
          $list_copos = array();
          if(!empty($listCopos)){
            foreach ($listCopos as $key => $valueCopoId) {
              $id_cp = PostalCode::where("d_codigo","=",$valueCopoId)->take(1)->get()->all();
              if($id_cp){
                if(count($id_cp) > 0){
                  $list_copos[$id_cp[0]->d_codigo] = $id_cp[0]->d_codigo." - ".$id_cp[0]->d_asenta." - ".$id_cp[0]->d_municipio;
                }
              }
            }
          }
          return $list_copos;
        });
        $form->multipleSelect('coposhascopos', __('Copos'))->options(Copos::all()->pluck('title', 'id'));
        $form->text('description', __('Descripción'));
        $form->multipleImage('images', __('Imagenes'));

        $form->saving(function (Form $form) {
          foreach ($form->postalcodes as $key => $value) {
            if(!empty($value)){
              $queryNews = News::where("url","LIKE","%".$value."%")->get();
              $postalCode = PostalCode::where("d_codigo","=",$value)->take(1)->get()->all()[0];
              if($queryNews){
                if(count($queryNews->all()) > 0){
                  foreach ($queryNews->all() as $keyId => $valueID) {
                    $url_copo = "/".$this->slugify($postalCode->d_estado)."/".$this->slugify($postalCode->d_municipio)."/".$this->slugify($form->title)."/".$postalCode->d_codigo."/".$this->slugify($valueID->title);
                    if($url_copo != $valueID->url){
                      if($valueID->url_copo != $url_copo){
                        $valueID->url_copo = $url_copo;
                        $valueID->url = $url_copo;
                        $valueID->save();
                      }
                    }
                  }

                }
              }
            }
          }
        });
        return $form;
    }

    function slugify($text)
    {
      // Strip html tags
      $text=strip_tags($text);
      // Replace non letter or digits by -
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);
      // Transliterate
      setlocale(LC_ALL, 'en_US.utf8');
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
      // Remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);
      // Trim
      $text = trim($text, '-');
      // Remove duplicate -
      $text = preg_replace('~-+~', '-', $text);
      // Lowercase
      $text = strtolower($text);
      // Check if it is empty
      if (empty($text)) { return 'n-a'; }
      // Return result
      return $text;
    }
}
