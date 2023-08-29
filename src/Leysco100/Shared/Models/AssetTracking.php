<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class AssetTracking extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'asset_trackings';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }
}
