<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:weather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Weather for each state of México and save in our database';

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
      //$url = "https://api.openweathermap.org/data/2.5/weather?q=León,37000&appid=3588ffc77f3674df4bb37c24ac9c6a7f";
      $url = "https://api.openweathermap.org/data/2.5/onecall?lat=21.1167&lon=-101.667&exclude=hourly,daily&units=metric&appid=3588ffc77f3674df4bb37c24ac9c6a7f";

      $c = curl_init($url);

      $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';


      curl_setopt($c, CURLOPT_USERAGENT, $config['useragent']);
      curl_setopt($c, CURLOPT_REFERER, 'https://www.eldolar.info/');

      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

      $html = curl_exec($c);

      if (curl_error($c)) die(curl_error($c));

      print_r(json_decode($html));
    }
}
