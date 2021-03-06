<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aws;

class GenerateNewsDollar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:news:dollar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera las notas del dolar con diferentes templates';

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
      $html = $this->getRenderedHTML("htmls/dollar/down_dollar_2.html");
      $htmlCSS = $this->getRenderedHTML("htmls/dollar/css/down_dollar_css.html");
      $htmlJS = $this->getRenderedHTML("htmls/dollar/javascript/down_dollar_js.html");


      \DB::select("SET lc_time_names = 'es_ES';");

      $resultHistorico = \DB::select('SELECT CONCAT(DATE_FORMAT(created_at,"%W")," ",DATE_FORMAT(created_at,"%d")," de ",DATE_FORMAT(created_at,"%M")," fue de ",FORMAT(dollarBuying, 2)," en compra y ",FORMAT(dollarSelling, 2)," a la venta") as fecha FROM dollar WHERE YEARWEEK(created_at) = YEARWEEK(NOW() - INTERVAL 14 WEEK) order by created_at asc;');

      $resultDollar = \DB::select("SELECT * FROM dollar ORDER BY created_at DESC LIMIT 1");

      $resultMinMaxMonth = \DB::select("SELECT min(dollarMonthPercentage) as minimo,max(dollarMonthPercentage) as maximo FROM dollar WHERE YEARWEEK(created_at) = YEARWEEK(NOW() - INTERVAL 4 WEEK)");

      $resultAverageLastWeek = \DB::select("SELECT FORMAT(AVG(DISTINCT dollar), 2) as promedioSemanal FROM dollar WHERE YEARWEEK(created_at) = YEARWEEK(NOW() - INTERVAL 1 WEEK)");

      $resultAverageLast2Week = \DB::select("SELECT FORMAT(AVG(DISTINCT dollar), 2) as promedioSemanal FROM dollar WHERE YEARWEEK(created_at) = YEARWEEK(NOW() - INTERVAL 2 WEEK)");

      $resultMinMaxBuyingAndSellingLast2Week = \DB::select("SELECT FORMAT(min(dollarBuying), 2) as minimoCompra,FORMAT(max(dollarBuying), 2) as maximoCompra, FORMAT(min(dollarSelling), 2) as minimoVenta, FORMAT(max(dollarSelling), 2)  as maximoVenta  FROM dollar WHERE YEARWEEK(created_at) = YEARWEEK(NOW() - INTERVAL 14 WEEK)");

      $resultBidenMinMaxUp = \DB::select("SELECT min(dollarBidenPercentage) as minimaBiden, max(dollarBidenPercentage) as maximaBiden FROM dollar WHERE dollarBidenPercentage > 0");

      $resultBidenMinMaxDown = \DB::select("SELECT min(dollarBidenPercentage) as minimaBiden, max(dollarBidenPercentage) as maximaBiden FROM dollar WHERE dollarBidenPercentage < 0");

      $resultAMLOMinMaxDown = \DB::select("SELECT min(dollarAMLOPercentage) as minimaAMLO, max(dollarAMLOPercentage) as maximaAMLO FROM dollar WHERE dollarAMLOPercentage < 0");

      $resultAMLOMinMaxUp = \DB::select("SELECT min(dollarAMLOPercentage) as minimaAMLO, max(dollarAMLOPercentage) as maximaAMLO FROM dollar WHERE dollarAMLOPercentage > 0");

      $dollarSelling = number_format(str_replace("-","",$resultDollar[0]->dollarSelling),2,".","");
      $dollarBuying = number_format(str_replace("-","",$resultDollar[0]->dollarBuying),2,".","");
      $dollarYesterday = number_format(str_replace("-","",$resultDollar[0]->dollarWithYesterday),2,".","");
      $dollar = number_format(str_replace("-","",$resultDollar[0]->dollar),2,".","");
      $dollarAverageLastWeek = number_format(abs($resultAverageLastWeek[0]->promedioSemanal),2,".","");
      $dollarAverage2Week = number_format(abs($resultAverageLast2Week[0]->promedioSemanal),2,".","");



      date_default_timezone_set ('America/Mexico_City');
      setlocale(LC_TIME, 'es_ES.UTF-8');

      $ftitle = "";
      $fsummary = "";
      $url = "/guanajuato/guanajuato/36000/";
      $content = "";
      $curdate = date("Y-m-d H:i:s");
      $url_img = "";
      $title_slug = "";
      $id_cp = 3635;

      //TODO: VARIABLES
      $diaHoy = strftime("%A %d de")." ".strftime("%B")." del ".strftime("%Y");

      $mesActual = ucfirst(strftime("%B"));

      $monedaDollarSelling = array($dollarSelling." pesos",$dollarSelling/100 ." centavos");
      $monedaDollarBuying = array($dollarBuying." pesos",$dollarBuying/100 ." centavos");
      $monedaYesteryday = array($dollarYesterday." pesos",$dollarYesterday/100 ." centavos");

      $bloqueEncabezadosAlza = array(
        "Precio del d??lar y tipo de cambio en M??xico este ".$diaHoy,
        "Costo del d??lar y tipo de cambio en M??xico este ".$diaHoy,
        "As?? amaneci?? el precio del d??lar en M??xico este ".$diaHoy,
        "Alza del d??lar el ".$diaHoy,
        "Precio del d??lar va a la alza, avanza contra el peso este".$diaHoy,
        "D??lar al alza se vende hoy en ".$monedaDollarSelling[array_rand($monedaDollarSelling,1)]." este ".$diaHoy,
        "D??lar al alza se compra hoy en ".$monedaDollarBuying[array_rand($monedaDollarBuying,1)]." este ".$diaHoy,
        "Incremento del d??lar por ".$monedaYesteryday[array_rand($monedaYesteryday,1)]." este ".$diaHoy,
        "Tipo de cambio en M??xico: ?? A cu??nto cotiza el d??lar este ".$diaHoy."?",
        "D??lar reporta incremento este ".$diaHoy,
        "Incremento el d??lar este ".$diaHoy.", revisa como se cotiza",
        "Peso se eleva levemente este d??a ".$diaHoy,
        "Peso de M??xico pierde ante el d??lar este d??a ".$diaHoy,
        "Hoy ".$diaHoy." el peso pierde terreno frente al d??lar",
        "As?? amaneci?? este ".$diaHoy." el precio del d??lar y tipo de cambio",
        "El precio del d??lar va a la alza este ".$diaHoy." respecto al peso",
        "Amanece d??lar a ".$dollar." pesos este ".$diaHoy,
        "Este mes de ".$mesActual." el d??lar gano ".$resultDollar[0]->dollarMonthPercentage." respecto al peso",
      );
      $bloqueEncabezadosBaja = array(
        "Precio del d??lar y tipo de cambio en M??xico este ".$diaHoy,
        "Costo del d??lar y tipo de cambio en M??xico este ".$diaHoy,
        "As?? amaneci?? el precio del d??lar en M??xico este ".$diaHoy,
        "Baja el precio d??lar este ".$diaHoy,
        "Dolar a la baja, no supera los ".floor($resultDollar[0]->dollar*1+1)." pesos",
        "Precio del d??lar va a la baja, retrocede frente al peso este ".$diaHoy,
        "D??lar a la baja se vende hoy en ".$monedaDollarSelling[array_rand($monedaDollarSelling,1)]." este ".$diaHoy,
        "D??lar a la baja se compra hoy en ".$monedaDollarBuying[array_rand($monedaDollarBuying,1)]." este ".$diaHoy,
        "Disminuci??n del d??lar por ".$monedaYesteryday[array_rand($monedaYesteryday,1)]." este ".$diaHoy,
        "Tipo de cambio en M??xico: ?? A cu??nto cotiza el d??lar este ".$diaHoy."?",
        "D??lar reporta disminuci??n este ".$diaHoy,
        "Bajo el d??lar este ".$diaHoy.", revisa como se cotiza",
        "Peso se cae levemente este d??a ".$diaHoy,
        "Peso de M??xico gana ante el d??lar este d??a ".$diaHoy,
        "Hoy ".$diaHoy." el peso gana terreno frente al d??lar",
        "As?? amaneci?? este ".$diaHoy." el precio del d??lar y tipo de cambio",
        "El precio del d??lar va a la baja este ".$diaHoy." respecto al peso",
        "Amanece d??lar a ".$dollar." pesos este ".$diaHoy,
        "Este mes de ".$mesActual." el d??lar perdi?? ".str_replace("-","",$resultDollar[0]->dollarMonthPercentage)."% respecto al peso",
      );
      $bloqueEncabezadosMantener = array(
        "Precio del d??lar y tipo de cambio en M??xico este ".$diaHoy,
        "Costo del d??lar y tipo de cambio en M??xico este ".$diaHoy,
        "As?? amaneci?? el precio del d??lar en M??xico este ".$diaHoy,
        "Se mantiene el precio d??lar este ".$diaHoy,
        "Dolar se mantiene, sigue en ".floor($resultDollar[0]->dollar*1)." pesos",
        "Precio del d??lar se mantiene frente al peso este".$diaHoy,
        "D??lar se mantiene y sigue a la compra en ".$monedaDollarBuying[array_rand($monedaDollarBuying,1)]." pesos este ".$diaHoy,
        "D??lar se mantiene y sigue a la venta en ".$monedaDollarSelling[array_rand($monedaDollarSelling,1)]." pesos este ".$diaHoy,
        "Tipo de cambio en M??xico: ?? A cu??nto cotiza el d??lar este ".$diaHoy."?",
        "??Cu??l es el precio del d??lar hoy ".$diaHoy."?",
        "Peso se mantiene levemente este d??a ".$diaHoy,
        "Peso de M??xico y el d??lar se mantienen este d??a".$diaHoy,
        "Hoy ".$diaHoy." el peso y el d??lar se mantienen",
        "Este ".$diaHoy." el d??lar se mantiene en el piso de ".floor($resultDollar[0]->dollar*1)." pesos",
        "As?? amaneci?? este ".$diaHoy." el precio del d??lar y tipo de cambio",
        "Amanece d??lar a ".$dollar." pesos este ".$diaHoy
      );

      $bloqueEncabezadosProgresivoAlta = array(
        "Sigue subiendo el precio del d??lar este ".$diaHoy,
        "D??lar sigue en racha de alza en su ".$resultDollar[0]->dayspersistent." d??a el ".$diaHoy,
        "Peso mexicano sigue perdiendo ante el d??lar por ".$resultDollar[0]->dayspersistent." d??a el ".$diaHoy,
        "D??lar presenta ".$resultDollar[0]->dayspersistent." d??a(s) a la alza",
      );
      $bloqueEncabezadosProgresivoBaja = array(
        "Sigue bajando el precio del d??lar este ".$diaHoy,
        "D??lar sigue en racha de baja en su ".$resultDollar[0]->dayspersistent." d??a el ".$diaHoy,
        "Peso mexicano sigue ganando ante el d??lar por ".$resultDollar[0]->dayspersistent." d??a el ".$diaHoy,
        "D??lar presenta ".$resultDollar[0]->dayspersistent." d??a(s) a la baja",
      );
      $bloqueEncabezadosProgresivoMantiene = array(
        "Sigue mantiendose el precio del d??lar hoy ".$diaHoy,
        "D??lar se mantiene en el piso de los ".$dollar." esta semana en M??xico",
        "D??lar sigue en racha de mantenerse en su ".$resultDollar[0]->dayspersistent." d??a el ".$diaHoy,
        "Peso mexicano y el d??lar se mantienen por ".$resultDollar[0]->dayspersistent." d??a el ".$diaHoy,
        "D??lar presenta ".$resultDollar[0]->dayspersistent." d??a(s) en mantenerse",
      );

      $bloqueEncabezadosProgresivoSemanasAlta = array(
        "D??lar presenta ".round($resultDollar[0]->dayspersistent/7)." semana(s) a la alza",
        "Registra d??lar semana consecutiva a la alza en M??xico",
      );
      $bloqueEncabezadosProgresivoSemanasBaja = array(
        "D??lar presenta ".round($resultDollar[0]->dayspersistent/7)." semana(s) a la baja",
        "Registra d??lar semana consecutiva a la baja en M??xico",
      );
      $bloqueEncabezadosProgresivoSemanasMantenerse = array(
        "D??lar presenta ".round($resultDollar[0]->dayspersistent/7)." semana(s) en mantenerse",
        "Registra d??lar semana consecutiva de mantenerse en M??xico",
      );

      $bloqueEntradaAlza = array(
        "El d??lar se aprecia este d??a por arriba de la barrera de las ".floor($resultDollar[0]->dollar*1)." unidades ante el peso mexicano con una compra de ".$dollarBuying." pesos y venta de ".$dollarSelling." pesos.",
        "El tipo de cambio interbancario del d??lar amanece en ".$dollar." pesos, lo que representa un alza en ".$resultDollar[0]->dollarWithYesterdayPercentage." respecto al peso mexicano, con un incremento de ".$dollarYesterday." pesos",
        "Al iniciar operaciones este ".$diaHoy.", el d??lar est?? a la alza en ".$resultDollar[0]->dollarWithYesterdayPercentage." respecto al peso mexicano, con un incremento de ".$dollarYesterday." pesos.",
      );
      $bloqueEntradaBaja = array(
        "El peso se aprecia este d??a por debajo de la barrera de las ".floor($resultDollar[0]->dollar*1+1)." unidades ante una debilidad del d??lar con una compra de ".$dollarBuying." pesos y venta de ".$dollarSelling." pesos.",
        "El tipo de cambio interbancario del d??lar amanece en ".$dollar." pesos, lo que representa una baja en ".str_replace("-","",$resultDollar[0]->dollarWithYesterdayPercentage)."% respecto al peso mexicano, con una disminuci??n de ".$dollarYesterday." pesos",
        "Al iniciar operaciones este ".$diaHoy.", el d??lar est?? a la baja en ".str_replace("-","",$resultDollar[0]->dollarWithYesterdayPercentage)."% respecto al peso mexicano, con una disminuci??n de ".$dollarYesterday." pesos.",
      );
      $bloqueEntradaMantener = array(
        "D??lar se mantiene ante peso mexicano y su valor sigue en ".floor($resultDollar[0]->dollar*1)." unidades",
        "El precio del d??lar en M??xico se mantiene en su valor en la compra y venta en las ventanillas del pa??s este d??a ".$diaHoy."con un precio de $".$dollar." pesos",
        "El tipo de cambio interbancario del d??lar amanece en ".$dollar." pesos, por lo que se mantiene en ".$dollarYesterday." pesos",
        "Al iniciar operaciones este ".$diaHoy.", el d??lar se mantiene en $".$dollar." pesos",
      );

      $bloqueEntradaBajaIntermedia = array(
        "El peso rompi?? el techo de las ".floor($resultDollar[0]->dollar*1+1)." unidades para cerrar operaciones en su mejor nivel desde finales de enero",
      );

      $bloqueVentanilla = array(
        "El precio del d??lar estadounidense en la compra, se promedia en ventanilla en $".$dollarBuying." pesos. Mientras que su valor en ventanilla por venta se promedi?? en $".$dollarSelling." pesos.",
        "Los valores del d??lar por peso se encuentran a la compra en $".$dollarBuying.", mientras a la venta en $".$dollarSelling." pesos.",
        "La moneda americana cotiza este mi??rcoles a $".$dollarBuying." pesos a la compra y $".$dollarSelling." pesos a la venta en ventanillas bancarias.",
        "El d??lar se cotiza en ".$dollarBuying." pesos a la compra y ".$dollarSelling." pesos a la venta.",
        "El tipo de cambio interbancario se ubica en ".$dollar." pesos por d??lar. A la compra en ".$dollarBuying.", mientras a la venta en ".$dollarSelling."."
      );


      $bloqueA??oAlza = array(
        "Desde el m??ximo que registr?? en ".strftime("%B")." del a??o pasado a la fecha, el d??lar recuper?? ".str_replace("-","",$resultDollar[0]->dollarYearPercentage)."%, es decir ".$resultDollar[0]->dollarYear." pesos",
        "En lo que va del a??o a la fecha, el comportamiento del d??lar contra el peso mexicano est?? a la baja, con una disminuci??n de ".str_replace("-","",$resultDollar[0]->dollarYear)." pesos y un porcentaje de ".str_replace("-","",$resultDollar[0]->dollarYearPercentage),
        "Con los valores actuales, en lo que va del a??o a la fecha, el comportamiento del d??lar contra el peso mexicano tambi??n est?? a la alza, con un incremento de ".$resultDollar[0]->dollarYear." y un porcentaje de ".str_replace("-","",$resultDollar[0]->dollarYearPercentage)."."
      );

      $bloqueA??oBaja = array(
        "Desde el m??ximo que registr?? en ".strftime("%B")." del a??o pasado a la fecha, el peso mexicano recuper?? ".str_replace("-","",$resultDollar[0]->dollarYearPercentage).", es decir ".str_replace("-","",$resultDollar[0]->dollarYear)." pesos",
        "En lo que va del a??o a la fecha, el comportamiento del d??lar contra el peso mexicano est?? a la alza, con una ganancia de ".str_replace("-","",$resultDollar[0]->dollarYear)." pesos y un porcentaje de ".str_replace("-","",$resultDollar[0]->dollarYearPercentage),
        "Con los valores actuales, en lo que va del a??o a la fecha, el comportamiento del d??lar contra el peso mexicano tambi??n est?? a la baja, con un decremento de ".$resultDollar[0]->dollarYear." pesos y un porcentaje de ".str_replace("-","",$resultDollar[0]->dollarYearPercentage)."."
      );

      $bloqueSemanaBaja = array(
        "Y es que, en esta semana el d??lar ha registrado una ca??da de ".str_replace("-","",$resultDollar[0]->dollarWeekPercentage)."% frente al peso, la moneda americana se mantiene a la compra entre los ".$dollarBuying." y ".$dollar." pesos a la compra y entre los ".($dollar-0.485)." y ".$dollarSelling." pesos a la venta.",
        "La media de venta durante la ??ltima semana se ubic?? en ".abs($dollarAverageLastWeek)." pesos, debajo de la semana inmediata anterior donde fue de ".abs($dollarAverage2Week)." pesos.",
        "Durante la semana el d??lar ha registrado una ca??da de ".str_replace("-","",$resultDollar[0]->dollarWeekPercentage)." frente al peso, la moneda americana se mantiene a la compra entre los ".$resultMinMaxBuyingAndSellingLast2Week[0]->minimoCompra." y ".$resultMinMaxBuyingAndSellingLast2Week[0]->maximoCompra." pesos a la compra y entre los ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->minimoVenta)." y ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->maximoVenta)." pesos a la venta."
      );
      $bloqueSemanaAlza = array(
        "Y es que, en esta semana el d??lar ha registrado el d??lar ha registrado una alza de ".str_replace("-","",$resultDollar[0]->dollarWeekPercentage)." frente al peso, la moneda americana se mantiene a la compra entre los ".$dollarBuying." y ".$dollar." pesos a la compra y entre los ".($dollar-0.485)." y ".$dollarSelling." pesos a la venta.",
        "La media de venta durante la ??ltima semana se ubic?? en ".$dollarAverageLastWeek." pesos, arriba de la semana inmediata anterior donde fue de ".$dollarAverage2Week." pesos.",
        "Durante la semana el d??lar ha registrado una alza de ".str_replace("-","",$resultDollar[0]->dollarWeekPercentage)." frente al peso, la moneda americana se mantiene a la compra entre los ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->minimoCompra)." y ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->maximoCompra)." pesos a la compra y entre los ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->minimoVenta)." y ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->maximoVenta)." pesos a la venta."
      );

      $bloqueMesBaja = array(
        "La baja de este d??a contrasta con lo que se ha manifestado en lo que va del mes a la fecha, con una ganancia de ".str_replace("-","",$resultDollar[0]->dollarMonthPercentage)."%, es decir, ".str_replace("-","",$resultDollar[0]->dollarMonth)." pesos",
        "Durante el mes ha registrado ca??das de ".str_replace("-","",$resultMinMaxMonth[0]->minimo)." y ".str_replace("-","",$resultMinMaxMonth[0]->maximo)." cifras que concuerdan con los del trimestre",
      );

      $bloqueMesAlza = array(
        "La alza de este d??a contrasta con lo que se ha manifestado en lo que va del mes a la fecha, con una p??rdida de ".str_replace("-","",$resultDollar[0]->dollarMonthPercentage)."%, es decir, ".str_replace("-","",$resultDollar[0]->dollarMonth)." pesos",
        "Durante el mes ha registrado ca??das de ".str_replace("-","",$resultMinMaxMonth[0]->minimo)." y ".str_replace("-","",$resultMinMaxMonth[0]->maximo)." cifras que concuerdan con los del trimestre",
      );

      $bloqueRellenoParteUna = array(
        "Las variables del d??lar han mantenido un descontrol en su valor en los bancos de M??xico, una de las principales son las afectaciones que ha tra??do a la econom??a global la pandemia del coronavirus SARS-CoV-2.",
        "En todo el mundo, el precio del d??lar ha sido cambiante, en una de las monedas m??s importantes pues es considerada universal, incursionando en casi todas las econom??as del planeta",
        "Cabe recordar que los mercados cambian de comportamiento de acuerdo a las decisiones que tomen los pa??ses, organismos financieros y empresas.",
        "Si quieres revisar los registros de d??as pasados del precio del d??lar y el tipo de cambio, visita codigopostal.com",
      );
      $bloqueRellenoParteDos = array(
        "Cada banco en M??xico presenta un valor variable en la compra y venta de d??lares en el territorio de la Rep??blica.",
        "Lo mismo pasa con los d??lares que circulan en el mercado. Cuando existen muchos en circulaci??n se dice que la moneda tiene liquidez y su precio baja. Y cuando sube, es b??sicamente por su poca disponibilidad frente a la demanda de la divisa.",
        "Las variaciones del mercado tambi??n afectan el precio de una moneda, por ejemplo: si muchos inversionistas deciden sacar su dinero de un pa??s para llevarlo a otro, entonces la liquidez bajar?? y el precio de la moneda aumentar??.",
        "Cabe recordar que los mercados cambian de comportamiento de acuerdo a las decisiones que tomen los pa??ses, organismos financieros y empresas."
      );
      $bloqueRellenoParteTres = array(
        "M??xico lleva varios a??os con un tipo de cambio flotante con un grado de intervenci??n del Banco de M??xico, teniendo presente que en el r??gimen fijo la moneda se reval??a o eval??a mientras que en el r??gimen flexible o flotante la moneda se aprecia o deprecia. La modificaci??n del tipo de cambio fijo al flotante ha permitido que la demanda y oferta sean los factores decisivos de la paridad",
        "Sab??as que...en M??xico, existe un r??gimen cambiario flexible desde el 22 diciembre de 1994 (el 21 se abandon?? el r??gimen de desliz cambiario lo que efectivamente provoc?? una importante devaluaci??n), despu??s de experimentar con reg??menes fijos (1954-1976) y otros mixtos como la flotaci??n controlada y otros. El r??gimen flexible ha sido muy ??til para estabilizar la econom??a mexicana en periodos de alta incertidumbre y quitar presi??n a variables como las tasas de inter??s y la pol??tica fiscal",
        "Sab??as que...el d??lar es la moneda de referencia mundial desde la Segunda Guerra Mundial. Ninguna otra moneda le ha hecho sombra desde entonces. Tampoco en los ??ltimos 25 a??os, donde se le acercaron primero el yen y luego el euro",
      );

      $bloqueHistorico = array(
        "<p>Los datos durante la ??ltima semana del d??lar:</p><p>".$resultHistorico[1]->fecha."</p><p>".$resultHistorico[2]->fecha."</p><p>".$resultHistorico[3]->fecha."</p><p>".$resultHistorico[4]->fecha."</p><p>".$resultHistorico[4]->fecha."</p><p>".$resultHistorico[5]->fecha."</p>",
        "<p>La cotizaci??n de compra-venta del d??lar en la semana anterior fue: <p>".$resultHistorico[0]->fecha."</p><p>".$resultHistorico[1]->fecha."</p><p>".$resultHistorico[2]->fecha."</p><p>".$resultHistorico[3]->fecha."</p><p>".$resultHistorico[4]->fecha."</p><p>".$resultHistorico[5]->fecha."</p>"
      );

      $bloqueBidenAlza = array(
        "Si se considera el precio del d??lar desde cuando toma posesi??n Joe Biden el 20 de enero del 2021 a la presidencia de los Estados Unidos, se presenta a la alza con un incremento de ".str_replace("-","",$resultDollar[0]->dollarBiden)." pesos y un porcentaje favorable de ".str_replace("-","",$resultDollar[0]->dollarBidenPercentage).".",
        "Y desde el 20 de enero del 2021 cuando tomo posesi??n Joe Biden registr?? una ganancia de los ".$resultBidenMinMaxUp[0]->minimaBiden." y ".$resultBidenMinMaxUp[0]->maximaBiden." sobre el peso."
      );

      // $bloqueBidenBaja = array(
      //   "Aunque si se considera el precio del d??lar desde la llegada de Joe Biden a la presidencia de los Estados Unidos, se presenta a la alza con un incremento de ".str_replace("-","",$resultDollar[0]->dollarBiden)." pesos y un porcentaje favorable de ".str_replace("-","",$resultDollar[0]->dollarBidenPercentage)."%.",
      //   "Y desde el inicio del gobierno de Joe Biden registr?? una perdida de los ".$resultBidenMinMaxDown[0]->minimaBiden." y ".$resultBidenMinMaxDown[0]->maximaBiden." sobre el peso."
      // );

      $bloqueAMLOAlza = array(
        "Si se considera el precio del d??lar desde cuando toma posesi??n AMLO el 01 de diciembre del 2018 a la presidencia de M??xico, se presenta a la baja con ".str_replace("-","",$resultDollar[0]->dollarAMLO)." pesos y un porcentaje desfavorable de ".str_replace("-","",$resultDollar[0]->dollarAMLOPercentage).".",
        "Y desde el 01 de diciembre del 2018 cuando tomo posesi??n AMLO registr?? una perdida de los ".$resultAMLOMinMaxUp[0]->minimaAMLO." y ".$resultAMLOMinMaxUp[0]->maximaAMLO." sobre el peso."
      );

      $bloqueAMLOBaja = array(
        "Si se considera el precio del d??lar desde cuanto toma posesi??n AMLO el 01 de diciembre del 2018 a la presidencia de M??xico, se presenta a la alza con  ".str_replace("-","",$resultDollar[0]->dollarAMLO)." pesos y un porcentaje favorable de ".str_replace("-","",$resultDollar[0]->dollarAMLOPercentage).".",
        "Y desde el 01 de diciembre del 2018 cuando tomo posesi??n AMLO registr?? una ganancia de los ".str_replace("-","",$resultAMLOMinMaxDown[0]->minimaAMLO)." y ".str_replace("-","",$resultAMLOMinMaxDown[0]->maximaAMLO)." sobre el peso."
      );

      $bloqueParrafo = array(
        "El d??lar es una de las monedas m??s importantes pues es considerada universal incursionando en casi todas las econom??as del planeta.",
        "??Por qu?? es importante conocer el tipo de cambio? Sirve como indicador de la competitividad de un pa??s con el resto del mundo ya que relaciona los precios internos de la producci??n nacional con los precios internacionales.",
        "El tipo de cambio es una referencia que se usa en el mercado cambiario para conocer el n??mero de unidades de moneda nacional que deben pagarse para obtener una moneda extranjera, o similarmente, el n??mero de unidades de moneda nacional que se obtienen al vender una unidad de moneda extranjera.
",
        "??Qu?? determina el precio del d??lar? La ley de la oferta y la demanda es el principal factor que determinan el precio de esta divisa. En M??xico el tipo de cambio peso-d??lar se determina bajo un r??gimen cambiario de libre flotaci??n. Es la Comisi??n de Cambios la que faculta al Banco de M??xico para llevar a cabo operaciones en el mercado cambiario."
      );

      $secondParagraph = rand(1,3);
      $thirdParagraph = rand(1,3);
      $fourthParagraph = rand(1,2);
      $fiveParagraph = rand(1,3);

      $arraySecondParagraphDown = array(
        $bloqueSemanaBaja,
        $bloqueMesBaja,
        $bloqueA??oBaja
      );

      $arraySecondParagraphUp = array(
        $bloqueSemanaAlza,
        $bloqueMesAlza,
        $bloqueA??oAlza
      );

      $arrayThirdParagrapDown = array(
        $bloqueA??oBaja,
        $bloqueA??oBaja
      );

      $arrayThirdParagrapUp = array(
        $bloqueA??oAlza,
        $bloqueA??oAlza
      );

      $arrayFiveParagraphUp = array(
        $bloqueAMLOBaja,
        $bloqueBidenAlza,
      );

      $arrayFiveParagraphDown = array(
        $bloqueAMLOAlza,
        $bloqueAMLOAlza,
      );


      if(isset($resultDollar[0])){ // SE BAJA
        if($resultDollar[0]->dollarWithYesterday < 0){
          $ftitle = $bloqueEncabezadosBaja[array_rand($bloqueEncabezadosBaja,1)];
          if($resultDollar[0]->dayspersistent >= 2 && $resultDollar[0]->dayspersistent < 7){
            $ftitle = $bloqueEncabezadosProgresivoBaja[array_rand($bloqueEncabezadosProgresivoBaja,1)];
          }else if($resultDollar[0]->dayspersistent > 7){
            $ftitle = $bloqueEncabezadosProgresivoSemanasBaja[array_rand($bloqueEncabezadosProgresivoSemanasBaja,1)];
          }
          $content = "<p>".$bloqueEntradaBaja[array_rand($bloqueEntradaBaja,1)]."</p>";
          $content.= "<p>".$bloqueVentanilla[array_rand($bloqueVentanilla,1)]."</p>";

          $resultRandomNumberTwo = $this->getItemOfArrayByRandomNumberRecursive($arraySecondParagraphDown);

          if($resultRandomNumberTwo["keyToDelete"] == 2){
            unset($arrayThirdParagrapDown[0]);
          }

          $content.="<p>".$resultRandomNumberTwo["text"]."</p>";

          $resultRandomNumberThird = $this->getItemOfArrayByRandomNumberRecursive($arrayThirdParagrapDown);

          if($resultRandomNumberThird["keyToDelete"] == 1){
            unset($arrayFiveParagraphDown[1]);
          }

          if($resultRandomNumberThird["keyToDelete"] == 1){
            $content.=$resultRandomNumberThird["text"];
          }else{
            $content.="<p>".$resultRandomNumberThird["text"]."</p>";
          }


          $resultRandomNumberFive = $this->getItemOfArrayByRandomNumberRecursive($arrayFiveParagraphDown);

          echo "\n\n\nKEY TO DELETE FIVE".$resultRandomNumberFive["keyToDelete"]."\n\n\n";

          if($resultRandomNumberFive["keyToDelete"] == 2){
            $content.=$resultRandomNumberFive["text"];
          }else{
            $content.="<p>".$resultRandomNumberFive["text"]."</p>";
          }

          $content.= "<p>".$bloqueParrafo[array_rand($bloqueParrafo,1)]."</p>";
          $content .= $htmlCSS;
          $content .= $htmlJS;
          $content.= $resultDollar[0]->htmlDollarOthers;
          $content = addslashes(html_entity_decode(trim($content)))."<caption>Tabla proporcionada por ElDolar.info</caption>";

        }else if($resultDollar[0]->dollarWithYesterday > 0){ // SE ALZA
          $ftitle = $bloqueEncabezadosAlza[array_rand($bloqueEncabezadosAlza,1)];
          if($resultDollar[0]->dayspersistent >= 2 && $resultDollar[0]->dayspersistent < 7){
            $ftitle = $bloqueEncabezadosProgresivoAlta[array_rand($bloqueEncabezadosProgresivoAlta,1)];
          }else if($resultDollar[0]->dayspersistent > 7){
            $ftitle = $bloqueEncabezadosProgresivoSemanasAlta[array_rand($bloqueEncabezadosProgresivoSemanasAlta,1)];
          }
          $content = "<p>".$bloqueEntradaAlza[array_rand($bloqueEntradaAlza,1)]."</p>";
          $content.= "<p>".$bloqueVentanilla[array_rand($bloqueVentanilla,1)]."</p>";

          $resultRandomNumberTwo = $this->getItemOfArrayByRandomNumberRecursive($arraySecondParagraphUp);

          if($resultRandomNumberTwo["keyToDelete"] == 2){
            unset($arrayThirdParagrapUp[0]);
          }

          $content.="<p>".$resultRandomNumberTwo["text"]."</p>";

          $resultRandomNumberThird = $this->getItemOfArrayByRandomNumberRecursive($arrayThirdParagrapUp);

          if($resultRandomNumberThird["keyToDelete"] == 1){
            unset($arrayFiveParagraphUp[2]);
          }

          if($resultRandomNumberThird["keyToDelete"] == 1){
            $content.=$resultRandomNumberThird["text"];
          }else{
            $content.="<p>".$resultRandomNumberThird["text"]."</p>";
          }

          $resultRandomNumberFive = $this->getItemOfArrayByRandomNumberRecursive($arrayFiveParagraphUp);

          if($resultRandomNumberFive["keyToDelete"] == 2){
            $content.=$resultRandomNumberFive["text"];
          }else{
            $content.="<p>".$resultRandomNumberFive["text"]."</p>";
          }

          $content.= "<p>".$bloqueParrafo[array_rand($bloqueParrafo,1)]."</p>";
          $content .= $htmlCSS;
          $content .= $htmlJS;
          $content.= $resultDollar[0]->htmlDollarOthers;
          $content = addslashes(html_entity_decode(trim($content)))."<caption>Tabla proporcionada por ElDolar.info</caption>";


        }else{ // SE MANTIENE
          $ftitle = $bloqueEncabezadosMantener[array_rand($bloqueEncabezadosMantener,1)];
          if($resultDollar[0]->dayspersistent >= 2 && $resultDollar[0]->dayspersistent < 7){
            $ftitle = $bloqueEncabezadosProgresivoMantiene[array_rand($bloqueEncabezadosProgresivoMantiene,1)];
          }else if($resultDollar[0]->dayspersistent > 7){
            $ftitle = $bloqueEncabezadosProgresivoSemanasMantenerse[array_rand($bloqueEncabezadosProgresivoSemanasMantenerse,1)];
          }
          $content = "<p>".$bloqueEntradaMantener[array_rand($bloqueEntradaMantener,1)]."</p>";
          $content.= "<p>".$bloqueVentanilla[array_rand($bloqueVentanilla,1)]."</p>";
          $content.= "<p>".$bloqueParrafo[array_rand($bloqueParrafo,1)]."</p>";
          $content.= $bloqueHistorico[array_rand($bloqueHistorico,1)];
          $content .= $htmlCSS;
          $content .= $htmlJS;
          $content.= $resultDollar[0]->htmlDollarOthers;
          $content = addslashes(html_entity_decode(trim($content)))."<caption>Tabla proporcionada por ElDolar.info</caption>";
        }

        $title_slug = $this->slugify($ftitle)."-".date("Y-m-d");

        $url.= $title_slug;

        $this->CrearTransparencia("negro","dolar-baja.png");

        $this->CargarDatosaImagen($dollar,"dolar-baja.png","blanco");

        $get_url_image = $this->getImageFromAWSByUriImageExternal(getcwd().'/app/Console/Commands/images/dollar_convertida/dolar-baja'.date("ymd").".png");

        if($get_url_image !== false)
        {
            $url_img = $get_url_image['relative_path'];
        }

        $insert_news = \DB::insert("INSERT INTO `news`(`id_cp`,`title`,`url`,`summary`,`content`,`created_at`,`updated_at`,
                                                        `image`,`seo_title`,`seo_description`,`seo_keywords`,`id_status_news`,`imported`,`title_normalizado`,
                                                        `id_editor`,`url_canonical`,`id_author`,`id_position`,`id_copo_id`,`id_state`,`model_feed`,`caption`)
                                    VALUES ($id_cp, '$ftitle','$url',
                                    '$fsummary',
                                    '$content','$curdate', '$curdate', '$url_img', '$ftitle', '$ftitle', '$ftitle',7, 0, '$title_slug', NULL, NULL,100,NULL,
                                    NULL, $id_cp, 0, NULL);");

        $dollarPath = getcwd()."/public/dollar/";
        $fileDollar = fopen($dollarPath."dollar.txt", "w+");
        $txt = "<TITLE>".$ftitle."</TITLE>\n";
        $txt.= "<SUMMARY>".$fsummary."</SUMMARY>\n";
        $txt.= "<CONTENT>".$content."</CONTENT>\n";
        $txt.= "<IMAGE>https://copoadminpro.s3.us-east-2.amazonaws.com/".$url_img."</IMAGE>\n";
        $txt.= "<SEOTITLE>".$ftitle."</SEOTITLE>\n";
        $txt.= "<SEODESCRIPTION>".$fsummary."</SEODESCRIPTION>\n";
        $txt.= "<FECHA>".date("d-m-Y H:i:s")."</FECHA>\n";
        $txt.= "<AUTOR>CodigoPostal</AUTOR>\n";
        fwrite($fileDollar, $txt);
      }
    }

    public function getItemOfArrayByRandomNumberRecursive($arraySearch){
      $rand= array_rand($arraySearch,1);

      if(isset($arraySearch[$rand]) && !empty($arraySearch[$rand])){
        $resultSearch = $arraySearch[$rand];

        $randArray = array_rand($resultSearch,1);

        $resultSearch = $resultSearch[$randArray];

        return array("text" => $resultSearch,"keyToDelete" => $rand);
      }else{
        $this->getItemOfArrayByRandomNumberRecursive($arraySearch);
      }
    }

    public function slugify($text)
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


    public function getRenderedHTML($path){
      ob_start();
      include($path);
      $var=ob_get_contents();
      ob_end_clean();
      return $var;
    }

    public function CrearTransparencia($semaforo, $nombre){
        try {
            $r = 0;
            $g = 0;
            $b = 0;

            switch ($semaforo) {
                case "verde":
                    $r = 137;
                    $g = 238;
                    $b = 133;
                    break;
                case "amarillo":
                    $r = 255;
                    $g = 213;
                    $b = 0;
                    break;
                case "naranja":
                    $r = 255;
                    $g = 128;
                    $b = 0;
                    break;
                case "rojo":
                    $r = 255;
                    $g = 0;
                    $b = 76;
                    break;
                case "gris":
                    $r = 155;
                    $g = 155;
                    $b = 155;
                    break;
                case "negro":
                    $r = 0;
                    $g = 0;
                    $b = 0;
                    break;
            }

            //Cargamos la dos imagenes ambas de 128x128px
            $a = sprintf(getcwd()."/app/Console/Commands/images/dollar/%s", $nombre);
            $img1 = imagecreatefrompng($a);

            //Creamos el lienzo con el tama??o para contener las 2 imagenes, y le asignamos transparencia
            $image = imagecreatetruecolor(831, 548);
            imagesavealpha($image, true);
            $alpha = imagecolorallocatealpha($image, $r, $g, $b, 50);
            imagefill($image, 0, 0, $alpha);

            //Guardamos y leberamos el objeto
            $c = sprintf(getcwd().'/app/Console/Commands/images/dollar_transparencias/%s.png', $semaforo);
            imagepng($image, $c);

            $marca = imagecreatefrompng($c);

            imagecopy($img1, $marca, 0, 0, 0, 0, 831, 548);
            imagepng($img1, getcwd().'/app/Console/Commands/images/dollar_convertida/' . str_replace(".png", (date("ymd").".png"), $nombre));

            imagedestroy($image);
            imagedestroy($img1);

            echo "Se creo la transparencia \n";

            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function CargarDatosaImagen($dolar,$nombre,$semaforo){
        try {
            //header('Content-type: image/png');

            // Load And Create Image From Source
            $imgs = sprintf(getcwd().'/app/Console/Commands/images/dollar_convertida/%s', str_replace(".png", (date("ymd").".png"), $nombre));

            $our_image = imagecreatefrompng($imgs);

            // Set Path to Font File
            $font_path = getcwd().'/app/Console/Commands/fonts/static/Karla-Bold.ttf';

            // Set Text to Be Printed On Image
            $text1 = "Precio del d??lar de hoy:";
            $text2= ucfirst(strftime("%A %d de"))." ".ucfirst(strftime("%B"))." del ".strftime("%Y");
            $text3 = "$".$dolar." MXN";

            $r1 = 0;
            $g1 = 0;
            $b1 = 0;

            $r2 = 0;
            $g2 = 0;
            $b2 = 0;

            switch ($semaforo) {
                case "verde";
                case "amarillo";
                    $r1 = 0;
                    $g1 = 0;
                    $b1 = 0;

                    $r2 = 0;
                    $g2 = 130;
                    $b2 = 255;
                    break;
                case "naranja":
                case "rojo":
                    $r1 = 0;
                    $g1 = 0;
                    $b1 = 0;

                    $r2 = 255;
                    $g2 = 255;
                    $b2 = 255;
                    break;
                case "negro":
                    $r1 = 0;
                    $g1 = 0;
                    $b1 = 0;

                    $r2 = 0;
                    $g2 = 0;
                    $b2 = 0;
                    break;
                default:
                    $r1 = 255;
                    $g1 = 255;
                    $b1 = 255;

                    $r2 = 255;
                    $g2 = 255;
                    $b2 = 255;
                    break;
            }

            $size1 = 20;
            $angle1 = 0;
            $left1 = 10;
            $top1 = 50;

            $white_color = imagecolorallocate($our_image, $r1, $g1, $b1);
            imagettftext($our_image, $size1, $angle1, $left1, $top1, $white_color, $font_path, $text1);

            $size2 = 30;
            $angle2 = 0;
            $left2 = 10;
            $top2 = 100;

            $white_color = imagecolorallocate($our_image, $r1, $g1, $b1);
            imagettftext($our_image, $size2, $angle2, $left2, $top2, $white_color, $font_path, $text2);

            $size2 = 60;
            $angle2 = 0;
            $left2 = 10;
            $top2 = 200;

            // $white_color = imagecolorallocate($our_image, $r2, $g2, $b2);
            // imagettftext($our_image, $size2, $angle2, $left2, $top2, $white_color, $font_path, $text3);


            // $size1 = 22;
            // $angle1 = 0;
            // $left1 = 50;
            // $top1 = 330;
            //
            // $white_color = imagecolorallocate($our_image, $r1, $g1, $b1);
            // imagettftext($our_image, $size1, $angle1, $left1, $top1, $white_color, $font_path, $text4);
            //
            // $size1 = 22;
            // $angle1 = 0;
            // $left1 = 50;
            // $top1 = 370;
            //
            // $white_color = imagecolorallocate($our_image, $r1, $g1, $b1);
            // imagettftext($our_image, $size1, $angle1, $left1, $top1, $white_color, $font_path, $text5);


            // $size1 = 70;
            // $angle1 = 0;
            // $left1 = 50;
            // $top1 = 470;
            //
            // $white_color = imagecolorallocate($our_image, $r2, $g2, $b2);
            // imagettftext($our_image, $size1, $angle1, $left1, $top1, $white_color, $font_path, $text6);

            $nombre = sprintf(getcwd().'/app/Console/Commands/images/dollar_convertida/%s', str_replace(".png", (date("ymd").".png"), $nombre));

            imagepng($our_image, $nombre);

            echo sprintf("La imagen se creo en %s\n", $nombre);

            // Clear Memory
            imagedestroy($our_image);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    public function getImageFromAWSByUriImageExternal($uriImageExternal){
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
