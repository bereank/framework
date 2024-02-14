<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\Administration\Models\OTER;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class CRD16 extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
    public function route()
    {
        return $this->belongsTo(ORPS::class, "RouteID", "id");
    }

    public function bpartner()
    {
        return $this->belongsTo(OCRD::class, "CardCode", "CardCode");
    }
}
