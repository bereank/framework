<?php
namespace Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OSLP;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OTER;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\Tier;
use Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models\CRD15;
use Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models\OCLG;
use Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models\OCPR;
use Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models\PaymentTerm;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\ODPI;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\ORDR;

class OCRD extends Model
{
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
