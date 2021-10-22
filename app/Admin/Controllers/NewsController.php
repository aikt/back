<?php

namespace App\Admin\Controllers;

use App\News;
use App\PostalCode;
use App\NewsStatus;
use App\Copos;
use App\Author;
use App\Tag;
use App\NewsPosition;
use App\NewsSections;
use App\AdminUsers;
use Auth;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;

class NewsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Noticia';

    public function findNews(Request $request){
      $title = $request->input('q');

      if(!empty($title)){
        $objNews = new News();
        return $objNews->where("title","like","%$title%")->paginate(null,['id as id',"title as text"]);
      }
    }

    public function slideAutoNews(Request $request){
      $news = \DB::select(
        "SELECT
          nw.id as idNoticia,
          nw.image as imagen,
          nw.title as titulo,
          nw.summary as sumario,
          nw.content as contenido,
          nw.url as url,
          nwa.id as idNoticiaAuto,
          nwa.estado as estado,
          nwa.municipio as municipio,
          nwa.asentamiento as asentamiento,
          nwa.puntuacion as puntuacion,
          cp.d_codigo as codigoPostal
         FROM news_auto as nwa
         INNER JOIN news as nw ON nw.id = nwa.id_news
         INNER JOIN postal_codes as cp ON cp.id = nw.id_cp ORDER BY nw.created_at DESC LIMIT 100;");

      foreach ($news as $key => $value) {
        $listWordRemark = array();
        if(!empty($value->estado)) array_push($listWordRemark, $value->estado);
        if(!empty($value->municipio)) array_push($listWordRemark, $value->municipio);
        if(!empty($value->asentamiento)) array_push($listWordRemark, $value->asentamiento);
        if(!empty($value->titulo)){
          $value->titulo = $this->getWordsRemark($listWordRemark,$value->titulo);
        }
        if(!empty($value->summary)){
          $value->summary = $this->getWordsRemark($listWordRemark,$value->summary);
        }
        if(!empty($value->contenido)){
          $value->contenido = $this->getWordsRemark($listWordRemark,$value->contenido);
        }
      }
      $states = \DB::select(
        "SELECT
          cp.d_estado
        FROM postal_codes as cp
        GROUP BY cp.d_estado
      ");

      return  response()->view(
          'slideautonews',
          array(
            "news" => $news,
            "states" => $states
          )
      );
    }

    public function swipeLeftNews(){
      if(!empty($_GET)){
        if(isset($_GET["idAuto"]) && isset($_GET["idNews"])){
          \DB::table("news_auto")->where("id","=",$_GET["idAuto"])->delete();
          \DB::update("UPDATE news SET id_status_news = 2 WHERE id = ".$_GET["idNews"]);
        }
      }
    }

    public function swipeRightNews(){
      if(!empty($_GET)){
        if(isset($_GET["idAuto"]) && isset($_GET["idNews"]) && isset($_GET["url"])){
          $url = $_GET["url"];
          date_default_timezone_set('America/Mexico_City');
          $filename = "sitemap-articles-".date("Y")."-".date("m").".xml";
          if(file_exists("sitemap/articles/".$filename)){
            $file = realpath(__DIR__ . '/../../../public/sitemap/articles/'.$filename);

            $xml = simplexml_load_file($file);

            $sitemaps = $xml;
            $sitemap = $sitemaps->addChild('url');
            $sitemap->addChild("loc","https://codigopostal.com".$url);
            $sitemap->addChild("lastmod",date('c',time()));
            $sitemap->addChild("changefreq","hourly");
            $sitemap->addChild("priority",0.9);

            $xml->asXML($file);
          }
          \DB::table("news_auto")->where("id","=",$_GET["idAuto"])->delete();
          \DB::update("UPDATE news SET id_status_news = 5 WHERE id = ".$_GET["idNews"]);
        }
      }
    }

    public function findNewsToSlide(){
      if(!empty($_GET)){
        if(isset($_GET["state"])){
          $state = $_GET["state"];
          $news = \DB::select(
            "SELECT
              nw.id as idNoticia,
              nw.image as imagen,
              nw.title as titulo,
              nw.summary as sumario,
              nw.content as contenido,
              nw.url as url,
              nwa.id as idNoticiaAuto,
              nwa.estado as estado,
              nwa.municipio as municipio,
              nwa.asentamiento as asentamiento,
              nwa.puntuacion as puntuacion,
              cp.d_codigo as codigoPostal
             FROM news_auto as nwa
             INNER JOIN news as nw ON nw.id = nwa.id_news
             INNER JOIN postal_codes as cp ON cp.id = nw.id_cp
             WHERE d_estado = '$state'
             ORDER BY nw.created_at DESC LIMIT 50;
          ");

          if(count($news) > 0){
            return  response()->view(
                'viewnewsslide',
                array(
                  "news" => $news
                )
            );
          }else{
            return "No se encontraron noticias para : ".$state;
          }
        }
      }
    }

    public function getWordsRemark($listWords,$text){
      foreach ($listWords as $key => $value) {
        $text = str_replace($value,"<txt style='background:yellow;'>".$value."</txt>",$text);
      }
      return $text;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new News());

        $grid->filter(function($filter){
            // Add a column filter
            $filter->like('title', 'titulo');
            $filter->like('summary', 'sumario');
            $filter->like('id_status_news', 'status noticia');
            $filter->like('id_author', 'ID Author');
            $filter->like('id_editor', 'ID Editor');
            $filter->like('url_canonical', 'url canonical');
            $filter->equal('id_state', 'ID de estado');
            $filter->like('model_feed',"auto nota");
        });
        $grid->paginate(100);
        date_default_timezone_set('America/Mexico_City');
        // $created_at = $grid->created_at();
        // $updated_at = $grid->updated_at();
        $grid->model()->orderBy("created_at","desc");
        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('summary', __('Summary'));
        $grid->column('url', __('Url'));
        $grid->column('id_status_news')->display(function($status_id){
          $status = NewsStatus::find($status_id);
          if(isset($status->type)){
            return $status->type;
          }else{
            return "";
          }
        });

        $grid->column('id_author')->display(function($id_author){
          $autor =Author::find($id_author);
          if(isset($autor->name)){
            return $autor->name;
          }else{
            return "";
          }
        });

        $grid->column('id_editor')->display(function($id_editor){
          if(!empty($id_editor)){
            $editor = \DB::select("SELECT * FROM admin_users WHERE id = $id_editor");
            if(isset($editor[0])){
              return $editor[0]->username;
            }else{
              return "";
            }
          }else return "";

        });
        // $grid->column('id_news_section')->display(function($id_news_section){
        //   $idSection =NewsSections::find($id_news_section);
        //   if(isset($idSection->category)){
        //     return $idSection->category;
        //   }else{
        //     return "";
        //   }
        // });

        $grid->column('coordinate_x', __('Coordinate x'));
        $grid->column('coordinate_y', __('Coordinate y'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('model_feed', __('auto nota'));
        $grid->column('id_state',__('Estado'))->display(function($id_state){
          $state =PostalCode::find($id_state);
          if(isset($state->d_estado)){
            return $state->d_estado;
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
        $show = new Show(News::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('summary', __('Summary'));
        $show->field('content', __('Content'));
        $show->field('url', __('Url'));
        $show->field('coordinate_x', __('Coordinate x'));
        $show->field('coordinate_y', __('Coordinate y'));
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
        $form = new Form(new News());

        $classNewsStatus = new NewsStatus();

        $form->tab("Datos noticia",function($form){
          $isEditorMaster = false;
          $isEditorJunior = false;
          $imported = false;
          // TODO: Logica de master y junior
          if(Auth::guard('admin')->user()->roles->where("slug","editor")->count() > 0 || Auth::guard('admin')->user()->roles->where("slug","administrator")->count() > 0){
            $isEditorMaster = true;
            $isEditorJunior = false;
          }else if(Auth::guard('admin')->user()->roles->where("slug","redactor")->count() > 0){
            $isEditorJunior = true;
            $isEditorMaster = false;
          }
          // TODO: Logica de si la nota es importada
          if(!empty(request()->route()->parameters)){
            $id = request()->route()->parameters["news"];
            $model = $form->model()->find($id);
            $imported = $model->imported;
          }
          // TODO: Comienza a desplegar campos
          $form->hidden("id");
          $form->text('title', __('Titulo'))->required();
          $form->text('summary', __('Resumen'));
          $form->ckeditor('content', __('Contenido'));
          $form->hidden('url', __('Url'));
          if($imported){
            $form->image('image',__('Imagen'))->disable();
          }else{
            $form->image('image', __('Imagen'))->uniqueName();
          }

          $form->multipleImage('pictures', __('Imágenes Infografías'))->sortable();

          $form->select('id_cp',"Codigo Postal")->ajax("/admin/api/postalcodes")->options(function($id){
            $id_cp = PostalCode::find($id);

            if($id_cp){
              return [$id_cp->id => $id_cp->d_codigo];
            }
          });
          $form->select('id_municipality',"Municipio")->ajax("/admin/api/findmunicipality")->options(function($id){
            $id_cp = PostalCode::find($id);

            if($id_cp){
              return [$id_cp->id => $id_cp->d_municipio];
            }
          });
          $form->select('id_state',"Estado")->ajax("/admin/api/findstate")->options(function($id){
            $id_cp = PostalCode::find($id);

            if($id_cp){
              return [$id_cp->id => $id_cp->d_estado];
            }
          });
          $form->select('id_copo_id',__("Copo"))->options(Copos::all()->pluck('title', 'id'));
          $authorModel = new Author();
          $classNewsStatus = new NewsStatus();
          $newsSections = new NewsSections();
          if($isEditorMaster && !$isEditorJunior){
            $form->select('id_author',__("Author"))->options($authorModel::all()->pluck('name', 'id'))->required();
            $form->select('id_status_news',"Status noticia")->options($classNewsStatus::all()->pluck("type","id"));
          }else if(!$isEditorMaster && $isEditorJunior){
            $form->select('id_status_news',"Status noticia")->options([2 => "Borrador"])->default(2);
          }

          $form->select('id_principal_editor', __('Editor principal'))->options(AdminUsers::all()->pluck('name', 'id'));

          // $form->datetime('news_programed',__("Fecha programada"));
          $form->select('id_news_section', __('Sección'))->options(NewsSections::all()->pluck('category', 'id'));
          $form->select('testab', __('Test AB'))->options([1 => 'Desactivado', 2 => 'Activado'])->default(1);

          $form->multipleSelect('tags', __('Tags'))->options(Tag::all()->pluck('name', 'id'));
          $form->select('id_position', __('Posición'))->options(NewsPosition::all()->pluck('name', 'id'));
          //$form->latlong("coordinate_x","coordinate_y","Coordenada")->height(500)->default(100,100);
          $form->hidden("seo_title");
          $form->hidden("id_editor");
          $form->hidden("url");
          $form->hidden("url_copo");
          $form->hidden("title_normalizado");

          date_default_timezone_set('America/Mexico_City');
          $curdate = date("Y-m-d H:i:s");
          $form->created_at = $curdate;
          $form->updated_at = $curdate;

          $form->hidden("imported");
          $form->text("seo_description",__("Seo descripción"))->required();
          $form->text("seo_keywords",__("Seo keywords"))->required();

          $form->multipleSelect('newshasnews',"Notas relacionadas")->ajax("/admin/api/findnews")->options(function($ids){
            if(!empty($ids)){
              return News::findMany($ids)->pluck("title","id");
            }
          });

        });

        $form->saving(function (Form $form) {
            $checkNews = new News();
            $resultNews = $checkNews->where("url","like","%".$this->slugify($form->title)."%")->where("id","!=",$form->id)->get()->all();
            if(count($resultNews) > 0){
              $error = new MessageBag([
                  'title'   => 'Noticia ya existe',
                  'message' => 'Ya existe una noticia con el mismo titulo: '.$form->title
              ]);

              return back()->with(compact('error'));
            }
            $form->seo_title = $form->title;
            $form->title_normalizado = $this->slugify($form->title);
            $imported = 0;
            if(!empty(request()->route()->parameters)){
              $id = request()->route()->parameters["news"];
              $model = $form->model()->find($id);
              $imported = $model->imported;
              if($imported) $imported = 1;
            }
            $form->imported = $imported;
            if(empty($form->id_editor)){
              $form->id_editor = Auth::guard('admin')->user()->id;
            }
            if($form->id_status_news == 1 || $form->id_status_news == 7){
              //if($form->url == ""){
                $url = "";
                if($form->id_cp != ""){
                  $postalCodeToSave = PostalCode::where("id","=",$form->id_cp)->first();
                  $queryCopo = DB::table('copos_postalcodes')
                           ->where("postal_code_id","=",$postalCodeToSave->d_codigo)
                           ->get();
                  if($queryCopo){
                    if(is_array($queryCopo->all())){
                      if(count($queryCopo->all())){
                        $copoModel = Copos::find($queryCopo->all()[0]->copo_id);
                        $url = "/".$this->slugify($postalCodeToSave->d_estado)."/".$this->slugify($postalCodeToSave->d_municipio)."/".$this->slugify($copoModel->title)."/".$postalCodeToSave->d_codigo."/".$this->slugify($form->title);
                        $form->url_copo = $this->unwantedArray($url);
                      }else{
                        $url = "/".$this->slugify($postalCodeToSave->d_estado)."/".$this->slugify($postalCodeToSave->d_municipio)."/".$postalCodeToSave->d_codigo."/".$this->slugify($form->title);
                      }
                    }
                  }
                }
                if($url == ""){
                  if($form->id_municipality != ""){
                    $postalCodeMunicipality = PostalCode::where("id","=",$form->id_municipality)->first();
                    $url = "/".$this->slugify($postalCodeMunicipality->d_estado)."/".$this->slugify($postalCodeMunicipality->d_municipio)."/".$postalCodeMunicipality->d_codigo."/".$this->slugify($form->title);
                    $form->id_cp = $form->id_municipality;
                  }
                }
                if($url == ""){
                  if($form->id_state != ""){
                    $postalCodeMunicipality = PostalCode::where("id","=",$form->id_state)->first();
                    $url = "/".$this->slugify($postalCodeMunicipality->d_estado)."/".$this->slugify($postalCodeMunicipality->d_municipio)."/".$postalCodeMunicipality->d_codigo."/".$this->slugify($form->title);
                    $form->id_cp = $form->id_state;
                  }
                }

                // Artisan::call('news:clearcache', ['url' => $this->unwantedArray($url)]);
                $file = 'urls.txt';
                $file_stream = fopen($file, "a") or die("Unable to open file!");
                fwrite($file_stream, $this->unwantedArray($url) . "\n");
                fclose($file_stream);

                $form->url = $this->unwantedArray($url);
                date_default_timezone_set('America/Mexico_City');
                $filename = "sitemap-articles-".date("Y")."-".date("m").".xml";
                if(file_exists("sitemap/articles/".$filename)){
                  $file = realpath(__DIR__ . '/../../../public/sitemap/articles/'.$filename);

                  $xml = simplexml_load_file($file);

                  $sitemaps = $xml;
                  $sitemap = $sitemaps->addChild('url');
                  $sitemap->addChild("loc","https://codigopostal.com".$url);
                  $sitemap->addChild("lastmod",date('c',time()));
                  $sitemap->addChild("changefreq","hourly");
                  $sitemap->addChild("priority",0.9);

                  $xml->asXML($file);
                }
              //}
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
    function unwantedArray($str){
     $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
     return strtr( $str, $unwanted_array );
    }

    public function exportNews(Request $request){
      $cp = $request->input('ids');
      $news = DB::select("SELECT * FROM news WHERE id IN ($cp)");

      $zip = new \ZipArchive;

      $fileName = 'notas.zip';

      if(file_exists(public_path($fileName))){
        unlink(public_path($fileName));
      }

      $res = $zip->open(public_path($fileName), \ZipArchive::CREATE) === TRUE;

      if ($res === TRUE)
      {
          foreach ($news as $key => $value) {
            $strNota = "TITULO: ".$value->title."\n\n";

            $strNota.= "SUMARIO: ".$value->summary."\n\n";
            if(!empty($value->url_copo)){
              $strNota.="URL: ".$value->url_copo."\n\n";
            }else if(!empty($value->url)){
              $strNota.="URL: ".$value->url."\n\n";
            }
            preg_match('(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)',$value->image,$matches,PREG_OFFSET_CAPTURE);

            if(!empty($matches)){ // TODO Si existe https o http
              // $ch = curl_init();
              // // set url
              // curl_setopt($ch, CURLOPT_URL, $value->image);
              // //return the transfer as a string
              // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              // curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
              //
              // $output = curl_exec($ch);
              //
              // curl_close($ch);
              //
              // print_r($output);

              $strNota.="URL IMAGEN: ".$value->image."\n\n";
              //$zip->addFromString($value->id.'.jpg', $output);
            }else { // no existe http
               $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
               $url = $protocol.request()->getHttpHost()."/uploads/".$value->image;

               $strNota.="URL IMAGEN: ".$url."\n\n";
              // $content = file_get_contents($url);
              // $zip->addFromString($value->id.'.png', $content);
            }
            $strNota.="CONTENIDO: ".$value->content."\n\n";

            $zip->addFromString($value->id.'.txt', $strNota);
          }

          $zip->close();
      }
      return response()->download(public_path($fileName));
    }
}
