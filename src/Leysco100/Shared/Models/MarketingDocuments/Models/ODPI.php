<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ODPI extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_d_p_i_s';

    public function items()
    {
        return $this->hasMany(DPI1::class, 'DocEntry');
    }
    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'DocEntry');
    }

    public function rows()
    {
        return $this->hasMany(DPI1::class, 'DocEntry');
    }

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }
}
