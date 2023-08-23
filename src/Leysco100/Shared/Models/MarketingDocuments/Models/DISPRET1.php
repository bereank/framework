<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\InventoryAndProduction\Models\SRI1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\Administration\Models\ORLP;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\Administration\Models\Vehicle;
use Leysco100\Shared\Models\Administration\Models\TaxGroup;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Leysco100\Shared\Models\MarketingDocuments\Models\ODISPRET;

class DISPRET1 extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'd_i_s_p_r_e_t1_s';




    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function SerialNumbers()
    {
        return $this->hasMany(SRI1::class, 'LineNum', 'id');
    }
    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }
    public function ordr()
    {
        return $this->belongsTo(ODISPRET::class, 'DocEntry');
    }

    public function oslp()
    {
        return $this->belongsTo(OSLP::class, 'SlpCode', 'SlpCode');
    }
    public function driver()
    {
        return $this->belongsTo(ORLP::class, 'RlpCode', 'RlpCode');
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
