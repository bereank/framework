<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ORLP extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_r_l_p_s';

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'ChannCode');
    }

    public function tier()
    {
        return $this->belongsTo(Tier::class, 'TierCode');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
