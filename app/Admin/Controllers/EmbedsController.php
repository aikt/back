<?php

namespace App\Admin\Controllers;

use App\Embeds;
use App\PostalCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;


class EmbedsController extends AdminController
{
     /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Embeds';


    public function GetInfoEmbedsClasificados()
    {
      $embeds = \DB::table('embeds')
        ->join('postal_codes', 'embeds.id_cp', '=', 'postal_codes.id')
        ->where('type', '=', 0)
        ->select('embeds.*', 'postal_codes.d_estado')->orderBy('embeds.created_at','DESC')
        ->simplePaginate(10);

      return  response()->view(
        'embeds',
        array(
          "embeds" => $embeds,
        )
      );
    }

    public function GetInfoEmbedsEmpleos()
    {
      $embeds = \DB::table('embeds')
        ->join('postal_codes', 'embeds.id_cp', '=', 'postal_codes.id')
        ->where('type', '=', 1)
        ->select('embeds.*', 'postal_codes.d_estado')->orderBy('embeds.created_at','DESC')
        ->simplePaginate(10);

      return  response()->view(
        'embedsempleos',
        array(
          "embeds" => $embeds,
        )
      );
    }
}
