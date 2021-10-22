<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PostalCode;
use App\NewsStatus;
use App\Tag;
use Illuminate\Support\Facades\DB;

class NewsSections extends Model
{
    protected $table = "news_sections";
}
