<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    protected $table = "postal_codes";
    protected $primaryKey = "id";

    public function news(){
      return $this->belongsToMany(News::class);
    }

}
