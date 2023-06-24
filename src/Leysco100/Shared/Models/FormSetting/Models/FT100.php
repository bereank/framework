<?php

namespace App\Domains\FormSetting\Models;

use Illuminate\Database\Eloquent\Model;

class FT100 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'f_t100_s';


    public function tablerows()
    {
        return $this->hasMany(FTR100::class, 'TabID');
    }
}
