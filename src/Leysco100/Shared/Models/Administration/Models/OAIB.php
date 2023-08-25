<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OAIB extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_a_i_b_s';

    public function oalr()
    {
        return $this->belongsTo(OALR::class, 'AlertCode');
    }
}
