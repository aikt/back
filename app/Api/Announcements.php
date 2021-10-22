<?php

namespace App\Api;

include_once 'HtmlWeb.php';

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use simplehtmldom\HtmlWeb;

use Aws;


class Announcements extends Controller
{

    private static $doc;
    private static $home;
    private static $homeUrl;
    private static $municipio;

    /**
     * Inicializar Objeto DOMDocument
     *
     * @author Omar Cortes
     * @param string uri
     * @return null
     */
    public static function getHtmlDom(string $uri, int $municipio)
    {
        // Load the page into memory

        $post = strpos(str_replace('//', '||', $uri), '/');

        self::$home = str_replace("||", "//", substr($uri, 0, $post));
        self::$homeUrl = $uri;
        self::$municipio = $municipio;

        $d = new HtmlWeb();
        self::$doc = $d->load($uri);
    }

    /**
     * Obtener los datos de la Url mandada (Casas)
     *
     * @author Omar Cortes
     * @return json
     */
    public static function getHouseNodeElements()
    {
        $title = "";
        $detail = "";
        $description = "";
        $image = "";
        $price = "";
        $url = "";
        $type = "Casa";
        $id_web = "";
        $units = "";
        $rooms = "";
        $baths = "";
        $sizes = "";
        $parking = "";
        $message = "";
        $status = 500;

        $row = 0;

        $data = array();

        try {
            $html = self::$doc;

            if ($html->find('div.tileV2') !== null) {
                foreach ($html->find('div.tileV2') as $article) {

                    $object = $article->find('div.additional-attributes-container', 0);

                    if ($object->find('div.additional-attributes-text', 0) !== null) {
                        $units = $object->find('div.additional-attributes-text', 0);
                        $units = str_replace(array('<span class="text-value">', '</span>'), array("", " "), $units->innertext);
                    }

                    if ($object->find('div.re-bedroom', 0) !== null) {
                        $rooms = $object->find('div.re-bedroom', 0);
                        $rooms = $rooms->innertext;
                    }

                    if ($object->find('div.re-bathroom', 0) !== null) {
                        $baths = $object->find('div.re-bathroom', 0);
                        $baths = $baths->innertext;
                    }

                    if ($object->find('div.surface-area', 0) !== null) {
                        $sizes = $object->find('div.surface-area', 0);
                        $sizes = $sizes->innertext;
                    }

                    if ($object->find('div.car-parking', 0) !== null) {
                        $parking = $object->find('div.car-parking', 0);
                        $parking = $parking->innertext;
                    }

                    if ($article->find('div.tile-location', 0) !== null) {
                        $detail = $article->find('div.tile-location', 0);
                        $detail = str_replace(array("<b>", "</b>"), "", $detail->innertext);
                    }

                    if ($article->find('div.tile-desc', 0) !== null) {
                        $description = $article->find('div.tile-desc', 0);
                        $description = ucfirst(strtolower($description->innertext));
                    }

                    if ($article->find('div.expanded-description', 0) !== null) {
                        $d = $article->find('div.expanded-description', 0);
                        $description = ucfirst(strtolower($d->innertext));
                    }

                    if ($article->find('div.tile-desc a', 0) !== null) {
                        $url = $article->find('div.tile-desc a', 0);
                        $url = self::$home . $url->attr['href'];
                    }

                    if ($article->attr['data-tileadid'] !== null) {
                        $id_web = $article->attr['data-tileadid'];
                    }

                    if ($article->find('img.lazyloaded', 0) !== null) {
                        $i = $article->find('img.lazyloaded', 0);
                        $image .= $i->attr['src'] . "|";

                        $img_sng = explode('|', $image);
                        $url_img = $img_sng[0];
                        $url_arr = explode('/', $url_img);
                        $img_name = end($url_arr);
                        $url_key = 'images/' . $img_name;

                        $aws_upload_img = self::getImageFromAWSByUriImageExternal($url_img, $url_key);

                        if($aws_upload_img !== false)
                        {
                            $aws_image = $aws_upload_img;
                        }
                    }

                    if ($article->find('span.ad-price', 0) !== null) {
                        $price = $article->find('span.ad-price', 0)->innertext;
                    }

                    if ($article->find('span.tile-promotion', 0) !== null) {
                        $title = $article->find('span.tile-promotion', 0);
                        $title = str_replace('<span class="icon-desarrollo"></span>', "", $title->innertext);
                    }

                    $id = DB::table('announcementHouse')->where('id_web', $id_web)->count();

                    if ($id == 0) {

                        DB::table('announcementHouse')->insert(
                            [
                                "title" => $title,
                                "detail" => $detail,
                                "description" => $description,
                                "image" => $aws_image,
                                "price" => $price,
                                "url" => $url,
                                "type" => $type,
                                "id_web" => $id_web,
                                "units" => $units,
                                "rooms" => $rooms,
                                "baths" => $baths,
                                "sizes" => $sizes,
                                "parking" => $parking,
                                "idMunicipio" => self::$municipio
                            ]
                        );

                        $row++;

                        $message = "Se cargaron los registros";
                        $status = 201;
                    } else {
                        $message = "Registro duplicado";
                        $status = 200;
                    }
                }
            }

            $data = array(
                "status" => $status,
                "url" => self::$homeUrl,
                "rows" => $row,
                "message" => $message
            );
        } catch (Exception $ex) {
            $data = array(
                "status" => 501,
                "url" => self::$homeUrl,
                "rows" => 0,
                "message" => $ex->getMessage()
            );
        }

        return \json_encode($data);
    }

