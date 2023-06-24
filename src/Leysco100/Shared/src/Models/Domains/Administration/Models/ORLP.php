<?php

namespace App\Domains\Administration\Models;

use App\Domains\Administration\Models\Tier;
use App\Domains\BusinessPartner\Models\Channel;
use Illuminate\Database\Eloquent\Model;

class ORLP extends Model
{
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
