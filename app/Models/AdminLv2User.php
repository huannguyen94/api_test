<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLv2User extends Model
{
    protected $table 		= 'admin_lv2_user';
    protected $primaryKey	= "adm_id";
    public $timestamps 		= false;
}
