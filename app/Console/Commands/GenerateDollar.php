<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateDollar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:dollar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Dollar information and save in our database';

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
      $url = "https://www.eldolar.info/en/mexico/dia/hoy";

      $c = curl_init($url);

      $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';


      curl_setopt($c, CURLOPT_USERAGENT, $config['useragent']);
      curl_setopt($c, CURLOPT_REFERER, 'https://www.eldolar.info/');

      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

      $html = curl_exec($c);

      if (curl_error($c)) die(curl_error($c));

      //Variables

      $dolarValue = 0;
      $dolarDayBeforeValue = 0;
      $dolarDayBeforePercentageValue = 0;
      $buyingUsaValue = 0;
      $sellingUsaValue = 0;
      $weekToDateValue = 0;
      $weekToDatePercentageValue = 0;
      $monthToDateValue = 0;
      $monthToDatePercentageValue = 0;
      $quarterToDateValue = 0;
      $quarterDatePercentageValue = 0;
      $yearToDateValue = 0;
      $yearDatePercentageValue = 0;
      $bidenValue = 0;
      $bidenPercentageValue = 0;
      $amloValue = 0;
      $amloPercentageValue = 0;
      $tableOthersBanks = "";

      $persistence = "";
      $dayspersistence = 0;


      preg_match_all(
          '/Dollar.=.<span.class=xTimes>(\d{1,}.\d{1,})<\/span>.Pesos<\/strong><\/h1><p.class=.(change.down|change.up|change.no)..title=(\w+|"(.*)?")>....((-|)\d{1,}.\d{1,}).((-|)\d{1,}(.\d{1,}|)%)<\/p>/',
          $html,
          $matchesDollarValue,
          PREG_PATTERN_ORDER
      );


      if(isset($matchesDollarValue[1][0]) && isset($matchesDollarValue[5][0]) && isset($matchesDollarValue[7][0])){
        $dolarValue = $matchesDollarValue[1][0];
        $dolarDayBeforeValue = $matchesDollarValue[5][0];
        $dolarDayBeforePercentageValue = $matchesDollarValue[7][0];



        preg_match_all(
            '/Buy<span.class=xTimes>(\d{1,}.\d{1,})<\/span>/',
            $html,
            $matchesBuyingUsaValue,
            PREG_PATTERN_ORDER
        );


        if(isset($matchesBuyingUsaValue[1][0])){
          $buyingUsaValue = $matchesBuyingUsaValue[1][0];


          preg_match_all(
              '/Sell<span.class=xTimes>(\d{1,}.\d{1,})<\/span>/',
              $html,
              $matchesSellingUsaValue,
              PREG_PATTERN_ORDER
          );

          if(isset($matchesSellingUsaValue[1][0])){
            $sellingUsaValue = $matchesSellingUsaValue[1][0];

            preg_match_all(
                '/Week<span.class=small-hide>.To.Date<\/span><\/span>&nbsp;<a.href=\d{1,}.class="(change.down|change.up|change.no)">....((-|)\d{1,}.\d{1,}).((-|)\d{1,}(.\d{1,}|)%)<\/a>/',
                $html,
                $matchesWeekToDateValue,
                PREG_PATTERN_ORDER
            );

            if(isset($matchesWeekToDateValue[2][0]) && isset($matchesWeekToDateValue[4][0])){
              $weekToDateValue = $matchesWeekToDateValue[2][0];
              $weekToDatePercentageValue = $matchesWeekToDateValue[4][0];

              preg_match_all(
                  '/Month<span.class=small-hide>.To.Date<\/span><\/span>&nbsp;<a.href=\d{1,}.class="(change.down|change.up|change.no)">....((-|)\d{1,}.\d{1,}).((-|)\d{1,}(.\d{1,}|)%)<\/a>/',
                  $html,
                  $matchesMonthToDateValue,
                  PREG_PATTERN_ORDER
              );

              if(isset($matchesMonthToDateValue[2][0]) && isset($matchesMonthToDateValue[4][0])){
                $monthToDateValue = $matchesMonthToDateValue[2][0];
                $monthToDatePercentageValue = $matchesMonthToDateValue[4][0];

                preg_match_all(
                    '/Quarter<span.class=small-hide>.To.Date<\/span><\/span>&nbsp;<a.href=\d{1,}.class="(change.down|change.up|change.no)">....((-|)\d{1,}.\d{1,}).((-|)\d{1,}(.\d{1,}|)%)<\/a>/',
                    $html,
                    $matchesQuarterToDateValue,
                    PREG_PATTERN_ORDER
                );

                if(isset($matchesQuarterToDateValue[2][0]) && isset($matchesQuarterToDateValue[4][0])){
                  $quarterToDateValue = $matchesQuarterToDateValue[2][0];
                  $quarterDatePercentageValue = $matchesQuarterToDateValue[4][0];

                  preg_match_all(
                      '/Year<span.class=small-hide>.To.Date<\/span><\/span>&nbsp;<a.href=\d{1,}.class="(change.down|change.up|change.no)">....((-|)\d{1,}.\d{1,}).((-|)\d{1,}(.\d{1,}|)%)<\/a>/',
                      $html,
                      $matchesYearToDateValue,
                      PREG_PATTERN_ORDER
                  );

                  if(isset($matchesYearToDateValue[2][0]) && isset($matchesYearToDateValue[4][0])){
                    $yearToDateValue = $matchesYearToDateValue[2][0];
                    $yearDatePercentageValue = $matchesYearToDateValue[4][0];

                    preg_match_all(
                        '/Biden<\/span>&nbsp;<a.href=\d{1,}.class=.(change.down|change.up|change.no).>....((-|)\d{1,}.\d{1,}).((-|)\d{1,}(.\d{1,}|)%)<\/a>/',
                        $html,
                        $matchesBidenValue,
                        PREG_PATTERN_ORDER
                    );

                    if(isset($matchesBidenValue[2][0]) && isset($matchesBidenValue[4][0])){
                      $bidenValue = $matchesBidenValue[2][0];
                      $bidenPercentageValue = $matchesBidenValue[4][0];


                      preg_match_all(
                          '/AMLO<\/span>&nbsp;<a.href=\d{1,}.class=.(change.down|change.up|change.no).>....((-|)\d{1,}.\d{1,}).((-|)\d{1,}(.\d{1,}|)%)<\/a>/',
                          $html,
                          $matchesAMLOValue,
                          PREG_PATTERN_ORDER
                      );

                      if(isset($matchesAMLOValue[2][0]) && isset($matchesAMLOValue[4][0])){
                        $amloValue = $matchesAMLOValue[2][0];
                        $amloPercentageValue = $matchesAMLOValue[4][0];
                        $curdate      = date("Y-m-d H:i:s");


                        preg_match_all(
                            '/(<table.id=\w+>.*<\/table>)/',
                            $html,
                            $matchesTableHtml,
                            PREG_PATTERN_ORDER
                        );

                        if(isset($matchesTableHtml[0][0])){
                          $tableOthersBanks = addslashes(html_entity_decode(trim($matchesTableHtml[0][0])));

                          $resultDollarYesterday = \DB::select("SELECT * FROM dollar ORDER BY created_at DESC LIMIT 1 OFFSET 0");

                          if(isset($resultDollarYesterday[0])){

                            $resultPersistence = number_format($dolarDayBeforeValue,4,".","");

                            if($resultPersistence > "0.0050"){
                              $persistence = "alza";
                              if($resultDollarYesterday[0]->persistent == "alza"){
                                $dayspersistence = (int)$resultDollarYesterday[0]->dayspersistent + 1;
                              }else{
                                $dayspersistence = 0;
                              }
                            }else if($resultPersistence < "-0.0050"){
                              $persistence = "baja";
                              if($resultDollarYesterday[0]->persistent == "baja"){
                                $dayspersistence = (int)$resultDollarYesterday[0]->dayspersistent + 1;
                              }else{
                                $dayspersistence = 0;
                              }
                            }else{
                              $persistence = "mantiene";
                              if($resultDollarYesterday[0]->persistent == "mantiene"){
                                $dayspersistence = (int)$resultDollarYesterday[0]->dayspersistent + 1;
                              }else{
                                $dayspersistence = 0;
                              }
                            }
                          }


                          \DB::insert("INSERT INTO `dollar`(`dollar`,`dollarWithYesterday`,`dollarWithYesterdayPercentage`,`dollarBuying`,`dollarSelling`,`dollarWeek`,`dollarWeekPercentage`,`dollarMonth`,`dollarMonthPercentage`,`dollarQuarter`,`dollarQuarterPercentage`,`dollarYear`,`dollarYearPercentage`,`dollarBiden`,`dollarBidenPercentage`,`dollarAMLO`,`dollarAMLOPercentage`,`created_at`,`htmlDollarOthers`,`persistent`,`dayspersistent`)
                                       VALUES ('$dolarValue','$dolarDayBeforeValue','$dolarDayBeforePercentageValue','$buyingUsaValue','$sellingUsaValue','$weekToDateValue','$weekToDatePercentageValue','$monthToDateValue','$monthToDatePercentageValue','$quarterToDateValue','$quarterDatePercentageValue','$yearToDateValue','$yearDatePercentageValue','$bidenValue','$bidenPercentageValue','$amloValue','$amloPercentageValue','$curdate','$tableOthersBanks','$persistence',$dayspersistence)");

                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
}
