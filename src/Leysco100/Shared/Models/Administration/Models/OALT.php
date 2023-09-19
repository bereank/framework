<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OALT extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_a_l_t';


    public function alt1()
    {
        return $this->hasMany(ALT1::class, 'DocEntry');
    }
}