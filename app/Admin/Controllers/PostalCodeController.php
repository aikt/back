<?php

namespace App\Admin\Controllers;

use App\PostalCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PostalCodeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Códigos postales';


    public function findPostalCodes(Request $request){
      $cp = $request->input('q');

      if(!empty($cp)){
        if(strlen($cp) >= 3){
          $objPostalCode = new PostalCode();
          return $objPostalCode->where("d_codigo","like","%$cp%")->paginate(null,['id as id',"d_asenta as text"]);
        }

      }else{
        return ["Información no encontrada"];
      }
    }

    public function findMunicipality(Request $request){
      $cp = $request->input('q');

      if(!empty($cp)){
        if(strlen($cp) >= 3){
          $objPostalCode = new PostalCode();
          $data = array();
          $queryPostalCode = DB::select("select postal_codes.id as id, concat(postal_codes.d_municipio,' - estado: ',postal_codes.d_estado) as text from postal_codes where postal_codes.d_municipio like '%".$cp."%' group by postal_codes.d_estado limit 99999");
          $data["current_page"] = 1;
          $data["data"] = $queryPostalCode;
          $data["first_page_url"] = "http://127.0.0.1:8000/admin/api/postalcodescopos?page=1";
          $data["from"] = 1;
          $data["last_page"] = 1;
          $data["last_page_url"] = "http://127.0.0.1:8000/admin/api/postalcodescopos?page=1";
          $data["next_page_url"] = null;
          $data["path"] = "http://127.0.0.1:8000/admin/api/postalcodescopos";
          $data["per_page"] = 99999;
          $data["prev_page_url"] = null;
          $data["to"] = 5;
          $data["total"] = 5;
          return $data;

        }

      }else{
        return ["Información no encontrada"];
      }
    }

    public function findState(Request $request){
      $cp = $request->input('q');

      if(!empty($cp)){
        if(strlen($cp) >= 3){
          $objPostalCode = new PostalCode();
          $data = array();
          $queryPostalCode = DB::select("select postal_codes.id as id, postal_codes.d_estado as text from postal_codes where postal_codes.d_estado like '%".$cp."%' group by postal_codes.d_estado limit 99999");
          $data["current_page"] = 1;
          $data["data"] = $queryPostalCode;
          $data["first_page_url"] = "http://127.0.0.1:8000/admin/api/postalcodescopos?page=1";
          $data["from"] = 1;
          $data["last_page"] = 1;
          $data["last_page_url"] = "http://127.0.0.1:8000/admin/api/postalcodescopos?page=1";
          $data["next_page_url"] = null;
          $data["path"] = "http://127.0.0.1:8000/admin/api/postalcodescopos";
          $data["per_page"] = 99999;
          $data["prev_page_url"] = null;
          $data["to"] = 5;
          $data["total"] = 5;
          return $data;

        }

      }else{
        return ["Información no encontrada"];
      }
    }

    public function findPostalCodesToCopos(Request $request){
      $cp = $request->input('q');

      if(!empty($cp)){
        if(strlen($cp) >= 3){
          $objPostalCode = new PostalCode();
          return $queryPostalCode = DB::table('postal_codes')
                                    ->join("copos_postalcodes","postal_codes.d_codigo","!=","copos_postalcodes.postal_code_id","left outer")
                                    ->select("postal_codes.d_codigo as id","postal_codes.d_asenta as text")
                                    ->where("postal_codes.d_codigo","like","%$cp%")->paginate(99999);
        }

      }else{
        return ["Información no encontrada"];
      }
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PostalCode());

        $grid->column('d_codigo', __('D codigo'));
        $grid->column('d_asenta', __('D asenta'));
        $grid->column('d_tipo_asenta', __('D tipo asenta'));
        $grid->column('d_municipio', __('D municipio'));
        $grid->column('d_estado', __('D estado'));
        $grid->column('d_ciudad', __('D ciudad'));
        $grid->column('d_CP', __('D CP'));
        $grid->column('c_estado', __('C estado'));
        $grid->column('c_oficina', __('C oficina'));
        $grid->column('c_CP', __('C CP'));
        $grid->column('c_tipo_asenta', __('C tipo asenta'));
        $grid->column('c_municipio', __('C municipio'));
        $grid->column('id_asenta_cpcons', __('Id asenta cpcons'));
        $grid->column('d_zona', __('D zona'));
        $grid->column('c_cve_ciudad', __('C cve ciudad'));

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
        $show = new Show(PostalCode::findOrFail($id));

        $show->field('d_codigo', __('D codigo'));
        $show->field('d_asenta', __('D asenta'));
        $show->field('d_tipo_asenta', __('D tipo asenta'));
        $show->field('d_municipio', __('D municipio'));
        $show->field('d_estado', __('D estado'));
        $show->field('d_ciudad', __('D ciudad'));
        $show->field('d_CP', __('D CP'));
        $show->field('c_estado', __('C estado'));
        $show->field('c_oficina', __('C oficina'));
        $show->field('c_CP', __('C CP'));
        $show->field('c_tipo_asenta', __('C tipo asenta'));
        $show->field('c_municipio', __('C municipio'));
        $show->field('id_asenta_cpcons', __('Id asenta cpcons'));
        $show->field('d_zona', __('D zona'));
        $show->field('c_cve_ciudad', __('C cve ciudad'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new PostalCode());

        $form->number('d_codigo', __('D codigo'));
        $form->text('d_asenta', __('D asenta'));
        $form->text('d_tipo_asenta', __('D tipo asenta'));
        $form->text('d_municipio', __('D municipio'));
        $form->text('d_estado', __('D estado'));
        $form->text('d_ciudad', __('D ciudad'));
        $form->number('d_CP', __('D CP'));
        $form->number('c_estado', __('C estado'));
        $form->number('c_oficina', __('C oficina'));
        $form->number('c_CP', __('C CP'));
        $form->number('c_tipo_asenta', __('C tipo asenta'));
        $form->number('c_municipio', __('C municipio'));
        $form->number('id_asenta_cpcons', __('Id asenta cpcons'));
        $form->text('d_zona', __('D zona'));
        $form->number('c_cve_ciudad', __('C cve ciudad'));

        return $form;
    }
}
