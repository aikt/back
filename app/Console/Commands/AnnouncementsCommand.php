<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Api\Announcements;

class AnnouncementsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcements:getData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtener los datos de la Url (casas y autos)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function getHouseData()
    {
        $casas = (object) array(
            ["url" => "https://www.vivanuncios.com.mx/s-casas-en-venta/guanajuato/v1c1293l1010%s",
            "municipio" => 36354],
            ["url" => "https://www.vivanuncios.com.mx/s-casas-en-venta/leon/v1c1293l10339%s",
            "municipio" => 40190],
            ["url" => "https://www.vivanuncios.com.mx/s-casas-en-venta/irapuato-guanajuato/v1c1293l16840%s",
            "municipio" => 38146],
            ["url" => "https://www.vivanuncios.com.mx/s-casas-en-venta/salamanca-guanajuato/v1c1293l16638%s",
            "municipio" => 38521],
            ["url" => "https://www.vivanuncios.com.mx/s-casas-en-venta/celaya/v1c1293l10327%s",
            "municipio" => 43437],
            ["url" => "https://www.vivanuncios.com.mx/s-casas-en-venta/jalisco/v1c1293l1013%s",
            "municipio" => 57012],
            ["url" => "https://www.vivanuncios.com.mx/s-casas-en-venta/zapopan/v1c1293l14828%s",
            "municipio" => 57461],
            ["url" => "https://www.vivanuncios.com.mx/s-casas-en-venta/toluca/v1c1293l10758%s",
            "municipio" => 62720],
            ["url" => "https://www.vivanuncios.com.mx/s-casas-en-venta/distrito-federal/v1c1293l1008%s",
            "municipio" => 1000],
            ["url" => "https://www.vivanuncios.com.mx/s-venta-inmuebles/puebla/v1c1097l1020%s",
            "municipio" => 95644]
	);

        echo "******************************************************".PHP_EOL;
        echo "*********************   GetHouses  *******************".PHP_EOL;
        echo "******************************************************".PHP_EOL;

        foreach($casas as $casa){

            $casa = (object) $casa;

            for ($i=1; $i < 6; $i++) {
                $url = sprintf($casa->url, "p".$i);

                echo "Procesando Url: ".$url.PHP_EOL;


                Announcements::getHtmlDom($url, $casa->municipio);
                $result = Announcements::getHouseNodeElements();
                $result = \json_decode($result);

                echo "status: " . $result->status.PHP_EOL;
                echo "message: " . $result->message.PHP_EOL;
            }
        }
    }

    private function getCarData()
    {
        $autos = (object) array(
            ["url" => "https://www.vivanuncios.com.mx/s-venta-autos/guanajuato/v1c65l1010%s",
            "municipio" => 36354],
            ["url" => "https://www.vivanuncios.com.mx/s-venta-autos/leon/v1c65l10339%s",
            "municipio" => 40190],
            ["url" => "https://www.vivanuncios.com.mx/s-venta-autos/irapuato/v1c65l10336%s",
            "municipio" => 38146],
            ["url" => "https://www.vivanuncios.com.mx/s-venta-autos/salamanca-guanajuato/v1c65l16638%s",
            "municipio" => 38521],
            ["url" => "https://www.vivanuncios.com.mx/s-venta-autos/celaya/v1c65l10327%s",
            "municipio" => 43437],
            ["url" => "https://www.vivanuncios.com.mx/s-venta-autos/guadalajara/v1c65l14822%s",
            "municipio" => 57012],
            ["url" => "https://www.vivanuncios.com.mx/s-venta-autos/zapopan/v1c65l14828%s",
            "municipio" => 57461],
            ["url" => "https://www.vivanuncios.com.mx/s-venta-autos/toluca/v1c65l10758%s",
            "municipio" => 62720],
            ["url" => "https://www.vivanuncios.com.mx/s-venta-autos-camionetas/distrito-federal/v1c81l1008%s",
            "municipio" => 1000],
	    ["url" => "https://www.vivanuncios.com.mx/s-venta-autos-camionetas/puebla/v1c81l1020%s",
            "municipio" => 95644]
        );

        echo "******************************************************".PHP_EOL;
        echo "************************  GetCars  *******************".PHP_EOL;
        echo "******************************************************".PHP_EOL;

        foreach($autos as $auto){

            $auto = (object) $auto;

            for ($i=1; $i < 6; $i++) {

                $url = sprintf($auto->url, "p".$i);

                echo "Procesando Url: ".$url.PHP_EOL;

                Announcements::getHtmlDom($url, $auto->municipio);
                $result = Announcements::getCarsNodeElements();

                $result = \json_decode($result);

                echo "status: " . $result->status.PHP_EOL;
                echo "message: " . $result->message.PHP_EOL;
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //https://www.vivanuncios.com.mx/
        /*
        Guanajuato	36354
        Irapuato	38146
        Salamanca	38521
        León	    40190
        Celaya	    43437
        Guadalajara 57012
        Zapopan	    57461
        Toluca      62720
        */

        echo "Begin Announcements Command".PHP_EOL;
        $this->getCarData();
        $this->getHouseData();
        echo "End Announcements Command".PHP_EOL;

        return 0;
    }
}
