<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Marketing\Models\ODISPASS;
use App\Domains\Administration\Models\ORLP;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\TaxGroup;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OITM;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OUOM;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\SRI1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class DISPASS1 extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'd_i_s_p_a_s_s1_s';


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
        return $this->belongsTo(ODISPASS::class, 'DocEntry');
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
