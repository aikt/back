<?php

namespace App\Admin\Controllers;


use App\News;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use Encore\Admin\Widgets\Table;



class DataCopoController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Reporte DataCopo';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $usuarios_totales = \DB::select("SELECT COUNT(*) AS total_usuarios FROM users;");
        $news_totales = \DB::select("SELECT COUNT(*) AS total_news FROM news;");
        $new_pub_totales = \DB::select("SELECT count(*) as total_news_pub FROM `news` WHERE id_status_news = 1 AND url is not null;");
        $new_ml_total = \DB::select("SELECT COUNT(*) AS total_ml  FROM `news` WHERE id_editor = 20");
        $news_semitag_total = \DB::select("SELECT COUNT(*) AS total_semitag FROM `news` WHERE id_status_news = 5;");
        $copos_total = \DB::select("SELECT COUNT(*) AS total_copos FROM `copos`;");
        $feeds_total = \DB::select("SELECT COUNT(*) AS total_feeds FROM `feed`;");
        $feeds_newstotal = \DB::select("SELECT COUNT(*) AS total_noticias_feeds FROM `news` WHERE 
                                        id_author IN (44, 39, 43, 31, 43, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70) 
                                        AND id_status_news = 1;");
        $megusta_total = \DB::select("SELECT COUNT(*) total_likes FROM `read_like` WHERE type = 2;");
        $leerdespues_total = \DB::select("SELECT COUNT(*) total_despues FROM `read_like` WHERE type = 1;");


        $semanas_usuarios = \DB::select("SELECT 
                                        IFNULL(COUNT(*), 0) as num_usuarios,
                                        (SELECT COUNT(*) FROM users) as total
                                        FROM users 
                                        WHERE create_at > DATE_SUB(NOW(), INTERVAL 13 WEEK)
                                        GROUP BY WEEK(create_at)
                                        ORDER BY create_at;");

        $semanas_noticias = \DB::select("SELECT 
                                        CONCAT('Semana ',WEEK(created_at)) semanas,
                                        COUNT(*) as num_noticias,
                                        (SELECT COUNT(*) FROM news) as total
                                        FROM news 
                                        WHERE created_at > DATE_SUB(NOW(), INTERVAL 13 WEEK)
                                        GROUP BY WEEK(created_at)
                                        ORDER BY created_at;");

        $semanas_noticias_pub = \DB::select("SELECT 
                                            COUNT(*) as num_noticias_pub,
                                            (SELECT COUNT(*) FROM news) as total
                                            FROM news 
                                            WHERE created_at > DATE_SUB(NOW(), INTERVAL 13 WEEK)
                                            AND id_status_news = 1 AND url is not null
                                            GROUP BY WEEK(created_at)
                                            ORDER BY created_at;");
        
        $news_ml = \DB::select("SELECT COUNT(*) AS total_ml  FROM `news` WHERE id_editor = 20 AND
                                created_at > DATE_SUB(NOW(), INTERVAL 13 WEEK)
                                GROUP BY WEEK(created_at)
                                ORDER BY created_at;");

        $semi_tag = \DB::select("SELECT COUNT(*) AS total_semitag  FROM `news` WHERE id_status_news = 5 AND updated_at  > DATE_SUB(NOW(), INTERVAL 13 WEEK)
                                GROUP BY WEEK(updated_at)
                                ORDER BY updated_at;");

        $copos_sem = \DB::select("SELECT COUNT(*) AS total_copos FROM `copos` WHERE created_at > DATE_SUB(NOW(), INTERVAL 13 WEEK)
                                GROUP BY WEEK(created_at)
                                ORDER BY created_at;");

        $feed_news = \DB::select("SELECT COUNT(*) AS total_noticias_feeds FROM `news` WHERE 
                                id_author IN (44, 39, 43, 31, 43, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70) 
                                AND id_status_news = 1
                                AND created_at > DATE_SUB(NOW(), INTERVAL 13 WEEK)
                                GROUP BY WEEK(created_at)
                                ORDER BY created_at;");
        

        $headers = ['Descripción', 'Total Acumulado'];

        $rows = 
        [
            ['Usuarios Nuevos'],
            ['Noticias'],
            ['Noticias Publicadas'],
            ['Machine Learning'],
            ['Semi Taggeadas'],
            ['Copos Activos'],
            ['Feeds'],
            ['Noticias / Feeds'],
            ['Me gusta'],
            ['Leer después'],
        ];

        array_push($rows[0], $usuarios_totales[0]->total_usuarios);
        array_push($rows[1], $news_totales[0]->total_news);        
        array_push($rows[2], $new_pub_totales[0]->total_news_pub);        
        array_push($rows[3], $new_ml_total[0]->total_ml); 
        array_push($rows[4], $news_semitag_total[0]->total_semitag); 
        array_push($rows[5], $copos_total[0]->total_copos); 
        array_push($rows[6], $feeds_total[0]->total_feeds); 
        array_push($rows[7], $feeds_newstotal[0]->total_noticias_feeds); 
        array_push($rows[8], $megusta_total[0]->total_likes); 
        array_push($rows[9], $leerdespues_total[0]->total_despues); 


        $styles = [
            'background-color' => 'red'
        ];

        foreach ($semanas_noticias as $value) 
        {
            array_push($headers, $value->semanas);
            array_push($rows[1], $value->num_noticias);
        }
        foreach ($semanas_usuarios as $value) 
        {
            array_push($rows[0], $value->num_usuarios);
        }
        foreach ($semanas_noticias_pub as $value) 
        {
            array_push($rows[2], $value->num_noticias_pub);
        }
        foreach ($news_ml as $value) 
        {
            array_push($rows[3], $value->total_ml);
        }
        foreach ($semi_tag as $value) 
        {
            array_push($rows[4], $value->total_semitag);
        }
       
        foreach ($copos_sem as $value) 
        {
            array_push($rows[5], $value->total_copos);
        }

        foreach ($feed_news as $value) 
        {
            array_push($rows[7], $value->total_noticias_feeds);
        }

        $table = new Table($headers, $rows, $styles);

        return $table->render();
    }
}
