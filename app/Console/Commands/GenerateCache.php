<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera el cache para las urls';

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

      $mysqli = mysqli_connect("copocluster.cluster-cyzzhqbezi1j.us-east-2.rds.amazonaws.com", "admin", "qdhuhs4es", "cacheCopo");

      $arrayUrls = array(
        "https://codigopostal.com?actualizacache=true",
        "https://codigopostal.com/ciudad-de-mexico/cuauhtemoc/roma-condesa?actualizacache=true",
        "https://codigopostal.com/ciudad-de-mexico/cuauhtemoc?actualizacache=true",
        "https://codigopostal.com/ciudad-de-mexico/benito-juarez/del-valle?actualizacache=true",
        "https://codigopostal.com/ciudad-de-mexico/benito-juarez?actualizacache=true",
        "https://codigopostal.com/ciudad-de-mexico/cuauhtemoc/centro-cdmx?actualizacache=true",
        "https://codigopostal.com/ciudad-de-mexico/cuauhtemoc?actualizacache=true",
        "https://codigopostal.com/ciudad-de-mexico/miguel-hidalgo/polanco?actualizacache=true",
        "https://codigopostal.com/ciudad-de-mexico/miguel-hidalgo?actualizacache=true",
        "https://codigopostal.com/puebla/san-pedro-cholula/cholula?actualizacache=true",
        "https://codigopostal.com/puebla/san-pedro-cholula?actualizacache=true",
        "https://codigopostal.com/puebla/puebla/puebla-centro?actualizacache=true",
        "https://codigopostal.com/puebla/atlixco?actualizacache=true",
        "https://codigopostal.com/jalisco/guadalajara/guadalajara-centro?actualizacache=true",
        "https://codigopostal.com/jalisco/guadalajara?actualizacache=true",
        "https://codigopostal.com/guanajuato/irapuato/irapuato-centro?actualizacache=true",
        "https://codigopostal.com/guanajuato/irapuato?actualizacache=true",
        "https://codigopostal.com/guanajuato/celaya/celaya-centro?actualizacache=true",
        "https://codigopostal.com/guanajuato/celaya?actualizacache=true",
        "https://codigopostal.com/guanajuato/salamanca/salamanca-centro?actualizacache=true",
        "https://codigopostal.com/guanajuato/salamanca?actualizacache=true",
        "https://codigopostal.com/guanajuato/leon/leon-centro?actualizacache=true",
        "https://codigopostal.com/guanajuato/leon?actualizacache=true",
        "https://codigopostal.com/jalisco/guadalajara/jardines-de-la-cruz?actualizacache=true",
        "https://codigopostal.com/jalisco/guadalajara/maga%C3%B1?actualizacache=true",
        "https://codigopostal.com/jalisco/guadalajara?actualizacache=true",
      );

      foreach ($arr_urls as $key => $value) {
        $this->generateCurlCache($value);
      }

      // $sql = "SELECT * FROM caches where update_at >= NOW() - INTERVAL 10 MINUTE order by update_at DESC";
      // $resultado = $mysqli->query($sql);

      // while ($cache = $resultado->fetch_assoc()) {
      //   $urlNew = $cache["url"];
      //   $pos = strpos($urlNew,"?");
      //   if($pos === false){
      //     $urlNew .= "?actualizacache=true";
      //   }else{
      //     $urlNew .= "&actualizacache=true";
      //   }
      //   echo $urlNew."\n";
      //   $this->generateCurlCache($urlNew);
      // }
      //
      // $news = \DB::select("SELECT * FROM news where id_status_news IN (1,5,7,8) and created_at >= NOW() - INTERVAL 8 HOUR order by created_at DESC");
      //
      // if(count($news) > 0){
      //   $listToCache = array();
      //   foreach ($news as $new) {
      //     $urlSearch = '';
      //     if(!empty($new->url_copo)){
      //       $urlSearch = $new->url_copo;
      //     }else{
      //       $urlSearch = $new->url;
      //     }
      //     $urlSearch = env("APP_URL_FRONT").ltrim($urlSearch,$urlSearch[0]);
      //     echo $urlSearch."\n";
      //     $sqlFind = "SELECT * FROM caches where url LIKE CONCAT('%','$urlSearch','%')";
      //     $resultadoFind = $mysqli->query($sqlFind);
      //
      //     if($resultadoFind->num_rows == 0){
      //       array_push($listToCache,$urlSearch);
      //     }
      //   }
      //   if(count($listToCache) > 0){
      //     foreach ($listToCache as $urlToCache) {
      //       $this->generateCurlCache($urlToCache);
      //     }
      //   }
      // }
    }

    public function generateCurlCache($urlCache){
      $url = $urlCache;
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
      curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_TIMEOUT,10);
      $output = curl_exec($ch);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      echo 'HTTP code: ' . $httpcode."  $urlCache\n";
    }
}
