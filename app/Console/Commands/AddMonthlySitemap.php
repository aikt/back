<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\DB;

class AddMonthlySitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando genera cada mes un nuevo archivo sitemap';

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
      date_default_timezone_set('America/Mexico_City');
      for ($i=2017; $i <= 2022; $i++) {
        $year = $i;
        for ($j=1; $j <= 12; $j++) {
          $month = $j;
          if($j < 10){
            $month = "0".$j;
          }
          $filename = "sitemap-articles-".$year."-".$month.".xml";
          if(!file_exists("sitemap/articles/".$filename)){
            $xml = new \DOMDocument();
            $xmlUrlSet = $xml->createElement("urlset");
            $xmlUrlSet->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
            $xml->appendChild( $xmlUrlSet );

            $xml->save('public/sitemap/articles/'.$filename);
            // TODO:Comenzamos con las noticias
            $news = \DB::select("SELECT url FROM news WHERE MONTH(created_at) = ".$month." AND YEAR(created_at) = ".$year." AND id_status_news = 1 AND url != '' AND id_cp IS NOT NULL");
            if(count($news) > 0 ){
              $file = realpath(__DIR__ . '/../../../public/sitemap/articles/'.$filename);
              $xml = simplexml_load_file($file);
              $sitemaps = $xml;

              foreach ($news as $key => $value) {
                $sitemap = $sitemaps->addChild('url');
                $sitemap->addChild("loc","https://codigopostal.com".$value->url);
                $sitemap->addChild("lastmod",date('c',time()));
                $sitemap->addChild("changefreq","hourly");
                $sitemap->addChild("priority",0.9);

                $xml->asXML($file);
              }
            }

            $file = realpath(__DIR__ . '/../../../public/sitemap/sitemap-index.xml');

            $xml = simplexml_load_file($file);

            $sitemaps = $xml;
            $sitemap = $sitemaps->addChild('sitemap');
            $sitemap->addChild("loc","https://admin.codigopostal.com/sitemap/articles/".$filename);
            $sitemap->addChild("lastmod",date('c',time()));

            $xml->asXML($file);
          }
        }

      }

      //TODO: AQUI SE HACE LAS NOTAS DE SECCIONES
      $filename = "sitemap-sections.xml";
      if(!file_exists("sitemap/sections/".$filename)){
        $xml = new \DOMDocument();
        $xmlUrlSet = $xml->createElement("urlset");
        $xmlUrlSet->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
        $xmlUrlSet->setAttribute("xmlns:news","http://www.google.com/schemas/sitemap-news/0.9");

        $xml->appendChild( $xmlUrlSet );

        $xml->save('public/sitemap/sections/sitemap-sections.xml');
      }

      // $sections = \DB::select("SELECT cp.d_municipio as municipio,cp.d_estado as estado,copo.title as copo FROM news as nw LEFT JOIN postal_codes as cp ON cp.id = nw.id_cp LEFT JOIN copos_postalcodes as cpp ON cpp.postal_code_id = cp.d_codigo LEFT JOIN copos as copo ON copo.id = cpp.copo_id WHERE nw.id_status_news = 1 AND nw.url != '' AND nw.id_cp IS NOT NULL GROUP BY cp.d_municipio");

      $sections = \DB::select("SELECT nw.title, nw.created_at,cp.d_municipio AS municipio,
                                cp.d_estado AS estado,copo.title AS copo
                                FROM news AS nw 
                                LEFT JOIN postal_codes AS cp ON cp.id = nw.id_cp
                                LEFT JOIN copos_postalcodes AS cpp ON cpp.postal_code_id = cp.d_codigo
                                LEFT JOIN copos AS copo ON copo.id = cpp.copo_id
                                WHERE nw.id_status_news = 1 AND nw.url ! = ''
                                AND nw.id_cp IS NOT NULL
                                GROUP BY cp.d_municipio ORDER BY nw.created_at DESC;");


      if(count($sections) > 0){
        foreach ($sections as $key => $value) {
          $file = realpath(__DIR__ . '/../../../public/sitemap/sections/sitemap-sections.xml');
          $xml = simplexml_load_file($file);
          $sitemaps = $xml;

          $url = "/".$this->slugify($value->estado)."/".$this->slugify($value->municipio);
          if(!empty($value->copo)){
            $url.="/".$this->slugify($value->copo);
          }
          $sitemap = $sitemaps->addChild('url');
          $sitemap->addChild("loc","https://codigopostal.com".$url);
          $sitemap->addChild("lastmod",date('c',time()));
          $sitemap->addChild("changefreq","hourly");
          $sitemap->addChild("priority",0.9);

          $xml->asXML($file);
        }
      }
      $file = realpath(__DIR__ . '/../../../public/sitemap/sitemap-index.xml');

      $xml = simplexml_load_file($file);

      $sitemaps = $xml;
      $sitemap = $sitemaps->addChild('sitemap');
      $sitemap->addChild("loc","https://admin.codigopostal.com/sitemap/sections/sitemap-sections.xml");
      $sitemap->addChild("lastmod",date('c',time()));

      $xml->asXML($file);

      //TODO: AQUI SE HACE LAS NOTAS DE CONTENIDO ORIGINAL

      $filename = "sitemap-article-content-original.xml";
      if(!file_exists("sitemap/".$filename)){
        $xml = new \DOMDocument();
        $xmlUrlSet = $xml->createElement("urlset");
        $xmlUrlSet->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
        $xmlUrlSet->setAttribute("xmlns:news","http://www.google.com/schemas/sitemap-news/0.9");
        $xml->appendChild( $xmlUrlSet );

        $xml->save('public/sitemap/sitemap-article-content-original.xml');
      }

      $newsContentOriginal = \DB::select("SELECT * FROM news where id_status_news = 1 and url != '' and id_cp is not null and id_author = 100 and imported = 0 order by created_at desc limit 20");

      if(count($newsContentOriginal) > 0){
        foreach ($newsContentOriginal as $key => $value) {
          $file = realpath(__DIR__ . '/../../../public/sitemap/sitemap-article-content-original.xml');
          $xml = simplexml_load_file($file);
          $sitemaps = $xml;

          $sitemap = $sitemaps->addChild('url');
          $sitemap->addChild("loc","https://codigopostal.com".$value->url);

          $google_news = $sitemap->addChild('news:news:news');
          $google_news_pub = $google_news->addChild('news:news:publication');
          $google_news_pub->addChild('news:news:name', "CodigoPostal.com");
          $google_news_pub->addChild('news:news:language', "es");

          $google_news->addChild('news:news:publication_date', date('c', strtotime($value->created_at)));
          $google_news->addChild('news:news:title', $value->title);

          $sitemap->addChild("lastmod",date('c',time()));
          $sitemap->addChild("changefreq","hourly");
          $sitemap->addChild("priority",0.9);

          $xml->asXML($file);
        }
      }
      $file = realpath(__DIR__ . '/../../../public/sitemap/sitemap-index.xml');

      $xml = simplexml_load_file($file);

      $sitemaps = $xml;
      $sitemap = $sitemaps->addChild('sitemap');
      $sitemap->addChild("loc","https://admin.codigopostal.com/sitemap/sitemap-article-content-original.xml");
      $sitemap->addChild("lastmod",date('c',time()));

      $xml->asXML($file);
    }

    public function slugify($text)
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
}
