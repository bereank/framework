<?php

namespace Leysco100\Shared\Models\Banking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\BusinessPartner\Models\OBPL;
use Leysco100\Shared\Models\MarketingDocuments\Models\OPRQ;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OVPM extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_v_p_m_s';

    public function vpm4()
    {
        return $this->hasMany(VPM4::class, 'DocNum');
    }

    public function vpm1()
    {
        return $this->hasMany(VPM1::class, 'DocNum');
    }

    public function branch()
    {
        return $this->belongsTo(OBPL::class, 'BPLId');
    }

    public function transaction()
    {
        return $this->belongsTo(OPRQ::class, 'BaseEntry');
    }
}
