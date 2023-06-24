<?php

namespace App\Models;

use App\Domains\BusinessPartner\Models\OCRD;
use Illuminate\Database\Eloquent\Model;

class AssetTracking extends Model
{
    protected $guarded = ['id'];
    protected $table = 'asset_trackings';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }
}
