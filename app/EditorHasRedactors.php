<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PostalCode;
use App\NewsStatus;

class EditorHasRedactors extends Model
{
    protected $table = "editor_has_redactors";
    public $timestamps = false;
}
