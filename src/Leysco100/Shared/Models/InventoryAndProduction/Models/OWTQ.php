<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\Administration\Models\OUDP;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OBPL;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\MarketingDocuments\Models\RDR1;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OWTQ extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_w_t_q_s';

    protected $appends = array('state');
    public function ocrd()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function items()
    {
        return $this->hasMany(RDR1::class, 'DocEntry');
    }
    public function department()
    {
        return $this->belongsTo(OUDP::class, 'Department');
    }
    public function rdr1()
    {
        return $this->hasMany(RDR1::class, 'DocEntry');
    }

    public function rows()
    {
        return $this->hasMany(WTQ1::class, 'DocEntry');
    }

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }

    public function BusinessPartner()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
    }

    public function branch()
    {
        return $this->belongsTo(OBPL::class, 'BPLId', 'BPLId');
    }
    public function location()
    {
        return $this->belongsTo(OLCT::class, 'LocationCode');
    }
    public function oslp()
    {
        return $this->belongsTo(OSLP::class, 'SlpCode', 'SlpCode');
    }

    public function getStateAttribute()
    {
        if ($this->status == "O") {
            return "Open";
        }

        if ($this->status == "C") {
            return "Closed";
        }
    }
}
