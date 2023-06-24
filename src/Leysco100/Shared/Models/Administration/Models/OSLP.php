<?php

namespace App\Domains\Administration\Models;

use App\Domains\BusinessPartner\Models\Channel;
use App\Domains\BusinessPartner\Models\OCLG;
use Illuminate\Database\Eloquent\Model;

class OSLP extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_s_l_p_s';

    public function regions()
    {
        return $this->hasMany(SLP1::class, 'SlpCode');
    }

    public function calls()
    {
        return $this->hasMany(OCLG::class, 'SlpCode');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'ChannCode');
    }

    public function tier()
    {
        return $this->belongsTo(Tier::class, 'TierCode');
    }
}
