<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateEmbed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:embed';

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
     * @return int
     */
    public function handle()
    {
        $states = [ 36354, 95644, 1 ];
        $states_comma = implode(",", $states);

        $states = \DB::select("SELECT d_estado, id FROM postal_codes WHERE id IN ($states_comma)");

        foreach($states as $state)
        {
            $state_name = $state->d_estado;

            $query_cars = \DB::select("SELECT DISTINCT ac.*, cp.id AS codigo_estado  FROM announcementCar AS ac
                                    INNER JOIN postal_codes AS cp
                                    ON ac.idMunicipio = cp.id
                                    WHERE cp.d_estado LIKE '$state_name%'
                                    AND ac.subUrl NOT LIKE '%quereta%'
                                    AND ac.subUrl NOT LIKE '%qro%'
                                    ORDER BY ac.create_at DESC LIMIT 3 OFFSET 15");
            $i = 0;
            $html = '<! -- EMBED PARA CLASIFICADOS MAILCHIMP '. $state_name .' --> <p style="font-family:roboto,helvetica neue,helvetica,arial,sans-serif;font-size:20px;font-weight:bold;display:block;color: #B4B4B4"><span style="display:block;margin-left:15px">CLASIFICADOS</span></p>';

            foreach ($query_cars as $val)
            {
                if($i < 2)
                {
                    $i++;
                    $html .= '<div style="margin-top:15px">
                        <a href="{url}" title="" style="text-decoration: none;" target="_blank">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tbody>
                                    <tr>
                                        <td style="width: 2.5%;"></td>

                                        <td style="width: 48.5%;">
                                            <img alt=""  src="{url_imagen}" style=" width: 100%;
                                            max-width: 190px;
                                            height: auto;border: 0; height: auto; outline: none; text-decoration: none;" />
                                        </td>

                                        <td style="width: 1.5%;"></td>

                                        <td style="width: 47.5%;text-align:left" valign="top">
                                        <p style="font-family:&#39;Roboto&#39;,helvetica neue,helvetica,arial,sans-serif;display:block;margin:10px 0;padding:0">
                                            <span style="display:block;background: #ECECEC 0% 0% no-repeat padding-box;color:#000000;font-size:10px;padding:10px;border-radius:5px;width:50%;margin-top-15px">
                                                Venta de Vehículos
                                            </span>
                                            <span style="display: block; margin-top: 10px; font-weight: bold; color: #000000; font-size: 14px; margin-top: 10px; margin-bottom: 20px;">
                                                {titulo}
                                            </span>
                                            <span style="display: block; margin-top: 10px; font-weight: bold; color: #ff002a; font-size: 14px; margin-top: 10px;">
                                                {precio}
                                            </span>
                                            <span style="display: block; margin-top: 10px; color: #000000; font-size: 12px; margin-top: 10px;">
                                                Kilometraje:  {kilometraje}
                                            </span>
                                        </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </a>
                        </div>';

                    $patterns = array(
                        '{url}',
                        '{url_imagen}',
                        '{precio}',
                        '{kilometraje}',
                        '{titulo}',
                    );

                    $replacements = array(
                        $val->subUrl,
                        $val->image,
                        $val->price,
                        number_format($val->mileage),
                        $val->title,
                    );

                    $html = str_replace($patterns, $replacements, $html);
                    $str = preg_replace("/(\/[^>]*>)([^<]*)(<)/","\\1\\3",$html);
                }

                $id_cp = $val->codigo_estado;
            }

            // print_r($str);

            echo("INSERTADO CLASIFICADOS EN: {$val->codigo_estado} \n");
            $curdate      = date("Y-m-d H:i:s");
            $insert_news = \DB::insert("INSERT INTO `embeds`(`embed`, `id_cp`, `type` , `created_at`,`updated_at`)
                            VALUES ('$str' , $id_cp, 0 , '$curdate', '$curdate');");

            $query_jobs = \DB::select("SELECT j.*,cp.id AS codigo_estado FROM announcementJob AS j
                                    INNER JOIN  postal_codes AS cp
                                    ON j.id_cp = cp.id
                                    WHERE cp.d_estado LIKE '$state_name%'
                                    AND DATE(created_at) > '2021-01-01'
                                    GROUP BY j.title, j.location
                                    ORDER BY j.created_at LIMIT 3;");
            $j = 0;
            $html_jobs = '<! -- EMBED PARA EMPLEOS MAILCHIMP ' . $state_name .' --> <p style="font-family: roboto, helvetica neue, helvetica, arial, sans-serif; font-size: 16px; font-weight: bold; display: block; color: #b4b4b4; margin: 10px 0; padding: 0;"><span style="display: block; margin-left: 15px;">EMPLEOS</span></p>';

            foreach ($query_jobs as $value)
            {
                if($j < 3)
                {
                    $j++;
                    $html_jobs .= '<div style="margin-top:15px">
                                <a href="{url}" title="" style="text-decoration: none;" target="_blank">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 2.5%;"></td>
                                                <td style="width: 10%; ">
                                                    <img
                                                        style="width: 30px; height: 30px;outline: #b1b1b1 solid 1.5px;padding:5px;text-decoration: none;"
                                                        src="https://ci4.googleusercontent.com/proxy/hP0HIzBCPcbV2b3mO6mj7SxVXFolIKS-By0QfXxhEnpKKONHNfZbXf1v5lRfH-fqqf2VQAjcmoLHaWOkE7qanKyND_Vrth6GDiLWm4oKoVAyhqjAkJrYnrzVEOkomQyH15vc5DFC29j2YCV-DAulr0WjjUVMHw=s0-d-e1-ft#https://mcusercontent.com/4831adaceeb0fba498736e7af/images/1b2a0131-ed37-4ebb-81f1-2ac9223d005a.png"
                                                        alt=""
                                                    />
                                                </td>
                                                <td style="width: 1%;"></td>
                                                <td style="width: 86.5%;text-align: left;margin-left:20px">
                                                <p style="font-family: roboto, helvetica neue, helvetica, arial, sans-serif;">
                                                    <span style="display: block; font-size: 100%; color: #1b70ee; font-weight: bold;">
                                                        {titulo}
                                                    </span>
                                                    <span style="display: block; font-size: 90%; color: #000000; margin-top: 2px;">
                                                        {descp}
                                                    </span>
                                                    <span style="display: block; font-size: 90% color: #1b70ee; margin-top: 2px;text-decoration: none;">
                                                        {sueldo}
                                                    </span>
                                                </p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </a>
                            </div>';

                    $patterns = array(
                        '{url}',
                        '{titulo}',
                        '{descp}',
                        '{sueldo}',
                    );

                    $descp = $value->company . ' - ' . $value->location;

                    $replacements = array(
                        $value->url,
                        $value->title,
                        $descp,
                        $value->salary,
                    );

                    $html_jobs = str_replace($patterns, $replacements, $html_jobs);
                    $str_jobs = preg_replace("/(\/[^>]*>)([^<]*)(<)/","\\1\\3",$html_jobs);
                    $str_jobs = preg_replace( "/\r|\n/", "", $str_jobs );

                }

                $id_cp = $value->codigo_estado;
            }
            // print_r($str_jobs);

            echo("INSERTADO EMPLEOS EN: {$value->codigo_estado} \n");
            $curdate      = date("Y-m-d H:i:s");
            $insert_news = \DB::insert("INSERT INTO `embeds`(`embed`, `id_cp`, `type` , `created_at`,`updated_at`)
                            VALUES ('$str_jobs' , $id_cp, 1 , '$curdate', '$curdate');");
        }
    }
}
