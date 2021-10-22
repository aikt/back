<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

date_default_timezone_set('America/Mexico_City');

class GenerateJobsNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new:autojobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para generar noticias acerca de vacantes';

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
        #genera un random para crear un tipo de nota
        $type = rand( 1, 4 );
        $this->GenerateNewsJobs( $type );
    }

    /**
     * @param: int tipo de nota del 1 al 4
     * 
     * Genera un tipo de nota especifica por cada 
     * autor : JuanJo Rodríguez
     */
    public function GenerateNewsJobs( int $type )
    {
        try
        {
            #obtiene los estados
            $sql_estado = \DB::select("SELECT * FROM postal_codes GROUP BY d_estado;");

            #itera sobre cada estado
            foreach ($sql_estado as $values)
            {
                #selecciona de la tabla de empleos los registros 
                #por estado 
                $sql_jobs = \DB::select("SELECT DISTINCT aj.*
                                        FROM announcementJob AS aj
                                        INNER JOIN postal_codes AS cp
                                        ON aj.id_cp = cp.id
                                        WHERE cp.d_estado LIKE '$values->d_estado%'
                                        AND MONTH(created_at) = 3
                                        GROUP BY aj.title, aj.location
                                        ORDER BY aj.created_at DESC
                                        LIMIT 0,10");

                $total_jobs = count($sql_jobs);

                if($total_jobs > 0)
                {
                    # dependiendo del tipo que se le pasó a la funcion se genera 
                    #un tipo de nota
                    switch ($type)
                    {
                        #textos genericos 
                        case 1:
                            $title = "¿Buscas trabajo en {estado}? Estas son las mejores vacantes al {fecha_actual}";
                            $summary = "En este mes se han registrado {count} nuevos empleos en {estado}";
                            $content = "<p>En <strong>C&oacute;digo Postal</strong> queremos ayudarte a encontrar empleo. En &eacute;poca de pandemia, con los recortes de puestos de trabajo o incluso cambiar de sitio por desarrollo profesional son algunas de las necesidades laborales.</p><p>Por eso te presentamos cinco de las mejores opciones que est&aacute;n disponibles en <strong>{estado}</strong>, para que te postules y logres tener de nuevo un empleo o quiz&aacute;s tu primera experiencia o mejorar tus condiciones laborales con una oportunidad mejor.</p><p>&nbsp;</p><p>[MENSAJE_SUSCRIPCION]</p><p>&nbsp;</p><p>Este mes hemos publicado <strong>{count}</strong> oportunidades para trabajar y queremos ayudar a nuestra comunidad a que tenga mejores opciones para desempe&ntilde;arse en un sitio agradable y que cumpla con tus expectativas.</p><p>Las vacantes son:</p>";

                            break;
                        case 2:
                            $title = "¿Buscas trabajo en {estado}? Estas son las {count} mejores vacantes de la semana al día {fecha_actual}";
                            $summary = "En este mes se han registrado {count} nuevos empleos en {estado}";
                            $content = "<p>En <strong>C&oacute;digo Postal</strong> queremos ayudarte a encontrar empleo. En &eacute;poca de pandemia, con los recortes de puestos de trabajo o incluso cambiar de sitio por desarrollo profesional son algunas de las necesidades laborales.</p>
                                        <p>Por eso te presentamos {count} de las mejores opciones que est&aacute;n disponibles en <strong>{estado}</strong>, para que te postules y logres tener de nuevo un empleo, tu primera experiencia o mejorar tus condiciones laborales con una oportunidad mejor.</p>
                                        <p>&nbsp;</p>
                                        <p>¿Qué competencias laborales buscan las empresas? Aquí te pasamos 7 tips que enlista la empresa OCC, Online Carreer Center, La bolsa de trabajo más grande de México.</p>
                                        <p>&nbsp;</p>
                                        <ul>
                                        <li>Negociación y resolución de conflictos</li>
                                        <li>Habilidad para tomar decisiones</li>
                                        <li>Conocimiento de uso de la maquinaria o herramientas (dependiendo de las necesidades de la empresa)</li>
                                        <li>Sentido de la responsabilidad</li>
                                        <li>Puntualidad</li>
                                        <li>Innovación (del sector que sea)</li>
                                        <li>Atención del cliente</li>
                                        </ul>
                                        <p>&nbsp;</p>
                                        <p>[MENSAJE_SUSCRIPCION]</p>
                                        <p>&nbsp;</p>
                                        <p>Esta semana hemos publicado <strong>{count}</strong> oportunidades para trabajar y queremos ayudar a nuestra comunidad a que tenga mejores opciones para desempe&ntilde;arse en un sitio agradable y que cumpla con tus expectativas.</p>
                                        <p>Las vacantes son:</p>";
                            break;
                        case 3:
                            $title = "Las {count} vacantes de trabajo de {estado} que debes revisar al día {fecha_actual}";
                            $summary = "Ya sea porque quieres tener una mejor oportunidad laboral, experimentar un
                                        cambio o porque te quedaste sin empleo en esta pandemia, en Código Postal
                                        compartimos {count} vacantes que hay en {estado} para que postules si te interesan.";

                            $content = "<p>Pero antes de enumerar el listado de las oportunidades laborales, aquí te presentamos cinco tips para que puedas armar y checar detalles importantes para tu CV:</p>
                                        <p></p>
                                        <p></p>
                                        <ul><li>Información adecuada, que sea concisa y lo importante.</li>
                                            <li>Diseño, no desestimes el impacto que pueda ocasionar un CV bien presentado</li>
                                            <li>Adapta tu CV, no mandes el mismo para todas las empresas</li>
                                            <li>Apuesta por la brevedadCV Bilingüe, esta opción ayuda mucho para empresas internacionales</li>
                                        </ul>
                                        <p></p>
                                        <p></p>
                                        <p>Te dejamos las {count} vacantes laborales de esta  semana en {estado}</p>
                                        <p></p>";
                            break;
                        case 4:
                            $title = "Postula en alguna de las {count} vacantes más relevantes de {estado} al día {fecha_actual}";
                            $summary = "Código Postal te presenta las {count} recomendaciones laborales más relevantes de {estado}."
                                        ." Siempre es buen momento para crecer profesionalmente o volver a tener empleo después "
                                        . "de los recortes y despidos ocasionados por la pandemia que ha vivido el país.";
                            $content = "<p>Por ejemplo la pandemia de COVID-19, según datos del IMSS, originó que se perdieran 1.2 millones de empleos, pero siempre hay una oportunidad para tratar de reinsertarse en el campo laboral.</p>
                                        <p></p>
                                        <p>Te dejamos las {count} vacantes laborales de esta semana en {estado}</p>
                                        <p>A continuación te damos ocho tips para las entrevistas de trabajo para que tengas un paso más adelante que el resto de los candidatos:</p>
                                        <p></p><p></p>
                                        <ul>
                                            <li>Infórmate sobre la empresas que te entrevistará, ¿qué hacen?, ¿cómo les ha ido?, ¿qué referencias tienen?</li>
                                            <li>Ser puntual, es el primer detalle que ven los reclutadores antes de siquiera haberte visto.</li>
                                            <li>Escucha bien cada pregunta y responde justamente lo que se te cuestiona</li>
                                            <li>Regula cuando hablas, es decir no contestes monosílabos pero tampoco des un discurso</li>
                                            <li>Sé claro en los logros que has conseguido</li>
                                            <li>Debes tener también definido ¿por qué quieres trabajar allí?</li>
                                            <li>Sé empático y positivo</li>
                                            <li>Sé sincero</li>
                                        </ul>
                                        <p></p><p></p>
                                        <p>Te dejamos las {count} vacantes laborales de esta semana en {estado}</p>
                                        <p></p>";
                            break;
                        default:
                            echo 'Tipo de noticia no reconocida';
                            die();
                            break;
                    }

                    $curdate      = date("Y-m-d H:i:s");
                    $fecha_parsed = self::fechaParsed($curdate);

                    // replace de los campos en titulo y resumen en el content de la nota
                    $patern = array(
                        '{estado}',
                        '{count}',
                        '{fecha_actual}',
                    );

                    $replacement = array(
                        $values->d_estado,
                        $total_jobs,
                        $fecha_parsed
                    );

                    $ftitle = str_replace($patern, $replacement, $title);
                    $fsummary = str_replace($patern, $replacement, $summary);

                    // replace de los campos en contenido
                    $patern_content = array(
                        '{estado}',
                        '{count}',
                    );

                    $replacement_content = array(
                        $values->d_estado,
                        $total_jobs
                    );

                    $content = str_replace($patern_content, $replacement_content, $content);

                    // variables adicionales
                    $cur_date_img = date('Ymd');
                    $state = self::slugify($values->d_estado);
                    $cp = $values->d_codigo;
                    $title_slug = self::slugify($ftitle);
                    $curdate = date("Y-m-d H:i:s");
                    $text_date = self::fechaParsed($curdate);
                    $text_date_slugified = self::slugify($text_date);
                    $url = '/' . $state . '/' . $cp . '/' . $title_slug . '/' . $text_date_slugified;
                    $img = 'images/img-' . $state . $cur_date_img . '.png';

                    $insert_news = \DB::insert("INSERT INTO `news`(`id_cp`,`title`,`url`,`summary`,`content`,`coordinate_x`,`coordinate_y`,
                                                                    `created_at`,`updated_at`,`image`,`seo_title`,`seo_description`,`seo_keywords`,
                                                                    `id_status_news`,`imported`,`title_normalizado`,`id_editor`,`url_canonical`,
                                                                    `id_author`,`id_position`,`id_copo_id`,`id_state`,`model_feed`,`caption`)
                                                VALUES ($values->id, '$ftitle','$url', '$fsummary','$content',0.00, 0.00 ,
                                                        '$curdate','$curdate', '$img', '$ftitle', '$ftitle', '$ftitle',8, 0, '$title_slug',
                                                        NULL, NULL, 100, NULL, NULL, $values->id, 0, NULL);");
                    echo("INSERTADO EN : " . $values->d_estado . "\n");
                }
            }
        }
        catch (Exception $e)
        {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }

    }


    #--------------------------------------------- UTILS ---------------------------------------------#
    /**
     *
     */
    private static function slugify($text)
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

    /**
     *
     */
    private static function fechaParsed($fecha)
	{
		$fecha = substr($fecha, 0, 10);
		$numeroDia = date('d', strtotime($fecha));
		$dia = date('l', strtotime($fecha));
		$mes = date('F', strtotime($fecha));
		$anio = date('Y', strtotime($fecha));
		$dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
		$dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
		$nombredia = str_replace($dias_EN, $dias_ES, $dia);
	  	$meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		$meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		$nombreMes = str_replace($meses_EN, $meses_ES, $mes);
		return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
    }
}