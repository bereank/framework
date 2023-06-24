<?php

namespace Leysco\LS100SharedPackage\Models\Domains\FormSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\FormSetting\Models\FT100;

class FTR100 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'f_t_r100_s';

    public function tabletabs()
    {
        return $this->belongsTo(FT100::class, 'TabID');
    }
}
