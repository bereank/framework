<?php

namespace Leysco100\Shared\Models\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OOAT extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_o_a_t_s';

    public function oat1()
    {
        return $this->hasMany(OAT1::class, 'AgrNo');
    }

    public function oat4()
    {
        return $this->hasMany(OAT4::class, 'AgrNo');
    }

    public function Items()
    {
        return $this->hasMany(OAT1::class, 'AgrNo');
    }

    public function buyer()
    {
        return $this->belongsTo(OCRD::class, 'BpCode');
    }
}
