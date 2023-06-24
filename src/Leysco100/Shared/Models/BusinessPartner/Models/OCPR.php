<?php

namespace App\Domains\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;

class OCPR extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_c_p_r_s';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }
}
