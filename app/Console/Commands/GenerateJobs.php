<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:jobs';

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
      $listJobs = array(
        array("id" => 1540,"url"=>"https://mx.indeed.com/jobs?q=&l=Aguascalientes"),
        array("id" => 2895,"url"=>"https://mx.indeed.com/jobs?q=&l=Baja+California"),
        array("id" => 5266,"url"=>"https://mx.indeed.com/trabajo?q=&l=Baja+California+Sur"),
        array("id" => 6228,"url"=>"https://mx.indeed.com/jobs?q=&l=Campeche"),
        array("id" => 12253,"url"=>"https://mx.indeed.com/jobs?q=&l=Chiapas"),
        array("id" => 19842,"url"=>"https://mx.indeed.com/jobs?q=&l=Chihuahua"),
        array("id" => 1,"url"=>"https://mx.indeed.com/jobs?q=&l=Ciudad+de+M%C3%A9xico"),
        array("id" => 7525,"url"=>"https://mx.indeed.com/trabajo?q=&l=Coahuila"),
        array("id" => 11421,"url"=>"https://mx.indeed.com/jobs?q=&l=Colima%2C+Col"),
        array("id" => 29300,"url"=>"https://mx.indeed.com/jobs?q=&l=Durango"),
        array("id" => 62720,"url"=>"https://mx.indeed.com/jobs?q=&l=estado+de+mexico"),
        array("id" => 36354,"url"=>"https://mx.indeed.com/jobs?q=&l=Guanajuato"),
        array("id" => 46186,"url"=>"https://mx.indeed.com/jobs?q=&l=Guerrero"),
        array("id" => 50928,"url"=>"https://mx.indeed.com/jobs?q=&l=Hidalgo"),
        array("id" => 57012,"url"=>"https://mx.indeed.com/jobs?q=&l=Jalisco"),
        array("id" => 70843,"url"=>"https://mx.indeed.com/jobs?q=&l=Michoac%C3%A1n"),
        array("id" => 81042,"url"=>"https://mx.indeed.com/jobs?q=&l=Morelos"),
        array("id" => 82760,"url"=>"https://mx.indeed.com/jobs?q=&l=Nayarit"),
        array("id" => 84736,"url"=>"https://mx.indeed.com/jobs?q=&l=Nuevo+Le%C3%B3n"),
        array("id" => 89572,"url"=>"https://mx.indeed.com/jobs?q=&l=Oaxaca"),
        array("id" => 95644,"url"=>"https://mx.indeed.com/jobs?q=&l=Puebla%2C+Pue.&radius=0"),
        array("id" => 100969,"url"=>"https://mx.indeed.com/jobs?q=&l=Quer%C3%A9taro%2C+Qro"),
        array("id" => 104063,"url"=>"https://mx.indeed.com/jobs?q=&l=Quintana+Roo"),
        array("id" => 105291,"url"=>"https://mx.indeed.com/jobs?q=&l=San+Luis+Potos%C3%AD%2C+S.+L.+P"),
        array("id" => 111271,"url"=>"https://mx.indeed.com/jobs?q=&l=Sinaloa"),
        array("id" => 115452,"url"=>"https://mx.indeed.com/jobs?q=&l=Sonoras"),
        array("id" => 124203,"url"=>"https://mx.indeed.com/jobs?q=&l=Tabasco"),
        array("id" => 126847,"url"=>"https://mx.indeed.com/jobs?q=&l=Tamaulipas"),
        array("id" => 130123,"url"=>"https://mx.indeed.com/jobs?q=&l=Tlaxcala"),
        array("id" => 131561,"url"=>"https://mx.indeed.com/jobs?q=&l=Veracruz%2C+Ver"),
        array("id" => 140461,"url"=>"https://mx.indeed.com/jobs?q=&l=Yucat%C3%A1n"),
        array("id" => 142117,"url"=>"https://mx.indeed.com/jobs?q=&l=Zacatecas%2C+Zac")
      );

      foreach ($listJobs as $key => $value) {
        $c = curl_init($value["url"]);

        $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';

        curl_setopt($c, CURLOPT_USERAGENT, $config['useragent']);
        curl_setopt($c, CURLOPT_REFERER, 'https://mx.indeed.com/');

        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($c);

        if (curl_error($c)) die(curl_error($c));

        // Get the status code
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        preg_match_all(
            '/href="(.*?)"\n.*\n.*\n.*\ntitle="(.*?)"\nclass="jobtitle.turnstileLink.*?"\n.*\n.\n.*\n\n.*\n\n<div.class="sjcl".\n.*\n<span.class="company">\n(.*?)<\/span>\n\n.*\n.*data-rc-loc="(.*?)".*\n.*\n.*\n\n.*.*\n.*\n.*salaryText">\n(.*?)<\/span>\n.*\n.*\n.*\n<ul.*?>((.*\n){1,}?)<\/ul>/',
            $html,
            $matchesTotalCases,
            PREG_PATTERN_ORDER
        );

        if(isset($matchesTotalCases[1]))
        {
          foreach ($matchesTotalCases[1] as $keyJob => $valueJob)
          {
            $url = "https://mx.indeed.com" . $valueJob;
            $title = $matchesTotalCases[2][$keyJob];
            $company = $matchesTotalCases[3][$keyJob];
            $location = $matchesTotalCases[4][$keyJob];
            $salary = $matchesTotalCases[5][$keyJob];
            $content = addslashes(html_entity_decode($matchesTotalCases[6][$keyJob]));
            $id_cp = $value["id"];
            $curdate      = date("Y-m-d H:i:s");

            $title_exists =  \DB::select("SELECT title FROM announcementJob WHERE title LIKE '{$title}%'");

            if(!empty($title_exists))
            {
              \DB::insert("INSERT INTO `announcementJob`(`title`,`location`,`salary`,`content`,`company`,`url`,`id_cp`,`created_at`,`updated_at`)
                           VALUES ('$title','$location','$salary','$content','$company','$url',$id_cp,'$curdate','$curdate')");
            }
            else
            {
              echo("EL TRABAJO YA EXISTE\n");
            }
          }
        }
      }
    }
}
