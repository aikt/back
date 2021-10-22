<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateDataCoronaVirus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:coronavirustate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
      $listStates = array(
      array("poblation" => "1,312,544","tipo_semaforo" => 3,"id" => 1540,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F0257lx"),
      array("poblation" => "3,315,766","tipo_semaforo" => 4,"id" => 2895,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01ccgq"),
      array("poblation" => "712,029","tipo_semaforo" => 3,"id" => 5266,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01ccjx"),
      array("poblation" => "899,931","tipo_semaforo" => 1,"id" => 6228,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F0257r8"),
      array("poblation" => "2,954,915","tipo_semaforo" => 1,"id" => 12253,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01zlx"),
      array("poblation" => "711,235","tipo_semaforo" => 3,"id" => 19842,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F0183z2"),
      array("poblation" => "5,217,908","tipo_semaforo" => 4,"id" => 1,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F04sqj"),
      array("poblation" => "3,556,574","tipo_semaforo" => 3,"id" => 7525,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F015701"),
      array("poblation" => "8,918,653","tipo_semaforo" => 2,"id" => 11421,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01gmlq"),
      array("poblation" => "1,754,754","tipo_semaforo" => 3,"id" => 29300,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01gfgm"),
      array("poblation" => "5,853,677","tipo_semaforo" => 4,"id" => 62720,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01gfhk"),
      array("poblation" => "3,533,251","tipo_semaforo" => 4,"id" => 36354,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F0259ws"),
      array("poblation" => "2,858,359","tipo_semaforo" => 3,"id" => 46186,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01bttt"),
      array("poblation" => "7,844,830","tipo_semaforo" => 3,"id" => 50928,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01gmng"),
      array("poblation" => "16,187,608","tipo_semaforo" => 3,"id" => 57012,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01btyw"),
      array("poblation" => "4,584,471","tipo_semaforo" => 3,"id" => 70843,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01btry"),
      array("poblation" => "1,903,811","tipo_semaforo" => 3,"id" => 81042,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01btwx"),
      array("poblation" => "1,181,050","tipo_semaforo" => 3,"id" => 82760,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01gfj1"),
      array("poblation" => "5,119,504","tipo_semaforo" => 3,"id" => 84736,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01gfjk"),
      array("poblation" => "3,967,889","tipo_semaforo" => 2,"id" => 89572,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F014m1m"),
      array("poblation" => "6,168,883","tipo_semaforo" => 3,"id" => 95644,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F025djt"),
      array("poblation" => "2,038,372","tipo_semaforo" => 3,"id" => 100969,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01gfk1"),
      array("poblation" => "1,501,562","tipo_semaforo" => 3,"id" => 104063,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01bgy3"),
      array("poblation" => "2,717,820","tipo_semaforo" => 3,"id" => 105291,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F025cf3"),
      array("poblation" => "2,966,321","tipo_semaforo" => 2,"id" => 111271,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01gfkk"),
      array("poblation" => "2,850,330","tipo_semaforo" => 3,"id" => 115452,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F0ldff"),
      array("poblation" => "2,395,272","tipo_semaforo" => 3,"id" => 124203,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01bkt4"),
      array("poblation" => "3,441,698","tipo_semaforo" => 2,"id" => 126847,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F01gfl1"),
      array("poblation" => "1,272,847","tipo_semaforo" => 2,"id" => 130123,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F025f1d"),
      array("poblation" => "8,112,505","tipo_semaforo" => 2,"id" => 131561,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F0g_gl"),
      array("poblation" => "2,097,175","tipo_semaforo" => 3,"id" => 140461,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F09gk_"),
      array("poblation" => "1,579,209","tipo_semaforo" => 3,"id" => 142117,"url"=>"https://news.google.com/covid19/map?hl=es-419&gl=MX&ceid=MX%3Aes-419&mid=%2Fm%2F012tgw")
      );

      foreach ($listStates as $key => $value) {
        $c = curl_init($value["url"]);

        $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';

        curl_setopt($c, CURLOPT_USERAGENT, $config['useragent']);
        curl_setopt($c, CURLOPT_REFERER, 'https://news.google.com');

        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($c);

        if (curl_error($c)) die(curl_error($c));

        // Get the status code
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        preg_match_all(
            '/Total de casos<\/div><div.*?>(.*?)<\/div>/',
            $html,
            $matchesTotalCases,
            PREG_PATTERN_ORDER
        );

        preg_match_all(
            '/Informe de ayer:(.*?)<strong>(.*?)<\/strong>/',
            $html,
            $matchesPerday,
            PREG_PATTERN_ORDER
        );

        preg_match_all(
            '/Muertes<\/div><div.*?>(.*?)<\/div>/',
            $html,
            $matchesDeaths,
            PREG_PATTERN_ORDER
        );

        echo $matchesTotalCases[1][0]."\n";
        echo $matchesPerday[2][0]."\n";
        echo $matchesDeaths[1][0]."\n\n\n";

        $totalcases = $matchesTotalCases[1][0];
        $totalperday = $matchesPerday[2][0];
        $totaldeaths = $matchesDeaths[1][0];
        $id_cp = $value["id"];
        $poblation = $value["poblation"];
        $tipo_semadoro = $value["tipo_semaforo"];
        $curdate      = date("Y-m-d H:i:s");


        \DB::insert("INSERT INTO `coronavirus_per_state`(`total_cases`,`new_cases_per_day`,`deaths`,`created_at`,`updated_at`,`id_cp`,`tipo_semaforo`,`poblation`) VALUES ('$totalcases','$totalperday','$totaldeaths','$curdate','$curdate',$id_cp,$tipo_semadoro,'$poblation')");
      }
    }
}
