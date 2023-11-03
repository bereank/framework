<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OALR extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_a_l_r_s';

    public function odrf()
    {
        return $this->belongsTo(ODRF::class, 'DraftEntry');
    }

    public function sendby()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function alert_template()
    {
        return $this->belongsTo(OALT::class, 'TCode');
    }
}
