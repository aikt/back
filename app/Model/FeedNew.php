<?php

namespace App\Model;

use Illuminate\Console\Command;
use App\Console\Commands\DB;
use App\PostalCode;
use App\Copos;
use Aws;

class FeedNew
{
  public $imported = 1;
  public $title;
  public $summary;
  public $content;
  public $image;
  public $canonical;
  public $created_at;
  public $arrayAsentamientos = array();
  public $arrayMunicipiosByState = array();
  public $arrayStates = array();
  public $magnitude = 1;
  public $idPostalCodeAutomatic=0;
  public $isModelFeed = 0;
  public function __construct($data,$parent){
    $this->title = ((!isset($data->title)) ? "" : ((!empty(trim($data->title))) ? trim($data->title) : ""));
    $this->summary = ((!isset($data->description)) ? "" : ((!empty(trim($data->description))) ? addslashes(html_entity_decode(strip_tags(trim($data->description)))) : ""));
    $this->content = ($data->children("content",true) != "") ? addslashes(html_entity_decode(trim($data->children("content",true)))) : "";
    $this->canonical = ((!isset($data->link)) ? "" : ((!empty(trim($data->link))) ? trim($data->link) : ""));
    $this->created_at = ((!isset($data->pubDate)) ? "" : ((!empty(trim($data->pubDate))) ? trim($data->pubDate) : ""));

    if(!empty($data->children("media",true))){
      $this->image = $data->children("media",true)->attributes()->url;
    }else if(isset($data->enclosure)){
      $this->image = $data->enclosure->attributes()->url;
    }else{
      $this->image = "";
    }

    if(!empty($this->title)){
      if(!empty($this->summary)){
        $this->summary = preg_replace('/m{[0-9]+}/',"",$this->summary);
        //if(!empty($this->image)){
          if(!empty($this->canonical)){
            if(!empty($this->created_at) && !is_bool($this->created_at)){
              //$this->getPostalCodeByText(); // TODO: Aqui comienza el script de modelo automatico
              $url = "";
	      $proceed = true;
              $url_copo = "";
              $updated_at = $this->created_at;
              $id_status_news = 2;
              if(!empty($this->image)){
                $valueImage = $this->image;
                $paths = explode("/",$valueImage);
                $nameAndFormat = explode(".",end($paths));
                $name = $nameAndFormat[0];
                $format = end($nameAndFormat);
                $random = rand(100000000000000,999999999999999);
                $yearImage = date("Y");
                $monthImage = date("m");
                $dayImage = date("d");
                $filename="/var/www/laravel/public/uploads/images/".$yearImage."/".$monthImage."/".$dayImage;
                $fileImage = $name."_".$random.".".$format;
                if(!file_exists($filename)){
                  mkdir($filename, 0777,true);
                }
                $imgName = $filename."/".$fileImage;
                $context = stream_context_create(
                    array(
                        "http" => array(
                            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                        )
                    )
                );
            		$getImage = @file_get_contents($valueImage, false, $context);
            		if($getImage !== false){
            		  file_put_contents($imgName, $getImage);
            		}else{
            		  $proceed = false;
            		}
                $this->image = "images/".$yearImage."/".$monthImage."/".$dayImage."/".$fileImage;

                $arrayList = array(
                  env("APP_URL")."uploads/".$this->image
                );

                $resultList = $this->getImageFromAWSByUriImageExternalList($arrayList);

              }

              // if($this->idPostalCodeAutomatic > 0){
              //   $this->isModelFeed = 1;
              //   $id_status_news = 1;
              //   $postalCodeToSave = PostalCode::where("id","=",$this->idPostalCodeAutomatic)->first();
              //   $queryCopo = \DB::table('copos_postalcodes')
              //            ->where("postal_code_id","=",$postalCodeToSave->d_codigo)
              //            ->get();
              //   if($queryCopo){
              //     if(is_array($queryCopo->all())){
              //       if(count($queryCopo->all())){
              //         $copoModel = Copos::find($queryCopo->all()[0]->copo_id);
              //         $url = "/".$this->slugify($postalCodeToSave->d_estado)."/".$this->slugify($postalCodeToSave->d_municipio)."/".$this->slugify($copoModel->title)."/".$postalCodeToSave->d_codigo."/".$this->slugify($this->title);
              //         $url_copo = $this->unwantedArray($url);
              //       }else{
              //         $url = "/".$this->slugify($postalCodeToSave->d_estado)."/".$this->slugify($postalCodeToSave->d_municipio)."/".$postalCodeToSave->d_codigo."/".$this->slugify($this->title);
              //       }
              //     }
              //   }
              //   $url = $this->unwantedArray($url);
              //
              //   date_default_timezone_set('America/Mexico_City');
              //   $filename = "sitemap-articles-".date("Y")."-".date("m").".xml";
              //   if(file_exists("sitemap/articles/".$filename)){
              //     $file = realpath(__DIR__ . '/../../../public/sitemap/articles/'.$filename);
              //
              //     $xml = simplexml_load_file($file);
              //
              //     $sitemaps = $xml;
              //     $sitemap = $sitemaps->addChild('url');
              //     $sitemap->addChild("loc",$url);
              //     $sitemap->addChild("lastmod",date('c',time()));
              //     $sitemap->addChild("changefreq","hourly");
              //     $sitemap->addChild("priority",0.9);
              //
              //     $xml->asXML($file);
              //   }
              // }else{
              //   $this->idPostalCodeAutomatic = null;
              //   $url = null;
              //   $url_copo = null;
              //   $updated_at = null;
              // }
              // TODO: Cuando hagamos el modelo automatico , quitar esas 4 lineas de abajo y descomentar las de arriba
              $this->idPostalCodeAutomatic = null;
              $url = null;
              $url_copo = null;
              $updated_at = null;

      	      $fecha = \DateTime::createFromFormat('*, d M Y H:i:s *????', $this->created_at) ?: \DateTime::createFromFormat('*, d M Y H:i:s *', $this->created_at) ?: \DateTime::createFromFormat('y-m-d H:i:s', $this->created_at);
      	      if(!is_bool($fecha)){
                $this->created_at = $fecha->format('Y-m-d H:i:s');
		            $check = \DB::select("select title from news where url_canonical = '$this->canonical'");
                if(empty($check)){
                  try{
                   $idNota = \DB::table('news')->insertGetId(
                      [
                        "title" => $this->title,
                        "id_cp" => $this->idPostalCodeAutomatic,
                        "summary" => $this->summary,
                        "content" => $this->content,
                        "coordinate_x" => 0,
                        "coordinate_y" => 0,
                        "url" => $url,
                        "url_copo" => $url_copo,
                        "image" => $this->image,
                        "seo_title" => $this->title,
                        "seo_keywords" => "",
                        "seo_description" => $this->summary,
                        "id_status_news" => $id_status_news,
                        "imported" => 1,
                        "id_author" => $parent->id_author,
                        "title_normalizado" => $this->slugify($this->title),
                        "url_canonical" => $this->canonical,
                        "created_at" => $this->created_at,
                        "updated_at" => $updated_at,
                        "model_feed" => $this->isModelFeed
                      ]
                    );
                  } catch(\Illuminate\Database\QueryException $ex){
                    dd($ex->getMessage());
                  }
                }
              }
            }
          }
        //}
      }
    }
  }

