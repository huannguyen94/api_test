<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiNhanhModel extends Model
{
    protected $table 		= 'chi_nhanh';
    protected $primaryKey	= "cn_id";
    public $timestamps 		= false;
}
