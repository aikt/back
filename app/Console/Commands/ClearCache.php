<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class ClearCache extends Command
{
	/**
 	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'news:clearcache';

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
	 * @return int
	 */
	public function handle()
	{
		$arr_urls = [];
		$file = '/var/www/laravel/public/urls.txt';
		// $file = 'urls.txt';
		$lines = file('/var/www/laravel/public/urls.txt');
		// $lines = file('urls.txt');
		$lines = array_unique($lines);

		if(count($lines) > 0){
			foreach($lines as $line)
			{
				$url_api = "https://api.codigopostal.com/api/v1/clear-cache";
				$update_api = self::update_url($url_api);

				if($update_api != 200)
				{
					echo $url_api . "\n";
					echo 'HTTP code: ' . $update_api . "\n";
				}

				echo $url_api . "\n";
				echo 'HTTP code: ' . $update_api . "\n";

				$url_front = "https://codigopostal.com/clear-cache";
				$update_front = self::update_url($url_front);

				if($update_front != 200)
				{
					echo $url_front . "\n";
					echo 'HTTP code: ' . $update_front . "\n";
				}

				echo $url_front . "\n";
				echo 'HTTP code: ' . $update_api . "\n";

				$parts = explode('/', rtrim($line, '/'));
				// elimina el titulo
				array_pop($parts);
				// elimina el cp
				array_pop($parts);

				$estado    = isset($parts[0]) ? $parts[0] : '';
				$municipio = isset($parts[1]) ? $parts[1] : '';
				$copo      = isset($parts[2]) ? $parts[2] : '';

				$url_copo      = $estado . '/' . $municipio . '/' . $copo;
				$url_municipio = $estado . '/' . $municipio;
				$url_estado    = $estado;

				$url_base = "https://www.codigopostal.com";
				$prefix_cache = "?actualizacache=true";

				$update_url_base = self::update_url($url_base . $prefix_cache);

				if($update_url_base != 200)
				{
					echo $url_base . $prefix_cache . "\n";
					echo 'HTTP code: ' . $update_url_base . "\n";
				}

				echo $url_base . $prefix_cache . "\n";
				echo 'HTTP code: ' . $update_url_base . "\n";

				array_push($arr_urls, $url_copo);
				array_push($arr_urls, $url_municipio);
				array_push($arr_urls, $url_estado);
				array_push($arr_urls, $line);

				$uniques_urls = array_unique($arr_urls);

				array_walk($uniques_urls, function(&$value, $key)
					use($url_base, $prefix_cache)
				{
					$value = $url_base . $value . $prefix_cache;
				});

				foreach($uniques_urls as $url)
				{
					$rsp_url = self::update_url($url);

					if($rsp_url != 200)
					{
						echo $url . "\n";
						echo 'HTTP code: ' . $rsp_url . "\n";
					}

					echo $url . "\n";
					echo 'HTTP code: ' . $rsp_url . "\n";
				}
			}
			print_r($uniques_urls);
			file_put_contents($file, '');
		}


	}

	private static function update_url(string $url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT,10);
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return $httpcode;
	}
}
