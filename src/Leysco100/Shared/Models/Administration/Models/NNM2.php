<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class NNM2 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'n_n_m2_s';

    public function nnm1()
    {
        return $this->belongsTo(NNM1::class, 'Series');
    }
}
