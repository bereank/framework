<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OILM extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(OITM::class, 'ItemCode');
    }

    public function business_partner()
    {
        return $this->hasMany(OCRD::class, 'BPCardCode');
    }
}
