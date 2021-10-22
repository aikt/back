<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PublishProgramedNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new:publishnew';

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
     * @return mixed
     */
    public function handle()
    {
        //get news with programed status
        $news = \DB::select("SELECT id FROM news WHERE id_status_news = 7 AND news_programed >= DATE_SUB(NOW(),INTERVAL 1 HOUR);");

        foreach($news as $value)
        {
            $publish = \DB::update("UPDATE news SET id_status_news = 1 WHERE id = {$value->id}");
        }
    }
}
