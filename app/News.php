<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PostalCode;
use App\NewsStatus;
use App\Tag;
use Illuminate\Support\Facades\DB;

class News extends Model
{
    protected $table = "news";
    public function tags(){
      return $this->belongsToMany(Tag::class,"news_has_tags","new_id","tag_id");
    }
    public function postalcode(){
      return $this->belongsTo(PostalCode::class);
    }
    public function statusnews(){
      return $this->belongsTo(NewsStatus::class,"news_status","id_status_news");
    }
    public function newshasnews(){
      return $this->belongsToMany(News::class,"news_has_news","new_id","sub_new_id");
    }

    public function setPicturesAttribute($pictures)
    {
      if (is_array($pictures)) {
          $this->attributes['pictures'] = json_encode($pictures);
      }
    }

    public function getPicturesAttribute($pictures)
    {
      return json_decode($pictures, true);
    }
}
