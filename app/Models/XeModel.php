<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XeModel extends Model
{
    protected $table 		= 'xe';
    protected $primaryKey	= "xe_id";
    public $timestamps 		= false;
}
