<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\MarketingDocuments\Models\RDR1;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OIGN extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_i_g_n_s';

    public function outlet()
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

    public function ign1()
    {
        return $this->hasMany(IGN1::class, 'DocEntry');
    }

    public function rows()
    {
        return $this->hasMany(IGN1::class, 'DocEntry');
    }

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }
}
