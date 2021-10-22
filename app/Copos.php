<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PostalCode;

class Copos extends Model
{
    protected $table = "copos";
    public function postalcodes(){
      return $this->belongsToMany(PostalCode::class,"copos_postalcodes","copo_id","postal_code_id");
    }
    public function coposhascopos(){
      return $this->belongsToMany(Copos::class,"copos_has_copos","copo_id","sub_copo_id");
    }
    public function getIdcpsAttribute($value){
      return explode(",",$value);
    }
    public function setIdcpsAttribute($value){
      $this->attributes["id_cps"] = implode(",",$value);
    }

    public function setImagesAttribute($images)
    {
        if (is_array($images)) {
            $this->attributes['images'] = json_encode($images);
        }
    }

    public function getImagesAttribute($images)
    {
        return json_decode($images, true);
    }
}
