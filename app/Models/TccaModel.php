<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TccaModel extends Model
{
    protected $table = 'tc_ca';
    public $timestamps 		= false;
    protected $fillable =[
        'ma_ca',
        'ten_ca',
        'gio_bat_dau',
        'gio_ket_thuc',
        'ghi_chu',
        'trang_thai',
        'last_update_by',
        'last_update_date',
    ];
}
