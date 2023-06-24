<?php

namespace Leysco\LS100SharedPackage\Models\Domains\FormSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\FormSetting\Models\FTR100;

class FT100 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'f_t100_s';

    public function tablerows()
    {
        return $this->hasMany(FTR100::class, 'TabID');
    }
}