    /**
     * Obtener los datos de la Url mandada (Carros)
     *
     * @author Omar Cortes
     * @return json
     */
    public static function getCarsNodeElements()
    {
        $brand = "";
        $model = "";
        $year = "";
        $mileage = "";
        $bodywork = "";
        $color = "";
        $transmission = "";
        $traction = "";
        $fuel = "";
        $airconditioning = "";
        $seller = "";
        $descripction = "";
        $id_web = "";
        $row = 0;
        $url = "";
        $subUrl = "";
        $title = "";
        $price = "";
        $image = "";
        $message = "";
        $status = 500;

        try {

            $html = self::$doc;

            if ($html->find('div.tileV2',0) !== null) {
                foreach ($html->find('div.tileV2') as $article) {

                    $url = self::$homeUrl;

                    if ($article->find('div.tile-contact-us', 0) !== null) {
                        $object = $article->find('div.tile-contact-us', 0);
                        $id_web = trim($object->attr['data-tileadid']);
                    }



                    if ($article->find('div.tile-desc a', 0) !== null) {
                        $object = $article->find('div.tile-desc a', 0);
                        $subUrl = self::$home . $object->attr['href'];
                    }

                    if ($subUrl !== "") {

                        $detalle = self::getDataSubCars($subUrl);

                        if ($detalle->find('img.slick-carousel', 0) !== null) {
                            $image = $detalle->find('img.slick-carousel', 0);
                            $image = $image->attr['src'];

                            $url_arr = explode('/', $image);
                            $img_name = end($url_arr);
                            $url_key = 'images/' . $img_name;

                            $aws_upload_img = self::getImageFromAWSByUriImageExternal($image, $url_key);

                            if($aws_upload_img !== false)
                            {
                                $image = $aws_upload_img;
                            }

                        }

                        if ($detalle->find('span.ad-title', 0) !== null) {
                            $object = $detalle->find('span.ad-title', 0);
                            $title = trim($object->innertext);
                        }

                        if ($detalle->find('span.ad-price', 0)) {
                            $object = $detalle->find('span.ad-price', 0);
                            $price = trim($object->innertext);
                        }

                        if ($detalle->find('ul.category-pri-attrs', 0) !== null) {

                            $item = $detalle->find('ul.category-pri-attrs', 0);

                            if ($item->find('li a', 0) !== null) {
                                $brand = $item->find('li a', 0);
                                $brand = $brand->innertext;
                            }

                            if ($item->find('li a', 1) !== null) {
                                $model = $item->find('li a', 1);
                                $model = $model->innertext;
                            }

                            if ($item->find('li span', 3) !== null) {
                                $year = $item->find('li span', 3);
                                $year = $year->innertext;
                            }

                            if ($item->find('li span', 5) !== null) {
                                $mileage = $item->find('li span', 5);
                                $mileage = $mileage->innertext;
                            }

                            if ($item->find('li span', 7) !== null) {
                                $bodywork = $item->find('li span', 7);
                                $bodywork = $bodywork->innertext;
                            }

                            if ($item->find('li span', 9) !== null) {
                                $color = $item->find('li span', 9);
                                $color = $color->innertext;
                            }

                            if ($item->find('li span', 11) !== null) {
                                $transmission = $item->find('li span', 11);
                                $transmission = $transmission->innertext;
                            }

                            if ($item->find('li span', 13) !== null) {
                                $traction = $item->find('li span', 13);
                                $traction = $traction->innertext;
                            }

                            if ($item->find('li span', 15) !== null) {
                                $fuel = $item->find('li span', 15);
                                $fuel = $fuel->innertext;
                            }

                            if ($item->find('li span', 17) !== null) {
                                $airconditioning = $item->find('li span', 17);
                                $airconditioning = $airconditioning->innertext;
                            }

                            if ($item->find('li span', 19) !== null) {
                                $seller = $item->find('li span', 19);
                                $seller = $seller->innertext;
                            }
                        }

                        if ($detalle->find('div.sec-attrs', 0) !== null) {
                            $descripction = $detalle->find('div.sec-attrs', 0)->innertext;
                        }

                        $id = DB::table('announcementCar')->where('id_web', $id_web)->count();

                        if ($id == 0) {

                            DB::table('announcementCar')->insert(
                                [
                                    "title" => $title,
                                    "price" => $price,
                                    "image" => $image,
                                    "brand" => $brand,
                                    "model" => $model,
                                    "year" => $year,
                                    "mileage" => $mileage,
                                    "bodywork" => $bodywork,
                                    "color" => $color,
                                    "transmission" => $transmission,
                                    "traction" => $traction,
                                    "fuel" => $fuel,
                                    "airconditioning" => $airconditioning,
                                    "seller" => $seller,
                                    "descripction" => $descripction,
                                    "id_web" => $id_web,
                                    "url" => $url,
                                    "subUrl" => $subUrl,
                                    "idMunicipio" => self::$municipio
                                ]
                            );

                            $row++;
                            $message = "Se cargaron los registros";
                            $status = 201;
                        } else {
                            $status = 200;
                            $message = "Registro duplicado";
                        }
                    }
                }
            }

            $data = array(
                "status" => $status,
                "url" => self::$homeUrl,
                "rows" => $row,
                "message" => $message
            );
        } catch (Exception $ex) {
            $data = array(
                "status" => 501,
                "url" => self::$homeUrl,
                "rows" => 0,
                "message" => $ex->getMessage()
            );
        }

        return \json_encode($data);
    }

    private static function getDataSubCars(string $uri)
    {
        try {
            $d = new HtmlWeb();
            return $d->load($uri);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }


    private static function getImageFromAWSByUriImageExternal($uriImageExternal,$folderImage){
        $s3 = new Aws\S3\S3Client([
          'region'  => 'us-east-2',
          'version' => 'latest',
          'credentials' => [
            'key'    => "AKIAIQS7HMCD7GJJMLNA",
            'secret' => "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",
          ]
        ]);

        $getImage = @file_get_contents($uriImageExternal);
        if($getImage !== FALSE){
          $result = $s3->putObject([
            'Bucket' => 'copoadminpro',
            'Key'    => $folderImage,
            'Body' => $getImage
          ]);

          $urlImageFromAWS = $result->get("ObjectURL");
          if(!empty($urlImageFromAWS)) {
            return $urlImageFromAWS;
          }else return false;
        }else return false;
     }
}
