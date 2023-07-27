<?php

namespace Leysco100\Shared\Models\Administration\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\Tier;
use Leysco100\Shared\Models\Administration\Models\Channel;
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
}
