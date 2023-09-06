<?php

namespace Leysco100\Shared\Models\Banking\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OPDF extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_p_d_f_s';

    public function pdf2()
    {
        return $this->hasMany(PDF2::class, 'DocNum');
    }

    public function pdf1()
    {
        return $this->hasMany(PDF1::class, 'DocNum');
    }
}
