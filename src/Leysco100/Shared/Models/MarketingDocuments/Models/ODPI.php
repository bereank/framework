<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ODPI extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_d_p_i_s';

    public function items()
    {
        return $this->hasMany('App\DPI1', 'DocEntry');
    }
    public function outlet()
    {
        return $this->belongsTo('App\OCRD', 'DocEntry');
    }

    public function rows()
    {
        return $this->hasMany('App\DPI1', 'DocEntry');
    }

    public function objecttype()
    {
        return $this->belongsTo('App\APDI', 'ObjType', 'ObjectID');
    }
}
