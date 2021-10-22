<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateClima extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:clima';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Clima information and save in our database';

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
      $lines = gzfile('https://smn.conagua.gob.mx/webservices/index.php?method=1');
      $implodeDa = implode($lines);
      $datal = json_decode($implodeDa,true);

      $arrayClima = array();

      $dateNow = date("Ymd");

      $arrayMunicipios = array(
        array(
          "estado" => "Guanajuato",
          "municipio" => "León"
        ),
        array(
          "estado" => "Guanajuato",
          "municipio" => "Irapuato"
        ),
        array(
          "estado" => "Guanajuato",
          "municipio" => "Celaya"
        ),
        array(
          "estado" => "Guanajuato",
          "municipio" => "Salamanca"
        ),
        array(
          "estado" => "Jalisco",
          "municipio" => "Guadalajara"
        ),
        array(
          "estado" => "Puebla",
          "municipio" => "San Pedro Cholula"
        ),
        array(
          "estado" => "Puebla",
          "municipio" => "Puebla"
        ),
        array(
          "estado" => "Puebla",
          "municipio" => "Atlixco"
        ),
        array(
          "estado" => "Ciudad de México",
          "municipio" => "Cuauhtémoc"
        ),
        array(
          "estado" => "Ciudad de México",
          "municipio" => "Benito Juárez"
        ),
        array(
          "estado" => "Ciudad de México",
          "municipio" => "Miguel Hidalgo"
        ),
      );
      $arr = [];
      $curdate = date("Y-m-d H:i:s");
      foreach ($datal as $key => $value) {
        $pos = strpos($value["dloc"],$dateNow);
        $arr[$value["desciel"]] = "";
        if($pos !== false){
          foreach ($arrayMunicipios as $municipio) {
            if($value["nes"] == $municipio["estado"]){
              if($value["nmun"] == $municipio["municipio"]){
                $tipo_clima = $value["desciel"];
                $estado = $value["nes"];
                $municipio = $value["nmun"];
                $temp_max = $value["tmax"];
                $temp_min = $value["tmin"];
                $direccion_viento = $value["dirvienc"];
                $direccion_viento_grados = $value["dirvieng"];
                $numero_dia = $value["ndia"];
                $latitud = $value["lat"];
                $longitud = $value["lon"];
                $prob_precipitacion = $value["probprec"];
                $precipitacion = $value["prec"]."%";
                $velocidad_viento = $value["velvien"];
                \DB::insert("INSERT INTO `clima`(`tipo_clima`,`estado`,`municipio`,`temp_max`,`temp_min`,`velocidad_viento`,`direccion_viento`,`direccion_viento_grados`,`numero_dia`,`latitud`,`longitud`,`probabilidad_precipitacion`,`precipitacion`,`created_at`)
                            VALUES ('$tipo_clima','$estado','$municipio','$temp_max','$temp_min','$velocidad_viento','$direccion_viento','$direccion_viento_grados','$numero_dia','$latitud','$longitud','$prob_precipitacion','$precipitacion','$curdate')");
              }
            }
          }
        }
      }
    }
}
