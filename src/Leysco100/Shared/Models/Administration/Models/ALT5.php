<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Leysco100\Shared\Models\OUQR;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ALT5 extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'a_l_t5';

    public function attachmt_query()
    {
        return $this->belongsTo(ALT6::class, 'DocEntry');
    }
}
