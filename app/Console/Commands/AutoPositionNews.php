<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\DB;
use App\Model\NewsAuto;

class AutoPositionNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto posiciona notas con el modelo de machine learning';

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
      ini_set('memory_limit', '-1');

      $news = \DB::select("select * from news WHERE created_at >= now() - INTERVAL 1 DAY AND id_author != 100  AND id_status_news = 2 AND url is null and id = 472537 ORDER BY created_at DESC");

      $arrayStates = array();
      $asentas = \DB::select("SELECT id, CONCAT_WS('+', d_asenta,d_municipio,d_estado) as postalcode FROM postal_codes WHERE d_asenta != 'México' GROUP BY postalcode");
      foreach ($asentas as $key => $value) {
        $arrayStates[] = $value;
      }

      $states = \DB::select("SELECT id, CONCAT_WS('+', d_asenta,d_municipio,d_estado) as postalcode FROM postal_codes WHERE d_estado != 'México' GROUP BY d_estado");
      foreach ($states as $key => $value) {
        $arrayStates[] = $value;
      }

      foreach ($news as $key => $value) {
        $newsAuto = new NewsAuto($value,$arrayStates);
      }
    }
}
