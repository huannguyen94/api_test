<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DieuDoTempModel extends Model
{
    protected $table 		= 'dieu_do_temp';
    protected $primaryKey	= "did_id";
    public $timestamps 		= false;
}
