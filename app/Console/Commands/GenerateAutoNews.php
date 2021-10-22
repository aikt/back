<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aws;


class GenerateAutoNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new:autonews';

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

    public $decesos_mas = 0;

    public function __construct()
    {
        parent:: __construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = rand( 1, 4 );

        $this->GenerateNewByType( $type );
    }

    /**
     *
     */
    public function GenerateNewByType( int $type )
    {
        try
        {
            $sql_estado = \DB::select("SELECT cpe.*,
                                    cp.d_codigo,
                                    DATE(cpe.created_at) AS info_date ,
                                    cp.d_estado, cp.id AS id_municipio

                                    FROM coronavirus_per_state AS cpe
                                    INNER JOIN  postal_codes AS cp
                                    ON    cpe.id_cp            = cp.id
                                    -- WHERE DATE(cpe.created_at) = DATE(NOW() - INTERVAL 1 DAY)
                                    GROUP BY cp.d_estado;");

            $sql_country = \DB::select("SELECT * FROM covid ORDER BY created_at DESC LIMIT 1");

            $total_deaths = (isset($sql_country[0]->muertes)) ? number_format($sql_country[0]->muertes) : "";
            $total_cases  = (isset($sql_country[0]->casos)) ? number_format($sql_country[0]->casos) : "";

            foreach($sql_estado as $values)
            {

                $porcentaje      = null;
                $porcentaje_pais = null;

                switch ($type)
                {
                    case 1:

                        $title        = "El estado de {estado} suma ya {total_casos} casos de coronavirus al {fecha_actual}";
                        $summary      = "En codigopostal.com nos interesa que estés enterado de la evolución del coronavirus en tu estado, es por eso que te presentamos un resumen hasta el día de hoy: ";
                        $curdate      = date("Y-m-d H:i:s");
                        $fecha_parsed = self::fechaParsed($curdate);

                        $content = '<p>El estado de {estado} suma ya un total de <strong>{casos_totales} casos confirmados de coronavirus</strong>
                                    luego de que ayer se registraran <strong>{casos_por_dia} contagios</strong> m&aacute;s seg&uacute;n cifras oficiales.</p>
                                    <p>La entidad cuenta con una poblaci&oacute;n de <strong>{habitantes_totales}</strong> de habitantes y el
                                    n&uacute;mero de infectados representa el <strong>{porcentaje} por ciento</strong> del total.</p>
                                    <p>Lamentablemente en {estado} han fallecido hasta el momento<strong> {muertes_totales} personas</strong> y durante ayer ocurrieron un total de
                                    <strong> {decesos_mas} decesos m&aacute;s</strong>.</p><p>Actualmente <strong>{estado}</strong> est&aacute; en:
                                    <span style = "background-color: {color}; color: #000000; border-radius: 15px; padding: 10px;">{semaforo}</span></p>
                                    <p>[MENSAJE_SUSCRIPCION]</p>';

                        $total_str        = str_replace(',', '', $values->total_cases);
                        $total_num        = intval($total_str);
                        $habitantes_str   = str_replace(',', '', $values->poblation);
                        $total_habitantes = intval($habitantes_str);

                        $porcentaje = self::porcentaje($total_habitantes, $total_num);

                        $deaths_before_yesterday = \DB::select("SELECT cpe.deaths
                                                                FROM coronavirus_per_state AS cpe
                                                                INNER JOIN  postal_codes AS cp
                                                                ON    cpe.id_cp            = cp.id
                                                                WHERE DATE(cpe.created_at) = DATE(NOW() - INTERVAL 2 DAY)
                                                                AND cp.d_estado LIKE '$values->d_estado%'
                                                                GROUP BY cp.d_estado;");

                        if(!empty($deaths_before_yesterday))
                        {
                            $this->decesos_mas = 0;

                            $deaths_yesterday         = self::string_to_number($values->deaths);
                            $deaths_before_yesterday_ = self::string_to_number($deaths_before_yesterday[0]->deaths);

                            $this->decesos_mas = $deaths_yesterday - $deaths_before_yesterday_;
                        }

                        break;

                    case 2:
                        $title   = "{estado} suma ya {total_casos} casos y {muertes_estado} muertos por coronavirus al dia {fecha_actual}";
                        $summary = "La pandemia de coronavirus ha cobrado ya la vida de {muertes_pais} de personas en el país y el estado de {estado} ha contribuido con {muertes_estado} al total de personas que lamentablemente perdieron la vida.";

                        $content = '<p style="border-bottom:1px solid #89EE85;opacity:1;text-align:center;margin-botton:15px 0;padding:0;color:#757575;font-family:Helvetica;font-size:16px;line-height:150%"> </p>
                                    <p>El 28 de febrero de este a&ntilde;o M&eacute;xico registr&oacute; su primer caso de infecci&oacute;n por COVID-19 y desde ese momento a la fecha el pa&iacute;s ya presenta un total de <strong>{total_casos_pais}</strong> enfermos de acuerdo con datos de las autoridades sanitarias.</p>';

                        $sql_get_five_by_state = \DB::select("SELECT
                                                            CAST(REPLACE(cpe.total_cases, ',', '') AS UNSIGNED) AS total_cases,
                                                            DATE(cpe.created_at) fecha
                                                            FROM coronavirus_per_state AS cpe
                                                            INNER JOIN postal_codes AS cp
                                                            ON cpe.id_cp = cp.id
                                                            WHERE d_estado LIKE '$values->d_estado%'
                                                            GROUP BY cpe.created_at
                                                            ORDER BY total_cases DESC LIMIT 5;");

                        $counter = count($sql_get_five_by_state);

                        $content .= '<p>En el estado de <strong>{estado}</strong> la evoluci&oacute;n de casos positivos de los &uacute;ltimos ' . $counter .' d&iacute;as ha sido de la siguiente manera:</p>
                                    <table style = "border-collapse: collapse; border: none;">
                                        <tbody>';

                        foreach ($sql_get_five_by_state as $gfbs)
                        {
                            $date_parsed = self::fechaParsed($gfbs->fecha);
                            $content .= '<tr style="border: none;">
                                <td style = "width: 50%; border: none;border-right: 2px solid #B1B1B1;"> <span style = "margin-right:20px"><strong>'. $date_parsed .'</strong></span></td>
                                <td style = "width: 50%; border: none;"> <span style                                 = "margin-left:20px;color:#0000FF">' . number_format($gfbs->total_cases) . '</span></td>


                            </tr>';
                        }

                        $content .= '</tbody>
                                    </table>
                                    <p>&nbsp;</p>
                                    [ADMEDIUM1]
                                    <p>[MENSAJE_SUSCRIPCION]</p>
                                    <p>&nbsp;</p>
                                    <p>&nbsp;</p>
                                    <p>Actualmente <strong>{estado}</strong> est&aacute; en sem&aacute;foro  <span style = "background-color: {color}; color: #000000; border-radius: 15px; padding: 10px;"> {semaforo} . </span>&nbsp; &iquest;Qu&eacute; significa cada color?</p>
                                    <p>&nbsp;</p>
                                    <p>[SEMAFORO]</p>';

                        // get top 5 more infected states
                        $sql_top5 = \DB::select("SELECT
                                                CAST(REPLACE(cpe.total_cases, ',', '') AS UNSIGNED) AS total_cases,
                                                d_estado
                                                FROM coronavirus_per_state AS cpe
                                                INNER JOIN postal_codes AS cp
                                                ON    cpe.id_cp        = cp.id
                                                WHERE DATE(created_at) = SUBDATE(CURDATE(), 1)
                                                GROUP BY d_estado
                                                -- WHERE DATE(created_at) = SUBDATE(CURDATE(), 1)
                                                ORDER BY total_cases DESC LIMIT 5;");

                        $content .= '<p>Hasta ahora los cinco estados con m&aacute;s personas infectadas son:</p><p style="border-bottom:1px solid #89EE85;opacity:1;text-align:center;margin-top:15px 0;padding:0;color:#757575;font-family:Helvetica;font-size:16px;line-height:150%"> </p>
                                    <p>&nbsp;</p>
                                    <table style = "border-collapse: collapse;border: none;" border = "1">
                                    <tbody>
                                    <tr style = "border: none;margin-bottom:20px">
                                    <td style = "width: 50%; border: none;">Total de casos por estado</td>
                                    </tr>';

                        foreach ($sql_top5 as $total)
                        {
                            $content .= '<tr style="border: none;">
                                <td style = "width: 50%; border: none;border-right: 2px solid #B1B1B1;"> <strong>'. $total->d_estado .'</strong></td>
                                <td style = "width: 50%; border: none;"> <span style = "margin-left:20px;color:#0000FF"><strong>' . number_format($total->total_cases) . '</strong></span></td>
                            </tr>';
                        }

                        $content .= '</tbody>
                                    </table>
                                    <p>Para conocer estad&iacute;sticas, recomendaciones y anuncios del gobierno sobre la pandemia de la COVID-19, te invitamos a consultar el sitio oficial: <a href = "http://www.coronavirus.gob.mx/" target = "_blank" rel = "noopener" data-saferedirecturl = "https://www.google.com/url?q=http://www.coronavirus.gob.mx/&amp;source=gmail&amp;ust=1608134903812000&amp;usg=AFQjCNF0xVwmUaGiMLv8ODIt17L34Z7DjQ">www.coronavirus.gob.mx</a>, que entre otras cosas cuenta con informaci&oacute;n de contacto y medidas de prevenci&oacute;n.</p>
                                    [ADMEDIUM2]';

                        break;
                    case 3:
                        $title = "{estado} suma ya {muertes_estado} muertos por coronavirus al {fecha_actual}";

                        $summary = "Con el proceso de vacunación en marcha para contrarrestar "
                                    . "y controlar el coronavirus, además de proteger a la población, "
                                    . "el estado de {estado} informó que ya suma un "
                                    . "total de {total_casos} casos con "
                                    . "{muertes_estado} fallecidos desafortunadamente. ";

                        $content = '<p style="border-bottom:1px solid #89EE85;opacity:1;text-align:center;margin-botton:15px 0;padding:0;color:#757575;font-family:Helvetica;font-size:16px;line-height:150%"> </p>
                                    <p>  &nbsp; </p>
                                    <p>Tan solo el día de ayer se presentaron <strong>{casos_por_dia} casos nuevos</strong>  y según los reportes oficiales murieron <strong>{muertes_totales}</strong> tras la jornada de este {fecha_actual}</p>
                                    <p>Las autoridades del estado han informado y solicitado a la población que se mantenga en sus casas y que salgan solo por lo indispensable. Actualmente se vive la segunda ola de contagios en el país y por ello las instituciones sanitarias están presentando altos índices de hospitalización.</p>
                                    <p>De acuerdo al gobierno federal, se cuenta con una estrategia en cinco etapas de vacunación en las que se distribuirán las poblaciones a ser vacunadas, así como las diferentes dosis con las que contará el programa de vacunación contra el virus SARS-CoV-2 para la prevención de la COVID-19. </p>
                                    <p>  &nbsp; </p>
                                    <p style = "border-bottom:1px solid #89EE85;opacity:1;text-align:center;margin-botton:15px 0;padding:0;color:#757575;font-family:Helvetica;font-size:16px;line-height:150%"> </p>
                                    <p>  &nbsp; </p>
                                    <p>[MENSAJE_SUSCRIPCION]</p>
                                    <p>  &nbsp; </p>
                                    <h3> <strong>FASES DE VACUNACIÓN</strong> <h3/>
                                    <p>  &nbsp; </p>
                                    <h4>Etapa 1</h4>
                                    <p style = "color:#1B70EE">Diciembre 2020-Febrero 2021<p/>
                                    <p>Personal de salud de primera línea de control de la COVID-19<p/>
                                    <p>  &nbsp; </p>
                                    <h4>Etapa 2</h4>
                                    <p style = "color:#1B70EE">Febrero-Abril 2021<p/>
                                    <p>Personal de salud restante y personas de 60 y más años<p/>
                                    <p>  &nbsp; </p>
                                    <h4>Etapa 3</h4>
                                    <p style = "color:#1B70EE">Abril-Mayo 2021<p/>
                                    <p>Personas de 50 a 59 años<p/>
                                    <p>  &nbsp; </p>
                                    <h4>Etapa 4</h4>
                                    <p style = "color:#1B70EE">Mayo-Junio 2021<p/>
                                    <p>Personas de 40 a 49 años<p/>
                                    <p>  &nbsp; </p>
                                    <h4>Etapa 5</h4>
                                    <p style = "color:#1B70EE">Junio 2021-Marzo 2022<p/>
                                    <p>Resto de la población<p/>
                                    <p>  &nbsp; </p>
                                    <p>Hasta el día de ayer, en México ya eran un total de <strong>{total_casos_pais}</strong>  infectados por el virus de COVID-19 y <strong>{muertes_pais}</strong> han fallecido.</p>';

                        break;
                    case 4:

                        $total_str_estado = str_replace(',', '', $values->total_cases);
                        $total_estado     = intval($total_str_estado);
                        $total_str_pais   = str_replace(',', '', $total_cases);
                        $total_pais       = intval($total_str_pais);

                        $porcentaje_pais = self::porcentaje($total_pais, $total_estado);

                        $deaths_before_yesterday = \DB::select("SELECT cpe.deaths
                                                                FROM coronavirus_per_state AS cpe
                                                                INNER JOIN  postal_codes AS cp
                                                                ON    cpe.id_cp            = cp.id
                                                                WHERE DATE(cpe.created_at) = DATE(NOW() - INTERVAL 2 DAY)
                                                                AND cp.d_estado LIKE '$values->d_estado%'
                                                                GROUP BY cp.d_estado;");

                        if(!empty($deaths_before_yesterday))
                        {
                            $this->decesos_mas = 0;

                            $deaths_yesterday         = self::string_to_number($values->deaths);
                            $deaths_before_yesterday_ = self::string_to_number($deaths_before_yesterday[0]->deaths);

                            $this->decesos_mas = $deaths_yesterday - $deaths_before_yesterday_;
                        }


                        $title = "{estado} suma ya {muertes_estado} muertos por coronavirus al {fecha_actual}";

                        $summary = 'El estado de {estado} representa el {porcentaje_pais}% de los casos positivos de coronavirus hasta el momento de acuerdo con los reportes de las autoridades sanitarias.';

                        $content = '<p style="border-bottom:1px solid #89EE85;opacity:1;text-align:center;margin-botton:15px 0;padding:0;color:#757575;font-family:Helvetica;font-size:16px;line-height:150%"> </p>
                                    <p>  &nbsp; </p>
                                    <p>
                                    Tan solo el día de ayer se presentaron {casos_por_dia} casos positivos de COVID-19 más y {decesos_mas} muertes
                                    desafortunadamente. Hasta lo que llevamos de la pandemia, que inició en México en el mes de marzo, {estado}
                                    ha presentado un total de {casos_totales} casos y  {muertes_totales} fallecieron por esta causa.
                                    </p>
                                    <p>
                                    El gobierno de México dio a conocer que para el periodo de finales del 2020 hasta el primer trimestre de 2022, firmó órdenes de compra por 198 millones de vacunas que se aplican en primera instancia al personal médico que está atendiendo la pandemia en los hospitales y centros de salud.
                                    </p>
                                    <p>  &nbsp; </p>
                                    <p style = "border-bottom:1px solid #89EE85;opacity:1;text-align:center;margin-botton:15px 0;padding:0;color:#757575;font-family:Helvetica;font-size:16px;line-height:150%"> </p>
                                    <p>  &nbsp; </p>
                                    <p>[MENSAJE_SUSCRIPCION]</p>
                                    <p>  &nbsp; </p>
                                    <h3> <strong>FASES DE VACUNACIÓN</strong> <h3/>
                                    <p>  &nbsp; </p>
                                    <h4>Etapa 1</h4>
                                    <p style = "color:#1B70EE">Diciembre 2020-Febrero 2021<p/>
                                    <p>Personal de salud de primera línea de control de la COVID-19<p/>
                                    <p>  &nbsp; </p>
                                    <h4>Etapa 2</h4>
                                    <p style = "color:#1B70EE">Febrero-Abril 2021<p/>
                                    <p>Personal de salud restante y personas de 60 y más años<p/>
                                    <p>  &nbsp; </p>
                                    <h4>Etapa 3</h4>
                                    <p style = "color:#1B70EE">Abril-Mayo 2021<p/>
                                    <p>Personas de 50 a 59 años<p/>
                                    <p>  &nbsp; </p>
                                    <h4>Etapa 4</h4>
                                    <p style = "color:#1B70EE">Mayo-Junio 2021<p/>
                                    <p>Personas de 40 a 49 años<p/>
                                    <p>  &nbsp; </p>
                                    <h4>Etapa 5</h4>
                                    <p style = "color:#1B70EE">Junio 2021-Marzo 2022<p/>
                                    <p>Resto de la población<p/>
                                    <p>  &nbsp; </p>
                                    <p>Hasta el día de ayer, en México ya eran un total de <strong>{total_casos_pais}</strong>  infectados por el virus de COVID-19 y <strong>{muertes_pais}</strong> han fallecido.</p>';

                        break;
                    default:
                        echo('Nada por hacer :(');
                        break;
                }

                $curdate      = date("Y-m-d H:i:s");
                $fecha_parsed = self::fechaParsed($curdate);

                $paterns_t_s = array(
                    '{estado}',
                    '{total_casos}',
                    '{fecha_actual}',
                    '{muertes_estado}',
                    '{muertes_pais}',
                    '{nuevos_casos}',
                    '{porcentaje_pais}',
                    '{decesos_mas}',
                );

                $replacements_t_s = array(
                    $values->d_estado,
                    $values->total_cases,
                    $fecha_parsed,
                    $values->deaths,
                    $total_deaths,
                    $values->new_cases_per_day,
                    $porcentaje_pais,
                    $this->decesos_mas,
                );

                $ftitle = str_replace($paterns_t_s, $replacements_t_s, $title);
                // echo("\n\nTITLEEEEE->" . $ftitle . "\n\n");
                $fsummary = str_replace($paterns_t_s, $replacements_t_s, $summary);
                // echo("\n\nSUMARIOOOO->" . $fsummary . "\n\n");

                $patterns_content = array(
                    '{estado}',
                    '{casos_totales}',
                    '{casos_por_dia}',
                    '{habitantes_totales}',
                    '{porcentaje}',
                    '{muertes_totales}',
                    '{decesos_mas}',
                    '{color}',
                    '{semaforo}',
                    '{total_casos_pais}',
                    '{muertes_pais}',
                    '{fecha_actual}',
                );

                // color semaforo
                switch ($values->tipo_semaforo)
                {
                    case 1:
                        $color    = "#89EE85";
                        $semaforo = "SEMÁFORO VERDE";
                        break;
                    case 2:
                        $color    = "#FFD500";
                        $semaforo = "SEMÁFORO AMARILLO";
                        break;
                    case 3:
                        $color    = "#FF8000";
                        $semaforo = "SEMÁFORO NARANJA";
                        break;
                    case 4:
                        $color    = "#FF004C";
                        $semaforo = "SEMÁFORO ROJO";
                        break;
                    default:
                        $color    = "";
                        $semaforo = "";
                        break;
                }

                $replaced_content = array(
                    $values->d_estado,
                    $values->total_cases,
                    $values->new_cases_per_day,
                    $values->poblation,
                    $porcentaje,
                    $values->deaths,
                    $this->decesos_mas,
                    $color,
                    $semaforo,
                    $total_cases,
                    $total_deaths,
                    $fecha_parsed,
                );

                $content = str_replace($patterns_content, $replaced_content, $content);

                $cur_date_img = date('Ymd');
                $state        = self::slugify($values->d_estado);
                $prefix       = 'https://codigopostal.com/img/covid/covid/imagen/img-covid-';
                $url_img      = $prefix . $state . $cur_date_img . '.png';

                // upload image to s3
                $get_url_image = self::getImageFromAWSByUriImageExternal($url_img);
                if($get_url_image !== false)
                {
                    $url_img = $get_url_image['relative_path'];
                }

                $cp           = $values->d_codigo;
                $title_slug   = self::slugify($ftitle);
                $url          = '/' . $state . '/' . $cp . '/' . $title_slug;


                $insert_news = \DB::insert("INSERT INTO `news`(`id_cp`,`title`,`url`,`summary`,`content`,`created_at`,`updated_at`,
                                                                `image`,`seo_title`,`seo_description`,`seo_keywords`,`id_status_news`,`imported`,`title_normalizado`,
                                                                `id_editor`,`url_canonical`,`id_author`,`id_position`,`id_copo_id`,`id_state`,`model_feed`,`caption`)
                                            VALUES ($values->id_cp, '$ftitle','$url',
                                            '$fsummary',
                                            '$content','$curdate', '$curdate', '$url_img', '$ftitle', '$ftitle', '$ftitle',7, 0, '$title_slug', NULL, NULL,100,NULL,
                                            NULL, $values->id_cp, 0, NULL);");
                echo("INSERTADO EN : " . $values->d_estado . "\n");
            }
        }
        catch (Exception $e)
        {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }

    }


    private static function slugify($text)
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

    private static function fechaParsed($fecha)
	{
		$fecha     = substr($fecha, 0, 10);
		$numeroDia = date('d', strtotime($fecha));
		$dia       = date('l', strtotime($fecha));
		$mes       = date('F', strtotime($fecha));
		$anio      = date('Y', strtotime($fecha));
		$dias_ES   = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
		$dias_EN   = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
		$nombredia = str_replace($dias_EN, $dias_ES, $dia);
		$meses_ES  = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		$meses_EN  = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		$nombreMes = str_replace($meses_EN, $meses_ES, $mes);
		return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
    }

    private static function porcentaje($total, $parte, $redondear = 2)
    {
        return round($parte / $total * 100, $redondear);
    }

    private static function string_to_number(string $string)
    {
        $total_str = str_replace(',', '', $string);
        return intval($total_str);
    }


    private static function getImageFromAWSByUriImageExternal($uriImageExternal){
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
            $img_up = "img/covid/" . $nameImage;

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

}
