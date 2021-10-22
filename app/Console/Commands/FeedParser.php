<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\DB;
use App\Model\Feed;

class FeedParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:parser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera el parseo de los feeds para la tabla noticias';

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

        $feeds = \DB::select("select * from feed");
	foreach ($feeds as $key => $value) {
          $feed = new Feed($value);
        }
    }
}
