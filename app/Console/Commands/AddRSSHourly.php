<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\DB;


class AddRSSHourly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera el rss  las noticias de AM y CodigoPostal';

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
        //File rss name
        $path = __DIR__ . '/../../../public/rss/';
        $rss_file = 'rss.xml';

        $file = realpath($path . $rss_file);

        if(file_exists($path. $rss_file))
        {
            unlink($file);
            fopen($file, 'w');  
            $url_fixed = "https://codigopostal.com";

            $xml = file_get_contents($file);
            $xml .= "<rss version=\"2.0\">\n" ;
            $xml .= "<channel>\n";
            $xml .= "<title>CódigoPostal RSS</title>\n";
            $xml .= "<description>CódigoPostal: Noticias AM y CódigoPostal</description>\n";
            $xml .= "<link>https://codigopostal.com/</link>\n";

            $info = \DB::select('SELECT title, content, created_at, url, image  FROM news WHERE id_author IN (3,100);');

            foreach($info as $value)
            {
                $xml .= "<item>\n";
                $xml .= "<title>$value->title</title>\n";
                $string = htmlspecialchars(html_entity_decode($value->content),ENT_XML1);;
    
                $xml .= "<description>" . "\n".$string . "\n" . "</description>\n";
                $xml .= "<pubDate>".date('D, d M Y H:i:s',strtotime($value->created_at))." GMT</pubDate>\n";
                $xml .= "<enclosure> $value->image </enclosure>\n";
                $xml .= "<url>". $url_fixed . $value->url . "</url>\n";
                $xml .= "</item>\n";
            }
            $xml .= "</channel>\n";
            $xml .= "</rss>\n";
            file_put_contents($file, $xml);
        }
        else
        {
            fopen($file, 'w');  
        }
    }
}
