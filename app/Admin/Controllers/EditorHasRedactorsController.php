<?php

namespace App\Admin\Controllers;

use App\EditorHasRedactors;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\DB;

class EditorHasRedactorsController extends AdminController
{
    public function findEditors(){
      $objUserEditor = config('admin.database.users_model');
      $editors = DB::table("admin_users")
                    ->join("admin_role_users","admin_users.id","=","admin_role_users.user_id")
                    ->join("editor_has_redactors","admin_users.id","!=","editor_has_redactors.editor_id")
                    ->select("admin_users.id as id","admin_users.name as text")
                    ->where("admin_role_users.role_id","=",3)->paginate(99999);

      return $editors;
    }
    public function findRedactors(){
      $objUserEditor = config('admin.database.users_model');
      $redactorsInEditors = DB::table("editor_has_redactors")->get()->all();
      $idsRedactors = array();
      if(count($redactorsInEditors) > 0){
        foreach ($redactorsInEditors as $key => $value) {
          $idRedactor = explode(",",$value->redactor_id);
          $idsRedactors = array_merge($idsRedactors,$idRedactor);
        }
      }
      $redactors = DB::table("admin_users")
                    ->join("admin_role_users","admin_users.id","=","admin_role_users.user_id")
                    ->select("admin_users.id as id","admin_users.name as text")
                    ->where("admin_role_users.role_id","=",2)
                    ->whereNotIn("admin_users.id",$idsRedactors)->paginate(99999);

      return $redactors;
    }
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\EditorHasRedactors';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EditorHasRedactors());

        $grid->column('id', __('Id'));
        $grid->column('editor_id')->display(function($status_id){
          $objUser = config('admin.database.users_model');
          return $objUser::find($status_id)->name;
        });
        $grid->column('redactor_id')->display(function($redactors_id){
          $objUser = config('admin.database.users_model');
          $listRedactors = array();
          $explodeRedactors = explode(",",$redactors_id);
          foreach ($explodeRedactors as $key => $value) {
            array_push($listRedactors,$objUser::find($value)->name);
          }
          return implode(",",$listRedactors);
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
        $show = new Show(EditorHasRedactors::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('editor_id', __('Editor id'));
        $show->field('redactor_id', __('Redactor id'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new EditorHasRedactors());

        $userModel = config('admin.database.users_model');
        $form->select('editor_id',"Editor")->ajax("/admin/api/findeditors")->options(function($id){
          $user = config('admin.database.users_model');
          $getUser = $user::find($id);
          if($getUser){
            return [$getUser->id => $getUser->name];
          }
        });

        $form->multipleSelect('redactor_id',"Redactores")->ajax("/admin/api/findredactors")->options(function($id){
          $user = config('admin.database.users_model');
          $getUsers = $user::find($id);
          if($getUsers){
            $listUsers = array();
            foreach ($getUsers->all() as $key => $value) {
              $listUsers[$value->id] = $value->name;
            }
            return $listUsers;
          }
        });

        $form->saving(function (Form $form) {
          if(count($form->redactor_id) > 0){
            $redactors = array();
            foreach ($form->redactor_id as $key => $value) {
              if(!empty($value)){
                $redactor = EditorHasRedactors::where('redactor_id',"LIKE","%".$value."%")->where("editor_id","!=",$form->editor_id)->first();
                if(!empty($redactor)){
                  $error = new MessageBag([
                     'title'   => 'Redactor ya existe',
                     'message' => 'Ya existe el redactor con otro editor',
                  ]);

                  return back()->with(compact('error'));
                }
                array_push($redactors,$value);
              }
            }
            $strRedactors = implode(",",$redactors);
            if(!empty(request()->route()->parameters)){
              DB::table('editor_has_redactors')
                  ->where('editor_id',$form->editor_id)
                  ->update(['redactor_id' => $strRedactors]);
            }else{
              DB::table('editor_has_redactors')->insert(
                  ['editor_id' => $form->editor_id, 'redactor_id' => $strRedactors]
              );
            }

            return redirect('/admin/auth/editorhasredactors');
          }else{
            $error = new MessageBag([
               'title'   => 'No hay redactores',
               'message' => 'Faltan agregar redactores',
            ]);

            return back()->with(compact('error'));
          }
        });

        return $form;
    }
}
