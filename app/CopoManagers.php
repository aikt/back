<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CopoManagers extends Model
{
    protected $table = "copo_managers";
    
    public function managerhasmessages()
    {
        return $this->belongsToMany(CopoManagers::class,"managers_has_messages","id_copo_manager","id_copo_message");
    }
}