  public function getImageFromAWSByUriImageExternalList($uriImageExternalList){
    $s3 = new Aws\S3\S3Client([
      'region'  => 'us-east-2',
      'version' => 'latest',
      'credentials' => [
        'key'    => "AKIAIQS7HMCD7GJJMLNA",
        'secret' => "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",
      ]
    ]);
    if (!file_exists('/var/www/laravel/public/uploads/tmp')) {
      mkdir('/var/www/laravel/public/uploads/tmp', 0777, true);
    }

    $arrayS3 = array();

    foreach ($uriImageExternalList as $key => $file) {
      $getImage = file_get_contents($file);
      if($getImage !== FALSE){
        preg_match('/.*\/.*\/(.*)/', $file, $matches, PREG_OFFSET_CAPTURE);
        if(isset($matches[1])){
		$nameImage = $matches[1][0];
		echo "HELOO DARKENESSS:".$nameImage;
          $img = '/var/www/laravel/public/uploads/tmp/'.$nameImage;
          file_put_contents($img, $getImage);
          $size = getimagesize($img);

	  if(!empty($size)){
	    $yearImage = date("Y");
            $monthImage = date("m");
            $dayImage = date("d");
            $result = $s3->putObject([
              'Bucket' => env("AWS_BUCKET"),
              'Key'    => "images/".$yearImage."/".$monthImage."/".$dayImage."/".$nameImage,
              'SourceFile' => realpath($img)
            ]);

            $urlImageFromAWS = $result->get("ObjectURL");
            if(!empty($urlImageFromAWS)) {
              unlink($img);
              array_push($arrayS3,$urlImageFromAWS);
            }
          }else{
            unlink($img);
          }
        }
      }
    }

    return $arrayS3;

  }
  public function getPostalCodeByText(){
    if(!$this->idPostalCodeAutomatic && $this->magnitude <= 3){
      if(empty($this->arrayStates)){
        $states = \DB::select("SELECT id,d_estado FROM postal_codes GROUP BY d_estado");
        $this->arrayStates = $states;
      }
      $titleToSplit = "";
      $indicator = "titulo";
      if($this->magnitude == 1){
        $titleToSplit = $this->title;
      }else if($this->magnitude == 2){
        $titleToSplit = $this->summary;
        $indicator = "sumario";
      }else if($this->magnitude == 3){
        $titleToSplit = $this->content;
        $indicator = "contenido";
      }

      $wordsText = preg_split("/\s+/", $titleToSplit);
      if(count($wordsText) > 1){
        $foundStateTitle = false;
        $this->idPostalCodeAutomatic = 0;
        foreach ($wordsText as $keyTitle => $valueTitle) {
          if(!$foundStateTitle){
            $titleS = $this->slugify($valueTitle);
            foreach ($this->arrayStates as $keyStates => $valueStates) {
              if(stristr($titleS,$this->slugify($valueStates->d_estado)) !== FALSE){
                if(!array_key_exists($valueStates->d_estado,$this->arrayMunicipiosByState)){
                  $municipios = \DB::select("SELECT id,d_municipio FROM postal_codes WHERE d_estado='$valueStates->d_estado' GROUP BY d_municipio");
                  $this->arrayMunicipiosByState[$valueStates->d_estado] = $municipios;
                }
                $this->idPostalCodeAutomatic = $valueStates->id;
                $foundStateTitle = true;
                $foundMunicipioTitle = false;
                foreach ($this->arrayMunicipiosByState[$valueStates->d_estado] as $keyMunicipios => $valueMunicipios) {
                  if(!$foundMunicipioTitle){
                    foreach ($wordsText as $keyTitleToMunicipios => $valueTitleToMunicipios) {
                      $titleMunicipiosS = $this->slugify($valueTitleToMunicipios);
                      if(stristr($titleMunicipiosS,$this->slugify($valueMunicipios->d_municipio)) !== FALSE){
                        $this->idPostalCodeAutomatic = $valueMunicipios->id;
                        $foundMunicipioTitle = true;
                        if($this->magnitude == 3){
                          if(!array_key_exists($valueMunicipios->d_municipio,$this->arrayAsentamientos)){
                            $asentamientos = \DB::select("SELECT id,d_asenta FROM postal_codes WHERE d_municipio='$valueMunicipios->d_municipio' GROUP BY d_asenta");
                            $this->arrayAsentamientos[$valueMunicipios->d_municipio] = $asentamientos;
                          }
                          $foundAsenta = false;
                          foreach ($this->arrayAsentamientos[$valueMunicipios->d_municipio] as $keyAsenta => $valueAsenta) {
                            if(!$foundAsenta){
                              foreach ($wordsText as $keyTitleAsenta => $valueTitleAsenta) {
                                $asentamientoS = $this->slugify($valueTitleAsenta);
                                if(stristr($asentamientoS,$this->slugify($valueAsenta->d_asenta)) !== FALSE){
                                  $this->idPostalCodeAutomatic = $valueAsenta->id;
                                  $foundAsenta = true;
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
      if(!$this->idPostalCodeAutomatic){
        $this->magnitude++;
        $this->getPostalCodeByText();
      }
    }
  }
  public function slugify($text)
  {
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }
  public function unwantedArray($str){
   $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                          'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                          'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                          'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                          'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
   return strtr( $str, $unwanted_array );
  }
}
