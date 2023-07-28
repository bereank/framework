<?php


namespace Leysco100\Shared\Models\Administration\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\BusinessPartner\Models\OCLG;
use Leysco100\Shared\Models\Administration\Models\Channel;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OSLP extends Model
{

    use UsesTenantConnection;
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
