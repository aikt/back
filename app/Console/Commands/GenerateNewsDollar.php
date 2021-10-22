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
        "Precio del dólar y tipo de cambio en México este ".$diaHoy,
        "Costo del dólar y tipo de cambio en México este ".$diaHoy,
        "Así amaneció el precio del dólar en México este ".$diaHoy,
        "Alza del dólar el ".$diaHoy,
        "Precio del dólar va a la alza, avanza contra el peso este".$diaHoy,
        "Dólar al alza se vende hoy en ".$monedaDollarSelling[array_rand($monedaDollarSelling,1)]." este ".$diaHoy,
        "Dólar al alza se compra hoy en ".$monedaDollarBuying[array_rand($monedaDollarBuying,1)]." este ".$diaHoy,
        "Incremento del dólar por ".$monedaYesteryday[array_rand($monedaYesteryday,1)]." este ".$diaHoy,
        "Tipo de cambio en México: ¿ A cuánto cotiza el dólar este ".$diaHoy."?",
        "Dólar reporta incremento este ".$diaHoy,
        "Incremento el dólar este ".$diaHoy.", revisa como se cotiza",
        "Peso se eleva levemente este día ".$diaHoy,
        "Peso de México pierde ante el dólar este día ".$diaHoy,
        "Hoy ".$diaHoy." el peso pierde terreno frente al dólar",
        "Así amaneció este ".$diaHoy." el precio del dólar y tipo de cambio",
        "El precio del dólar va a la alza este ".$diaHoy." respecto al peso",
        "Amanece dólar a ".$dollar." pesos este ".$diaHoy,
        "Este mes de ".$mesActual." el dólar gano ".$resultDollar[0]->dollarMonthPercentage." respecto al peso",
      );
      $bloqueEncabezadosBaja = array(
        "Precio del dólar y tipo de cambio en México este ".$diaHoy,
        "Costo del dólar y tipo de cambio en México este ".$diaHoy,
        "Así amaneció el precio del dólar en México este ".$diaHoy,
        "Baja el precio dólar este ".$diaHoy,
        "Dolar a la baja, no supera los ".floor($resultDollar[0]->dollar*1+1)." pesos",
        "Precio del dólar va a la baja, retrocede frente al peso este ".$diaHoy,
        "Dólar a la baja se vende hoy en ".$monedaDollarSelling[array_rand($monedaDollarSelling,1)]." este ".$diaHoy,
        "Dólar a la baja se compra hoy en ".$monedaDollarBuying[array_rand($monedaDollarBuying,1)]." este ".$diaHoy,
        "Disminución del dólar por ".$monedaYesteryday[array_rand($monedaYesteryday,1)]." este ".$diaHoy,
        "Tipo de cambio en México: ¿ A cuánto cotiza el dólar este ".$diaHoy."?",
        "Dólar reporta disminución este ".$diaHoy,
        "Bajo el dólar este ".$diaHoy.", revisa como se cotiza",
        "Peso se cae levemente este día ".$diaHoy,
        "Peso de México gana ante el dólar este día ".$diaHoy,
        "Hoy ".$diaHoy." el peso gana terreno frente al dólar",
        "Así amaneció este ".$diaHoy." el precio del dólar y tipo de cambio",
        "El precio del dólar va a la baja este ".$diaHoy." respecto al peso",
        "Amanece dólar a ".$dollar." pesos este ".$diaHoy,
        "Este mes de ".$mesActual." el dólar perdió ".str_replace("-","",$resultDollar[0]->dollarMonthPercentage)."% respecto al peso",
      );
      $bloqueEncabezadosMantener = array(
        "Precio del dólar y tipo de cambio en México este ".$diaHoy,
        "Costo del dólar y tipo de cambio en México este ".$diaHoy,
        "Así amaneció el precio del dólar en México este ".$diaHoy,
        "Se mantiene el precio dólar este ".$diaHoy,
        "Dolar se mantiene, sigue en ".floor($resultDollar[0]->dollar*1)." pesos",
        "Precio del dólar se mantiene frente al peso este".$diaHoy,
        "Dólar se mantiene y sigue a la compra en ".$monedaDollarBuying[array_rand($monedaDollarBuying,1)]." pesos este ".$diaHoy,
        "Dólar se mantiene y sigue a la venta en ".$monedaDollarSelling[array_rand($monedaDollarSelling,1)]." pesos este ".$diaHoy,
        "Tipo de cambio en México: ¿ A cuánto cotiza el dólar este ".$diaHoy."?",
        "¿Cuál es el precio del dólar hoy ".$diaHoy."?",
        "Peso se mantiene levemente este día ".$diaHoy,
        "Peso de México y el dólar se mantienen este día".$diaHoy,
        "Hoy ".$diaHoy." el peso y el dólar se mantienen",
        "Este ".$diaHoy." el dólar se mantiene en el piso de ".floor($resultDollar[0]->dollar*1)." pesos",
        "Así amaneció este ".$diaHoy." el precio del dólar y tipo de cambio",
        "Amanece dólar a ".$dollar." pesos este ".$diaHoy
      );

      $bloqueEncabezadosProgresivoAlta = array(
        "Sigue subiendo el precio del dólar este ".$diaHoy,
        "Dólar sigue en racha de alza en su ".$resultDollar[0]->dayspersistent." día el ".$diaHoy,
        "Peso mexicano sigue perdiendo ante el dólar por ".$resultDollar[0]->dayspersistent." día el ".$diaHoy,
        "Dólar presenta ".$resultDollar[0]->dayspersistent." día(s) a la alza",
      );
      $bloqueEncabezadosProgresivoBaja = array(
        "Sigue bajando el precio del dólar este ".$diaHoy,
        "Dólar sigue en racha de baja en su ".$resultDollar[0]->dayspersistent." día el ".$diaHoy,
        "Peso mexicano sigue ganando ante el dólar por ".$resultDollar[0]->dayspersistent." día el ".$diaHoy,
        "Dólar presenta ".$resultDollar[0]->dayspersistent." día(s) a la baja",
      );
      $bloqueEncabezadosProgresivoMantiene = array(
        "Sigue mantiendose el precio del dólar hoy ".$diaHoy,
        "Dólar se mantiene en el piso de los ".$dollar." esta semana en México",
        "Dólar sigue en racha de mantenerse en su ".$resultDollar[0]->dayspersistent." día el ".$diaHoy,
        "Peso mexicano y el dólar se mantienen por ".$resultDollar[0]->dayspersistent." día el ".$diaHoy,
        "Dólar presenta ".$resultDollar[0]->dayspersistent." día(s) en mantenerse",
      );

      $bloqueEncabezadosProgresivoSemanasAlta = array(
        "Dólar presenta ".round($resultDollar[0]->dayspersistent/7)." semana(s) a la alza",
        "Registra dólar semana consecutiva a la alza en México",
      );
      $bloqueEncabezadosProgresivoSemanasBaja = array(
        "Dólar presenta ".round($resultDollar[0]->dayspersistent/7)." semana(s) a la baja",
        "Registra dólar semana consecutiva a la baja en México",
      );
      $bloqueEncabezadosProgresivoSemanasMantenerse = array(
        "Dólar presenta ".round($resultDollar[0]->dayspersistent/7)." semana(s) en mantenerse",
        "Registra dólar semana consecutiva de mantenerse en México",
      );

      $bloqueEntradaAlza = array(
        "El dólar se aprecia este día por arriba de la barrera de las ".floor($resultDollar[0]->dollar*1)." unidades ante el peso mexicano con una compra de ".$dollarBuying." pesos y venta de ".$dollarSelling." pesos.",
        "El tipo de cambio interbancario del dólar amanece en ".$dollar." pesos, lo que representa un alza en ".$resultDollar[0]->dollarWithYesterdayPercentage." respecto al peso mexicano, con un incremento de ".$dollarYesterday." pesos",
        "Al iniciar operaciones este ".$diaHoy.", el dólar está a la alza en ".$resultDollar[0]->dollarWithYesterdayPercentage." respecto al peso mexicano, con un incremento de ".$dollarYesterday." pesos.",
      );
      $bloqueEntradaBaja = array(
        "El peso se aprecia este día por debajo de la barrera de las ".floor($resultDollar[0]->dollar*1+1)." unidades ante una debilidad del dólar con una compra de ".$dollarBuying." pesos y venta de ".$dollarSelling." pesos.",
        "El tipo de cambio interbancario del dólar amanece en ".$dollar." pesos, lo que representa una baja en ".str_replace("-","",$resultDollar[0]->dollarWithYesterdayPercentage)."% respecto al peso mexicano, con una disminución de ".$dollarYesterday." pesos",
        "Al iniciar operaciones este ".$diaHoy.", el dólar está a la baja en ".str_replace("-","",$resultDollar[0]->dollarWithYesterdayPercentage)."% respecto al peso mexicano, con una disminución de ".$dollarYesterday." pesos.",
      );
      $bloqueEntradaMantener = array(
        "Dólar se mantiene ante peso mexicano y su valor sigue en ".floor($resultDollar[0]->dollar*1)." unidades",
        "El precio del dólar en México se mantiene en su valor en la compra y venta en las ventanillas del país este día ".$diaHoy."con un precio de $".$dollar." pesos",
        "El tipo de cambio interbancario del dólar amanece en ".$dollar." pesos, por lo que se mantiene en ".$dollarYesterday." pesos",
        "Al iniciar operaciones este ".$diaHoy.", el dólar se mantiene en $".$dollar." pesos",
      );

      $bloqueEntradaBajaIntermedia = array(
        "El peso rompió el techo de las ".floor($resultDollar[0]->dollar*1+1)." unidades para cerrar operaciones en su mejor nivel desde finales de enero",
      );

      $bloqueVentanilla = array(
        "El precio del dólar estadounidense en la compra, se promedia en ventanilla en $".$dollarBuying." pesos. Mientras que su valor en ventanilla por venta se promedió en $".$dollarSelling." pesos.",
        "Los valores del dólar por peso se encuentran a la compra en $".$dollarBuying.", mientras a la venta en $".$dollarSelling." pesos.",
        "La moneda americana cotiza este miércoles a $".$dollarBuying." pesos a la compra y $".$dollarSelling." pesos a la venta en ventanillas bancarias.",
        "El dólar se cotiza en ".$dollarBuying." pesos a la compra y ".$dollarSelling." pesos a la venta.",
        "El tipo de cambio interbancario se ubica en ".$dollar." pesos por dólar. A la compra en ".$dollarBuying.", mientras a la venta en ".$dollarSelling."."
      );


      $bloqueAñoAlza = array(
        "Desde el máximo que registró en ".strftime("%B")." del año pasado a la fecha, el dólar recuperó ".str_replace("-","",$resultDollar[0]->dollarYearPercentage)."%, es decir ".$resultDollar[0]->dollarYear." pesos",
        "En lo que va del año a la fecha, el comportamiento del dólar contra el peso mexicano está a la baja, con una disminución de ".str_replace("-","",$resultDollar[0]->dollarYear)." pesos y un porcentaje de ".str_replace("-","",$resultDollar[0]->dollarYearPercentage),
        "Con los valores actuales, en lo que va del año a la fecha, el comportamiento del dólar contra el peso mexicano también está a la alza, con un incremento de ".$resultDollar[0]->dollarYear." y un porcentaje de ".str_replace("-","",$resultDollar[0]->dollarYearPercentage)."."
      );

      $bloqueAñoBaja = array(
        "Desde el máximo que registró en ".strftime("%B")." del año pasado a la fecha, el peso mexicano recuperó ".str_replace("-","",$resultDollar[0]->dollarYearPercentage).", es decir ".str_replace("-","",$resultDollar[0]->dollarYear)." pesos",
        "En lo que va del año a la fecha, el comportamiento del dólar contra el peso mexicano está a la alza, con una ganancia de ".str_replace("-","",$resultDollar[0]->dollarYear)." pesos y un porcentaje de ".str_replace("-","",$resultDollar[0]->dollarYearPercentage),
        "Con los valores actuales, en lo que va del año a la fecha, el comportamiento del dólar contra el peso mexicano también está a la baja, con un decremento de ".$resultDollar[0]->dollarYear." pesos y un porcentaje de ".str_replace("-","",$resultDollar[0]->dollarYearPercentage)."."
      );

      $bloqueSemanaBaja = array(
        "Y es que, en esta semana el dólar ha registrado una caída de ".str_replace("-","",$resultDollar[0]->dollarWeekPercentage)."% frente al peso, la moneda americana se mantiene a la compra entre los ".$dollarBuying." y ".$dollar." pesos a la compra y entre los ".($dollar-0.485)." y ".$dollarSelling." pesos a la venta.",
        "La media de venta durante la última semana se ubicó en ".abs($dollarAverageLastWeek)." pesos, debajo de la semana inmediata anterior donde fue de ".abs($dollarAverage2Week)." pesos.",
        "Durante la semana el dólar ha registrado una caída de ".str_replace("-","",$resultDollar[0]->dollarWeekPercentage)." frente al peso, la moneda americana se mantiene a la compra entre los ".$resultMinMaxBuyingAndSellingLast2Week[0]->minimoCompra." y ".$resultMinMaxBuyingAndSellingLast2Week[0]->maximoCompra." pesos a la compra y entre los ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->minimoVenta)." y ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->maximoVenta)." pesos a la venta."
      );
      $bloqueSemanaAlza = array(
        "Y es que, en esta semana el dólar ha registrado el dólar ha registrado una alza de ".str_replace("-","",$resultDollar[0]->dollarWeekPercentage)." frente al peso, la moneda americana se mantiene a la compra entre los ".$dollarBuying." y ".$dollar." pesos a la compra y entre los ".($dollar-0.485)." y ".$dollarSelling." pesos a la venta.",
        "La media de venta durante la última semana se ubicó en ".$dollarAverageLastWeek." pesos, arriba de la semana inmediata anterior donde fue de ".$dollarAverage2Week." pesos.",
        "Durante la semana el dólar ha registrado una alza de ".str_replace("-","",$resultDollar[0]->dollarWeekPercentage)." frente al peso, la moneda americana se mantiene a la compra entre los ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->minimoCompra)." y ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->maximoCompra)." pesos a la compra y entre los ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->minimoVenta)." y ".abs($resultMinMaxBuyingAndSellingLast2Week[0]->maximoVenta)." pesos a la venta."
      );

      $bloqueMesBaja = array(
        "La baja de este día contrasta con lo que se ha manifestado en lo que va del mes a la fecha, con una ganancia de ".str_replace("-","",$resultDollar[0]->dollarMonthPercentage)."%, es decir, ".str_replace("-","",$resultDollar[0]->dollarMonth)." pesos",
        "Durante el mes ha registrado caídas de ".str_replace("-","",$resultMinMaxMonth[0]->minimo)." y ".str_replace("-","",$resultMinMaxMonth[0]->maximo)." cifras que concuerdan con los del trimestre",
      );

      $bloqueMesAlza = array(
        "La alza de este día contrasta con lo que se ha manifestado en lo que va del mes a la fecha, con una pérdida de ".str_replace("-","",$resultDollar[0]->dollarMonthPercentage)."%, es decir, ".str_replace("-","",$resultDollar[0]->dollarMonth)." pesos",
        "Durante el mes ha registrado caídas de ".str_replace("-","",$resultMinMaxMonth[0]->minimo)." y ".str_replace("-","",$resultMinMaxMonth[0]->maximo)." cifras que concuerdan con los del trimestre",
      );

      $bloqueRellenoParteUna = array(
        "Las variables del dólar han mantenido un descontrol en su valor en los bancos de México, una de las principales son las afectaciones que ha traído a la economía global la pandemia del coronavirus SARS-CoV-2.",
        "En todo el mundo, el precio del dólar ha sido cambiante, en una de las monedas más importantes pues es considerada universal, incursionando en casi todas las economías del planeta",
        "Cabe recordar que los mercados cambian de comportamiento de acuerdo a las decisiones que tomen los países, organismos financieros y empresas.",
        "Si quieres revisar los registros de días pasados del precio del dólar y el tipo de cambio, visita codigopostal.com",
      );
      $bloqueRellenoParteDos = array(
        "Cada banco en México presenta un valor variable en la compra y venta de dólares en el territorio de la República.",
        "Lo mismo pasa con los dólares que circulan en el mercado. Cuando existen muchos en circulación se dice que la moneda tiene liquidez y su precio baja. Y cuando sube, es básicamente por su poca disponibilidad frente a la demanda de la divisa.",
        "Las variaciones del mercado también afectan el precio de una moneda, por ejemplo: si muchos inversionistas deciden sacar su dinero de un país para llevarlo a otro, entonces la liquidez bajará y el precio de la moneda aumentará.",
        "Cabe recordar que los mercados cambian de comportamiento de acuerdo a las decisiones que tomen los países, organismos financieros y empresas."
      );
      $bloqueRellenoParteTres = array(
        "México lleva varios años con un tipo de cambio flotante con un grado de intervención del Banco de México, teniendo presente que en el régimen fijo la moneda se revalúa o evalúa mientras que en el régimen flexible o flotante la moneda se aprecia o deprecia. La modificación del tipo de cambio fijo al flotante ha permitido que la demanda y oferta sean los factores decisivos de la paridad",
        "Sabías que...en México, existe un régimen cambiario flexible desde el 22 diciembre de 1994 (el 21 se abandonó el régimen de desliz cambiario lo que efectivamente provocó una importante devaluación), después de experimentar con regímenes fijos (1954-1976) y otros mixtos como la flotación controlada y otros. El régimen flexible ha sido muy útil para estabilizar la economía mexicana en periodos de alta incertidumbre y quitar presión a variables como las tasas de interés y la política fiscal",
        "Sabías que...el dólar es la moneda de referencia mundial desde la Segunda Guerra Mundial. Ninguna otra moneda le ha hecho sombra desde entonces. Tampoco en los últimos 25 años, donde se le acercaron primero el yen y luego el euro",
      );

      $bloqueHistorico = array(
        "<p>Los datos durante la última semana del dólar:</p><p>".$resultHistorico[1]->fecha."</p><p>".$resultHistorico[2]->fecha."</p><p>".$resultHistorico[3]->fecha."</p><p>".$resultHistorico[4]->fecha."</p><p>".$resultHistorico[4]->fecha."</p><p>".$resultHistorico[5]->fecha."</p>",
        "<p>La cotización de compra-venta del dólar en la semana anterior fue: <p>".$resultHistorico[0]->fecha."</p><p>".$resultHistorico[1]->fecha."</p><p>".$resultHistorico[2]->fecha."</p><p>".$resultHistorico[3]->fecha."</p><p>".$resultHistorico[4]->fecha."</p><p>".$resultHistorico[5]->fecha."</p>"
      );

      $bloqueBidenAlza = array(
        "Si se considera el precio del dólar desde cuando toma posesión Joe Biden el 20 de enero del 2021 a la presidencia de los Estados Unidos, se presenta a la alza con un incremento de ".str_replace("-","",$resultDollar[0]->dollarBiden)." pesos y un porcentaje favorable de ".str_replace("-","",$resultDollar[0]->dollarBidenPercentage).".",
        "Y desde el 20 de enero del 2021 cuando tomo posesión Joe Biden registró una ganancia de los ".$resultBidenMinMaxUp[0]->minimaBiden." y ".$resultBidenMinMaxUp[0]->maximaBiden." sobre el peso."
      );

      // $bloqueBidenBaja = array(
      //   "Aunque si se considera el precio del dólar desde la llegada de Joe Biden a la presidencia de los Estados Unidos, se presenta a la alza con un incremento de ".str_replace("-","",$resultDollar[0]->dollarBiden)." pesos y un porcentaje favorable de ".str_replace("-","",$resultDollar[0]->dollarBidenPercentage)."%.",
      //   "Y desde el inicio del gobierno de Joe Biden registró una perdida de los ".$resultBidenMinMaxDown[0]->minimaBiden." y ".$resultBidenMinMaxDown[0]->maximaBiden." sobre el peso."
      // );

      $bloqueAMLOAlza = array(
        "Si se considera el precio del dólar desde cuando toma posesión AMLO el 01 de diciembre del 2018 a la presidencia de México, se presenta a la baja con ".str_replace("-","",$resultDollar[0]->dollarAMLO)." pesos y un porcentaje desfavorable de ".str_replace("-","",$resultDollar[0]->dollarAMLOPercentage).".",
        "Y desde el 01 de diciembre del 2018 cuando tomo posesión AMLO registró una perdida de los ".$resultAMLOMinMaxUp[0]->minimaAMLO." y ".$resultAMLOMinMaxUp[0]->maximaAMLO." sobre el peso."
      );

      $bloqueAMLOBaja = array(
        "Si se considera el precio del dólar desde cuanto toma posesión AMLO el 01 de diciembre del 2018 a la presidencia de México, se presenta a la alza con  ".str_replace("-","",$resultDollar[0]->dollarAMLO)." pesos y un porcentaje favorable de ".str_replace("-","",$resultDollar[0]->dollarAMLOPercentage).".",
        "Y desde el 01 de diciembre del 2018 cuando tomo posesión AMLO registró una ganancia de los ".str_replace("-","",$resultAMLOMinMaxDown[0]->minimaAMLO)." y ".str_replace("-","",$resultAMLOMinMaxDown[0]->maximaAMLO)." sobre el peso."
      );

      $bloqueParrafo = array(
        "El dólar es una de las monedas más importantes pues es considerada universal incursionando en casi todas las economías del planeta.",
        "¿Por qué es importante conocer el tipo de cambio? Sirve como indicador de la competitividad de un país con el resto del mundo ya que relaciona los precios internos de la producción nacional con los precios internacionales.",
        "El tipo de cambio es una referencia que se usa en el mercado cambiario para conocer el número de unidades de moneda nacional que deben pagarse para obtener una moneda extranjera, o similarmente, el número de unidades de moneda nacional que se obtienen al vender una unidad de moneda extranjera.
",
        "¿Qué determina el precio del dólar? La ley de la oferta y la demanda es el principal factor que determinan el precio de esta divisa. En México el tipo de cambio peso-dólar se determina bajo un régimen cambiario de libre flotación. Es la Comisión de Cambios la que faculta al Banco de México para llevar a cabo operaciones en el mercado cambiario."
      );

      $secondParagraph = rand(1,3);
      $thirdParagraph = rand(1,3);
      $fourthParagraph = rand(1,2);
      $fiveParagraph = rand(1,3);

      $arraySecondParagraphDown = array(
        $bloqueSemanaBaja,
        $bloqueMesBaja,
        $bloqueAñoBaja
      );

      $arraySecondParagraphUp = array(
        $bloqueSemanaAlza,
        $bloqueMesAlza,
        $bloqueAñoAlza
      );

      $arrayThirdParagrapDown = array(
        $bloqueAñoBaja,
        $bloqueAñoBaja
      );

      $arrayThirdParagrapUp = array(
        $bloqueAñoAlza,
        $bloqueAñoAlza
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

            //Creamos el lienzo con el tamaño para contener las 2 imagenes, y le asignamos transparencia
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
            $text1 = "Precio del dólar de hoy:";
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
