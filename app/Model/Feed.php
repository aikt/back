<?php

namespace App\Model;

use Illuminate\Console\Command;
use App\Console\Commands\DB;
use App\Model\FeedNew;

class Feed
{
  public $url;
  public $domain;
  public $id_author;
  public $news = array();

  public function __construct($data){
    ini_set('memory_limit', '-1');  
    $cURLConnection = curl_init();

    curl_setopt($cURLConnection, CURLOPT_URL, $data->url);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

    $xmlData = curl_exec($cURLConnection);
    curl_close($cURLConnection);

    libxml_use_internal_errors(true);

    $xml = simplexml_load_string($xmlData,null,LIBXML_NOCDATA);

    if($xml){
      if(isset($xml->channel)){
	echo "\n\n!!!!!!=======  ".$xml->channel->title." =========!!!!!!\n\n";
        foreach ($xml->channel->item as $key => $value) {
		$new = new FeedNew($value,$data);
        }
      }
    }
  }
}
