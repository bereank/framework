<?php

namespace Leysco100\Shared\Models\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\AssetTracking;
use Leysco100\Shared\Models\LogisticsHub\Models\OCLG;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\Administration\Models\OTER;
use Leysco100\Shared\Models\Administration\Models\Tier;
use Leysco100\Shared\Models\Administration\Models\Channel;
use Leysco100\Shared\Models\MarketingDocuments\Models\ODLN;
use Leysco100\Shared\Models\MarketingDocuments\Models\ODPI;
use Leysco100\Shared\Models\MarketingDocuments\Models\OINV;
use Leysco100\Shared\Models\MarketingDocuments\Models\ORDR;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OCRD extends Model
{

    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_c_r_d_s';

    public function call()
    {
        return $this->hasMany(OCLG::class, 'CardCode');
    }

    public function order()
    {
        return $this->hasMany(ORDR::class, 'CardCode');
    }
    public function assets()
    {
        return $this->hasMany(AssetTracking::class, 'CardCode');
    }

    public function delivery()
    {
        return $this->hasMany(ODLN::class, 'CardCode');
    }
    public function invoice()
    {
        return $this->hasMany(OINV::class, 'CardCode');
    }

    public function contacts()
    {
        return $this->hasMany(OCPR::class, 'CardCode');
    }
    public function downPayInvoice()
    {
        return $this->hasMany(ODPI::class, 'CardCode');
    }

    //For Graphql
    public function orders()
    {
        return $this->hasMany(ORDR::class, 'CardCode');
    }

    public function channels()
    {
        return $this->belongsTo(Channel::class, 'ChannCode');
    }

    public function tiers()
    {
        return $this->belongsTo(Tier::class, 'TierCode');
    }

    public function tier()
    {
        return $this->belongsTo(Tier::class, 'TierCode');
    }
    public function territory()
    {
        return $this->belongsTo(OTER::class, 'Territory');
    }

    public function region()
    {
        return $this->belongsTo(OTER::class, 'Territory');
    }

    public function employees()
    {
        return $this->belongsTo(OSLP::class, 'SlpCode');
    }

    public function crd15()
    {
        return $this->hasMany(CRD15::class, 'CardCode');
    }

    public function octg()
    {
        return $this->belongsTo(PaymentTerm::class, 'GroupNum', 'GroupNum');
    }
}
