<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DescriptionPostalCodes extends Model
{
    protected $table = "description_postal_codes";

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
