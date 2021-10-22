<?php

namespace App\Model;

use Illuminate\Console\Command;
use App\Console\Commands\DB;
use App\PostalCode;
use App\Copos;

class NewsAuto
{
  public $imported = 1;
  public $title;
  public $summary;
  public $content;
  public $id_author;
  public $image;
  public $canonical;
  public $created_at;
  public $arrayAsentamientos = array();
  public $arrayMunicipiosByState = array();
  public $arrayStates = array();
  public $magnitude = 1;
  public $idPostalCodeAutomatic=0;
  public $isModelFeed = 0;
  public $score = 0;
  public $asentaFound = "";
  public $municipality = "";
  public $asenta = "";
  public $asentaMunicipality = "";
  public $states = "";
  public $state = "";
  public $listScore = array();
  public function __construct($data,$arrayStates){
    $this->arrayStates = $arrayStates;
    $this->title = ((!isset($data->title)) ? "" : ((!empty(trim($data->title))) ? trim($data->title) : ""));
    $this->summary = ((!isset($data->summary)) ? "" : ((!empty(trim($data->summary))) ? addslashes(html_entity_decode(strip_tags(trim($data->summary)))) : ""));
    $this->content = ((!isset($data->content)) ? "" : ((!empty(trim($data->content))) ? trim($data->content) : ""));
    $this->id_author = ((!isset($data->id_author)) ? "" : ((!empty(trim($data->id_author))) ? trim($data->id_author) : ""));

    $this->listScore["states"] = array();
    $this->listScore["municipalities"] = array();
    $this->listScore["asentas"] =  array();

    if(!empty($this->title) && strpos($this->title,"VIDEO") === false){
      if(!empty($this->summary)){
        $this->summary = preg_replace('/m{[0-9]+}/',"",$this->summary);
        $this->getPostalCodeByText(); // TODO: Aqui comienza el script de modelo automatico
        $url = "";
        $url_copo = "";
        $id_status_news = 2;
	if($this->idPostalCodeAutomatic > 0){
          if($this->score >= 4){
            $id_status_news = 1;
            $postalCodeToSave = PostalCode::where("id","=",$this->idPostalCodeAutomatic)->first();
            $queryCopo = \DB::table('copos_postalcodes')
                     ->where("postal_code_id","=",$postalCodeToSave->d_codigo)
                     ->get();
            if($queryCopo){
              if(is_array($queryCopo->all())){
                if(count($queryCopo->all())){
                  $copoModel = Copos::find($queryCopo->all()[0]->copo_id);
                  $url = "/".$this->slugify($postalCodeToSave->d_estado)."/".$this->slugify($postalCodeToSave->d_municipio)."/".$this->slugify($copoModel->title)."/".$postalCodeToSave->d_codigo."/".$this->slugify($this->title);
                  $url_copo = $this->unwantedArray($url);
                }else{
                  $url = "/".$this->slugify($postalCodeToSave->d_estado)."/".$this->slugify($postalCodeToSave->d_municipio)."/".$postalCodeToSave->d_codigo."/".$this->slugify($this->title);
                }
              }
            }
            $url = $this->unwantedArray($url);

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
            $this->updated_at = date('Y-m-d h:i:s');
            $check = \DB::select("select id from news where id = $data->id");
            if(!empty($check)){
              $editor = \DB::select("select id from admin_users where username = 'newbotleon'");
              try{
                \DB::table('news')
                ->where("id",$check[0]->id)
                ->update(
                  [
                    "id_cp" => $this->idPostalCodeAutomatic,
                    "url" => $url,
                    "url_copo" => $url_copo,
                    "id_status_news" => $id_status_news,
                    "id_editor" => $editor[0]->id,
                    "updated_at" => $this->updated_at
                  ]
                );
              } catch(\Illuminate\Database\QueryException $ex){
                dd($ex->getMessage());
              }
            }
          }else if($this->score <= 3){
            $postalCodeToSave = PostalCode::where("id","=",$this->idPostalCodeAutomatic)->first();
            $queryCopo = \DB::table('copos_postalcodes')
                     ->where("postal_code_id","=",$postalCodeToSave->d_codigo)
                     ->get();
            if($queryCopo){
              if(is_array($queryCopo->all())){
                if(count($queryCopo->all())){
                  $copoModel = Copos::find($queryCopo->all()[0]->copo_id);
                  $url = "/".$this->slugify($postalCodeToSave->d_estado)."/".$this->slugify($postalCodeToSave->d_municipio)."/".$this->slugify($copoModel->title)."/".$postalCodeToSave->d_codigo."/".$this->slugify($this->title);
                  $url_copo = $this->unwantedArray($url);
                }else{
                  $url = "/".$this->slugify($postalCodeToSave->d_estado)."/".$this->slugify($postalCodeToSave->d_municipio)."/".$postalCodeToSave->d_codigo."/".$this->slugify($this->title);
                }
              }
            }
            $url = $this->unwantedArray($url);
            $check = \DB::select("select id from news where id = $data->id");
            if(!empty($check)){
              $editor = \DB::select("select id from admin_users where username = 'newbotleon'");

              $this->updated_at = date('Y-m-d h:i:s');
              try{
                \DB::table('news')
                ->where("id",$check[0]->id)
                ->update(
                  [
                    "id_cp" => $this->idPostalCodeAutomatic,
                    "url" => $url,
                    "url_copo" => $url_copo,
                    "id_status_news" => 3,
                    "id_editor" => $editor[0]->id,
                    "updated_at" => $this->updated_at
                  ]
                );

                \DB::table('news_auto')
                ->insert(
                    [
                      "id_news" => $check[0]->id,
                      "estado" => $this->states,
                      "municipio" => $this->municipality,
                      "asentamiento" => $this->state,
                      "puntuacion" => $this->score
                    ]
                );
              } catch(\Illuminate\Database\QueryException $ex){
                dd($ex->getMessage());
              }
            }
          }
        }
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
  public function getPostalCodeByText(){
    $titleToSplit = $this->title."-".$this->summary."-".$this->content;

    if(strlen($titleToSplit) > 1){
      $this->idPostalCodeAutomatic = 0;

      $listStates = array();
      $listMunicipalities = array();
      $listAsentas = array();
      foreach ($this->arrayStates as $keyStates => $valueStates) {
        $explode = explode("+",$valueStates->postalcode);
        $stateSearch = $explode[2];
        $municipalitySearch = $explode[1];
        $asentaSearch = $explode[0];

        $posState = strpos($titleToSplit,$stateSearch);
        $posMunicipality = strpos($titleToSplit,$municipalitySearch);
        $posAsenta = strpos($this->content,$asentaSearch);

        $postalCode = "?cp=".$valueStates->id;
        if($posState !== false){
          if(!array_key_exists($stateSearch,$listStates)){
            $listStates[$stateSearch] = 1;
            if (!array_key_exists($stateSearch.$postalCode, $this->listScore["states"])) {
              $this->listScore["states"][$stateSearch.$postalCode] = 1;
            }else{
              $this->listScore["states"][$stateSearch.$postalCode] += 1;
            }
          }
        }
        if($posMunicipality !== false){
          if(!array_key_exists($municipalitySearch,$listMunicipalities)){
            $listMunicipalities[$municipalitySearch] = 1;
            if($municipalitySearch != $stateSearch){
              if (!array_key_exists($municipalitySearch."+".$stateSearch.$postalCode, $this->listScore["municipalities"])) {
                $this->listScore["municipalities"][$municipalitySearch."+".$stateSearch.$postalCode] = 1;
              }else{
                $this->listScore["municipalities"][$municipalitySearch."+".$stateSearch.$postalCode] += 1;
              }
            }
          }
        }
        if($posAsenta !== false){
          if(!array_key_exists($asentaSearch."|".$municipalitySearch,$listAsentas)){
            if($asentaSearch != $municipalitySearch && $asentaSearch != $stateSearch){
              $afterText = substr($this->content,$posAsenta + strlen($asentaSearch),1);
              if($this->searchCoincidencesSettlementAfter($afterText)){
                $listAsentas[$asentaSearch."|".$municipalitySearch] = 1;
                $res = 10;
                if($posAsenta <= 20){
                  $res = 0;
                }
                $beforeText = substr($this->content,$posAsenta - strlen($asentaSearch) - $res, strlen($asentaSearch) + 25);
                $foundBeforeText = "";
                if($this->searchCoincidencesSettlementBefore($beforeText)){
                  $foundBeforeText = "&before";
                }
                if (!array_key_exists($asentaSearch."+".$municipalitySearch."+".$stateSearch.$postalCode.$foundBeforeText, $this->listScore["asentas"])) {
                  $this->listScore["asentas"][$asentaSearch."+".$municipalitySearch."+".$stateSearch.$postalCode.$foundBeforeText] = 1;
                }else{
                  $this->listScore["asentas"][$asentaSearch."+".$municipalitySearch."+".$stateSearch.$postalCode.$foundBeforeText] += 1;
                }
              }
            }
          }
        }
      }

      $statesList = $this->listScore["states"];
      $municipalitiesList = $this->listScore["municipalities"];
      $asentasList = $this->listScore["asentas"];
      $score = 0;
      if(!empty($statesList)){
        if(count($statesList) == 1){
          $score += 1;
          $stateLevelFirst = explode("?",array_key_first($statesList));
          $this->states = $stateLevelFirst[0];
	  preg_match('/cp=(\d{1,})(&before)?/', $stateLevelFirst[1],  $matchesState, PREG_OFFSET_CAPTURE);
          if(isset($matchesState[1])){
            $this->idPostalCodeAutomatic = $matchesState[1][0];
          }
          if(!empty($municipalitiesList)){
            $countSameState = 0;
            $keyFoundMunicipality = "";
            foreach ($municipalitiesList as $keyMun => $valueMun) {
              $municipalitiesLevelFirst =  explode("?",$keyMun);
              $stateOfMunicipalityExplode = explode("+",$municipalitiesLevelFirst[0]);

              $stateExplode = $stateLevelFirst[0]; // VALOR ESTADO DE LISTA ESTADO
              $stateMunicipality = $stateOfMunicipalityExplode[1]; // VALOR ESTADO DE LISTA MUNICIPIOS

              if($stateExplode == $stateMunicipality){
                $countSameState++;
                $keyFoundMunicipality = $keyMun;
              }
            }
            if($countSameState == 1){
              $stateLevelFirst = explode("?",array_key_first($statesList));
              $municipalitiesLevelFirst =  explode("?",$keyFoundMunicipality);
              $stateOfMunicipalityExplode = explode("+",$municipalitiesLevelFirst[0]);

              $stateExplode = $stateLevelFirst[0]; // VALOR ESTADO DE LISTA ESTADO
              $stateMunicipality = $stateOfMunicipalityExplode[1]; // VALOR ESTADO DE LISTA MUNICIPIOS
              $municipality = $stateOfMunicipalityExplode[0]; // VALOR MUNICIPIO DE LA LISTA MUNICIPIOS

              if($stateExplode == $stateMunicipality){
                $score+=1;
                preg_match('/cp=(\d{1,})(&before)?/', $municipalitiesLevelFirst[1],  $matchesMun, PREG_OFFSET_CAPTURE);
                if(isset($matchesMun[1])){
                  $this->idPostalCodeAutomatic = $matchesMun[1][0];
                }
                $this->states = $stateExplode;
                $this->municipality = $municipality;
                if(!empty($asentasList)){
                  $foundAsenta = false;
                  foreach ($asentasList as $key => $value) {
                    $asentaLevelFirst = explode("?",$key);
                    $municipalityOfAsentaExplode = explode("+",$asentaLevelFirst[0]);

                    $municipality = $stateOfMunicipalityExplode[0]; // VALOR MUNICIPIO DE LA LISTA MUNICIPIOS
                    $municpalityAsenta = $municipalityOfAsentaExplode[1];

                    if($municipality == $municpalityAsenta && !$foundAsenta){
                      $score+=2;
                      $this->states = $stateExplode;
                      $this->municipality = $municipality;
                      $this->state = $municipalityOfAsentaExplode[0];
                      preg_match('/cp=(\d{1,})(&before)?/', $asentaLevelFirst[1],  $matchesAsen, PREG_OFFSET_CAPTURE);
                      if(isset($matchesAsen[1])){
                        $this->idPostalCodeAutomatic = $matchesAsen[1][0];
                      }
                      if(isset($matchesAsen[2])){
                        $score+=1;
                      }
                      $foundAsenta = true;
                    }
                  }
                }
              }
            }

          }
        }
      }
      $this->score = $score;
    }
  }
  public function searchCoincidencesSettlementBefore($text){
    $arrayCoincidencens = array("Ubicación", "Localidad", "Dirección", "Zona", "Barrio", "Colonia", "Lugar", "Entidad", "Territorio", "Terreno", "Estado", "Asentamiento","Avenida","Comunidad","Bulevar","bulevar","Vecinos","Fraccionamiento","fraccionamiento","vecinos","comunidad","avenida","ubicación", "localidad", "dirección", "zona", "barrio", "colonia", "lugar", "entidad", "territorio", "terreno", "estado", "asentamiento");
    foreach (str_word_count($text,1) as $keystack => $haystack) {
      foreach ($arrayCoincidencens as $keyCon => $valueCon) {
        if(($pos = strpos($haystack, $valueCon))!==false){
          return true;
        }
      }
    }
    return false;
  }
  public function searchCoincidencesSettlementAfter($text){
    $arrayCoincidencens = array(",", ".", ":",";"," ");
    foreach ($arrayCoincidencens as $keyCon => $valueCon) {
      if(($pos = strpos($text, $valueCon))!==false){
        return true;
      }
    }
    return false;
  }
  public function searchCoincidencesAsentamiento($text){
    $arrayCoincidencens = array("La","la");
    foreach ($arrayCoincidencens as $keyCon => $valueCon) {
      if(($pos = strpos($text, $valueCon))!==false) return true;
    }
    return false;
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
