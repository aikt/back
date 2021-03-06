<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aws;

class GenerateNewClima extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:new:clima';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea las notas del clima';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      \DB::select("SET lc_time_names = 'es_ES';");
      date_default_timezone_set ('America/Mexico_City');
      setlocale(LC_TIME, 'es_ES.UTF-8');

      $resultClima = \DB::select('SELECT * FROM `clima` WHERE DATE(`created_at`) = CURDATE();');
      $diaHoy = strftime("%d de")." ".strftime("%B");


      foreach ($resultClima as $key => $value) {
        $tipo_clima = strtolower($value->tipo_clima);
        $estado = $value->estado;
        $municipio = $value->municipio;
        $temp_max = number_format($value->temp_max,0,".","");
        $temp_min = number_format($value->temp_min,0,".","");
        $direccion_viento = strtolower($value->direccion_viento);
        $direccion_viento_grados = number_format($value->direccion_viento_grados,0,".","");
        $numero_dia = $value->numero_dia;
        $latitud = $value->latitud;
        $longitud = $value->longitud;
        $prob_precipitacion = number_format($value->probabilidad_precipitacion,0,".","");
        $precipitacion = $value->precipitacion;
        $velocidad_viento = $value->velocidad_viento;

        $postalCodeUrl = \DB::select("SELECT * FROM postal_codes where d_municipio LIKE '%".$municipio."%' and d_estado LIKE '%".$estado."%' GROUP BY d_municipio LIMIT 1");

        $estadoUrl = $this->slugify($postalCodeUrl[0]->d_estado);
        $municipioUrl = $this->slugify($postalCodeUrl[0]->d_municipio);
        $idCpUrl = $postalCodeUrl[0]->id;



        $spinTitleOne = array("la temperatura m??nima ser?? de ".$temp_min." grados" , "la temperatura m??xima ser?? de ".$temp_max." grados");
        $spinTitleTwo = array("la m??nima ser?? de ".$temp_min." grados" , "la m??xima ser?? de ".$temp_max." grados");
        $spinTitleThree = array("temperatura m??nima de ".$temp_min." grados" , "temperatura m??xima de ".$temp_max." grados");
        $titulos = array(
          "El clima hoy ".$diaHoy." en ".$municipio.": m??xima ".$temp_max." grados y m??nima ".$temp_min,
          "Este ".$diaHoy." el clima de ".$municipio.": temperatura m??xima ".$temp_max." grados",
          "El clima en ".$municipio." este ".$diaHoy.": m??xima ".$temp_max." grados",
          $municipio." este ".$diaHoy." tendr?? una m??xima de ".$temp_max." grados y m??nima de ".$temp_min
        );
        $spinSumarioOne = array("temperatura m??nima de ".$temp_min , "temperatura m??xima de ".$temp_max);
        $spinSumarioTwo = array("tendr?? una m??xima de ".$temp_max,"tendr?? una m??nima de ".$temp_min);
        $spinSumarioThree = array("una temperatura m??xima ".$temp_max." grados","una temperatura m??nima ".$temp_min." grados");
        $sumario = array(
          "Conoc?? el pron??stico del tiempo brindado por el Servicio Meteorol??gico Nacional",
          "El Sistema Meteorol??gico Nacional pronostic?? cielo ".$tipo_clima,
          "La probabilidad de precipitaci??n es de ".$prob_precipitacion."%",
          "Hoy los vientos alcanzar??n una velocidad de ".$velocidad_viento." km/hr"
        );
        $spinOne = array("prev??","anticipa","determina");
        $spinTwo = array("Adem??s","A su vez","Por otra parte");
        $spinThree = array("que se espera es","estimada es","podr??a ser");
        $spinFour = array("anuncia","establece","comunic??","inform??");
        $spinFive = array("Ahora que ya conoces","Ahora que ya sab??s","Ya te informaste sobre");
        $spinSix = array("el d??a de hoy","este d??a");
        $texto = array(
          "<p>El pron??stico del tiempo para ".$municipio." hoy anticipa que el cielo estar?? ".$tipo_clima." por la ma??ana y la temperatura m??xima alcanzar?? ".$temp_max." grados. A su vez, la m??nima se espera sea de ".$temp_min.".</p>
           <p>Seg??n comunic?? el Servicio Meteorol??gico Nacional (SMN), el clima presenta un ".$prob_precipitacion."% de probabilidad de precipitaciones con vientos del sur a una velocidad de ".$velocidad_viento." kil??metros por hora.</p>
           <p>Ya te informaste sobre c??mo estar?? el clima y no te pierdas las ??ltimas noticias sobre ".$municipio.".</p>",

          "<p>El Sistema Meteorol??gico Nacional pronostic?? para ".$municipio." hoy una temperatura m??xima ".$temp_max." grados y una m??nima de ".$temp_min."</p>
           <p>Tendr??n cielo ".$tipo_clima." con vientos de ".$velocidad_viento." km/h. La probabilidad de precipitaci??n ser?? de ".$prob_precipitacion."%.</p>
           <p>El Servicio Meteorol??gico Nacional (SMN) es el organismo encargado de proporcionar informaci??n sobre el estado del tiempo a escala nacional y local en nuestro pa??s.</p>",

           "<p>Hoy ".$municipio." tendr?? cielo ".$tipo_clima.". La humedad relativa ser?? de ".rand(10,30)."% y la probabilidad de lluvia es de ".rand(10,45)."%</p>
           <p>El Sistema Meteorol??gico Nacional pronostic?? una temperatura m??xima ".$temp_max." grados y una m??nima de ".$temp_min.". Se prev?? que los vientos alcancen ".$velocidad_viento."km/h con direcci??n al sur</p>
           <p>El Sistema Meteorol??gico Nacional cuenta con una red de 13 radares meteorol??gicos distribuidos en el Territorio Nacional y que comenz?? a funcionar en 1993 y proporciona informaci??n continua que se recibe v??a sat??lite.</p>",

           "<p>El SMN pronostic?? para hoy en ".$municipio." una temperatura m??xima de ".$temp_max." grados y una m??nima de ".$temp_min."</p>
            <p>El cielo ser?? ".$tipo_clima.", con una humedad de ".rand(10,30)."% y la probabilidad de lluvia de ".rand(10,45)."%. La velocidad de viento el d??a de hoy ser?? de ".$velocidad_viento."km/h</p>
            <p>Proveer pron??sticos, alertas e informaci??n del estado del tiempo y del clima estrat??gica y ??til para el pa??s, que sustente la toma de decisiones son parte de las funciones del SMN.</p>"
        );


        switch ($tipo_clima) {
          case 'medio nublado':
            $imagen_tipo_clima = "medio_nublado.png";
            break;
          case 'cielo nublado':
            $imagen_tipo_clima = "nublado.png";
            break;
          case 'poco nuboso':
            $imagen_tipo_clima = "poco_nuboso.png";
            break;
          case 'despejado':
            $imagen_tipo_clima = "despejado.png";
            break;
          case 'cielo cubierto':
            $imagen_tipo_clima = "cielo_cubierto.png";
            break;
          default:
            $imagen_tipo_clima = "clima.png";
            break;
        }

        $this->CrearTransparencia("negro",$imagen_tipo_clima);
        // $this->CargarDatosaImagen($imagen_tipo_clima,"blanco",$municipio);

        $get_url_image = $this->getImageFromAWSByUriImageExternal(getcwd().'/app/Console/Commands/images/clima_convertida/'.str_replace(".png", (date("ymd").".png"), $imagen_tipo_clima));

        if($get_url_image !== false)
        {
            $url_img = $get_url_image['relative_path'];
        }
        $id_cp = 3635;
        $ftitle = $titulos[array_rand($titulos,1)];
        $title_slug = $this->slugify($ftitle)."-".$municipio."-".date("Y-m-d");
        $fsummary = $sumario[array_rand($sumario,1)];
        $content = $texto[array_rand($texto,1)];
        $url = "/".$estadoUrl."/".$municipioUrl."/".$idCpUrl."/".$title_slug;
        $curdate = date("Y-m-d H:i:s");

        $insert_news = \DB::insert("INSERT INTO `news`(`id_cp`,`title`,`url`,`summary`,`content`,`created_at`,`updated_at`,
                                                        `image`,`seo_title`,`seo_description`,`seo_keywords`,`id_status_news`,`imported`,`title_normalizado`,
                                                        `id_editor`,`url_canonical`,`id_author`,`id_position`,`id_copo_id`,`id_state`,`model_feed`,`caption`)
                                    VALUES ($id_cp, '$ftitle','$url',
                                    '$fsummary',
                                    '$content','$curdate', '$curdate', '$url_img', '$ftitle', '$ftitle', '$ftitle',7, 0, '$title_slug', NULL, NULL,100,NULL,
                                    NULL, $id_cp, 0, NULL);");
      }
    }
    public function slugify($text)
    {
        // Strip html tags
        $text = strip_tags($text);
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
    public function getImageFromAWSByUriImageExternal($uriImageExternal){
  		$s3 = new Aws\S3\S3Client([
  			'region'  => 'us-east-2',
  			'version' => 'latest',
  			'credentials' => [
  				'key'    => "AKIAIQS7HMCD7GJJMLNA",
  				'secret' => "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",
  			]
  		]);
  		if (!file_exists('tmp')) {
  			mkdir('tmp', 0777, true);
  		}

  		$getImage = file_get_contents($uriImageExternal);

  		if($getImage !== FALSE)
  		{
  			$nameImage = rand(100000,1000000000).'.jpg';
  			$img = 'tmp/'.$nameImage;
  			file_put_contents($img, $getImage);
              $size = getimagesize($img);
              $img_up = "img/clima/" . $nameImage;

  			if(!empty($size)){
  				$result = $s3->putObject([
  					'Bucket' => 'copoadminpro',
  					'Key'    => $img_up,
  					'SourceFile' => realpath($img)
  				]);

  				$urlImageFromAWS = $result->get("ObjectURL");
  				if(!empty($urlImageFromAWS)) {
  					unlink($img);
  					return array(
                       "absolute_path" => $urlImageFromAWS,
                       "relative_path" => $img_up
                      );
  				}else return false;
  			}else{
  				unlink($img);
  				return false;
  			}
  		}else return false;
  	}
    public function CrearTransparencia($semaforo, $nombre){
        try {
            $r = 0;
            $g = 0;
            $b = 0;

            echo "Nombre foto clima: ".$nombre."\n";

            switch ($semaforo) {
                case "verde":
                    $r = 137;
                    $g = 238;
                    $b = 133;
                    break;
                case "amarillo":
                    $r = 255;
                    $g = 213;
                    $b = 0;
                    break;
                case "naranja":
                    $r = 255;
                    $g = 128;
                    $b = 0;
                    break;
                case "rojo":
                    $r = 255;
                    $g = 0;
                    $b = 76;
                    break;
                case "gris":
                    $r = 155;
                    $g = 155;
                    $b = 155;
                    break;
                case "negro":
                    $r = 0;
                    $g = 0;
                    $b = 0;
                    break;
            }

            //Cargamos la dos imagenes ambas de 128x128px
            $a = sprintf(getcwd()."/app/Console/Commands/images/clima/%s", $nombre);
            $img1 = imagecreatefrompng($a);

            //Creamos el lienzo con el tama??o para contener las 2 imagenes, y le asignamos transparencia
            // $image = imagecreatetruecolor(831, 548);
            // imagesavealpha($image, true);
            // $alpha = imagecolorallocatealpha($image, $r, $g, $b, 50);
            // imagefill($image, 0, 0, $alpha);

            //Guardamos y leberamos el objeto
            // $c = sprintf(getcwd().'/app/Console/Commands/images/clima_transparencias/%s.png', $semaforo);
            // imagepng($image, $c);

            // $marca = imagecreatefrompng($c);
            //
            // imagecopy($img1, $marca, 0, 0, 0, 0, 831, 548);
            imagepng($img1, getcwd().'/app/Console/Commands/images/clima_convertida/' . str_replace(".png", (date("ymd").".png"), $nombre));

            // imagedestroy($image);
            // imagedestroy($img1);

            echo "Se creo la transparencia \n";

            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    public function CargarDatosaImagen($nombre,$semaforo,$municipio){
        try {
            //header('Content-type: image/png');

            // Load And Create Image From Source
            $imgs = sprintf(getcwd().'/app/Console/Commands/images/clima_convertida/%s', str_replace(".png", (date("ymd").".png"), $nombre));

            $our_image = imagecreatefrompng($imgs);

            // Set Path to Font File
            $font_path = getcwd().'/app/Console/Commands/fonts/static/Karla-Bold.ttf';

            // Set Text to Be Printed On Image
            $text1 = "Clima de hoy en ".$municipio.":";
            $text2= ucfirst(strftime("%A %d de"))." ".ucfirst(strftime("%B"))." del ".strftime("%Y");

            $r1 = 0;
            $g1 = 0;
            $b1 = 0;

            $r2 = 0;
            $g2 = 0;
            $b2 = 0;

            switch ($semaforo) {
                case "verde";
                case "amarillo";
                    $r1 = 0;
                    $g1 = 0;
                    $b1 = 0;

                    $r2 = 0;
                    $g2 = 130;
                    $b2 = 255;
                    break;
                case "naranja":
                case "rojo":
                    $r1 = 0;
                    $g1 = 0;
                    $b1 = 0;

                    $r2 = 255;
                    $g2 = 255;
                    $b2 = 255;
                    break;
                case "negro":
                    $r1 = 0;
                    $g1 = 0;
                    $b1 = 0;

                    $r2 = 0;
                    $g2 = 0;
                    $b2 = 0;
                    break;
                default:
                    $r1 = 255;
                    $g1 = 255;
                    $b1 = 255;

                    $r2 = 255;
                    $g2 = 255;
                    $b2 = 255;
                    break;
            }

            $size1 = 30;
            $angle1 = 0;
            $left1 = 10;
            $top1 = 50;

            $white_color = imagecolorallocate($our_image, $r1, $g1, $b1);
            imagettftext($our_image, $size1, $angle1, $left1, $top1, $white_color, $font_path, $text1);

            $size2 = 35;
            $angle2 = 0;
            $left2 = 10;
            $top2 = 125;

            $white_color = imagecolorallocate($our_image, $r1, $g1, $b1);
            imagettftext($our_image, $size2, $angle2, $left2, $top2, $white_color, $font_path, $text2);

            $size2 = 60;
            $angle2 = 0;
            $left2 = 10;
            $top2 = 200;

            // $white_color = imagecolorallocate($our_image, $r2, $g2, $b2);
            // imagettftext($our_image, $size2, $angle2, $left2, $top2, $white_color, $font_path, $text3);


            // $size1 = 22;
            // $angle1 = 0;
            // $left1 = 50;
            // $top1 = 330;
            //
            // $white_color = imagecolorallocate($our_image, $r1, $g1, $b1);
            // imagettftext($our_image, $size1, $angle1, $left1, $top1, $white_color, $font_path, $text4);
            //
            // $size1 = 22;
            // $angle1 = 0;
            // $left1 = 50;
            // $top1 = 370;
            //
            // $white_color = imagecolorallocate($our_image, $r1, $g1, $b1);
            // imagettftext($our_image, $size1, $angle1, $left1, $top1, $white_color, $font_path, $text5);


            // $size1 = 70;
            // $angle1 = 0;
            // $left1 = 50;
            // $top1 = 470;
            //
            // $white_color = imagecolorallocate($our_image, $r2, $g2, $b2);
            // imagettftext($our_image, $size1, $angle1, $left1, $top1, $white_color, $font_path, $text6);

            $nombre = sprintf(getcwd().'/app/Console/Commands/images/clima_convertida/%s', str_replace(".png", (date("ymd")."_".$municipio.".png"), $nombre));

            imagepng($our_image, $nombre);

            echo sprintf("La imagen se creo en %s\n", $nombre);

            // Clear Memory
            imagedestroy($our_image);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
}
