<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Marketing\Models;

use App\Domains\Administration\Models\TaxGroup;
use App\Domains\InventoryAndProduction\Models\OITM;
use App\Domains\InventoryAndProduction\Models\OUOM;
use App\Domains\InventoryAndProduction\Models\SRI1;
use App\Domains\Marketing\Models\ODRF;
use Illuminate\Database\Eloquent\Model;

class DRF1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'd_r_f1_s';

    public function odrf()
    {
        return $this->belongsTo(ODRF::class, 'DocEntry');
    }

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }

    public function sri1()
    {
        return $this->hasMany(SRI1::class, 'ItemCode', 'ItemCode');
    }
    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
}
