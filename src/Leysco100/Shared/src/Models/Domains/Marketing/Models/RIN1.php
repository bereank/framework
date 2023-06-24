<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Marketing\Models;

use App\Domains\Marketing\Models\ORIN;
use Illuminate\Database\Eloquent\Model;
use App\Domains\Administration\Models\TaxGroup;
use App\Domains\InventoryAndProduction\Models\OITM;
use App\Domains\InventoryAndProduction\Models\OUOM;
use App\Domains\InventoryAndProduction\Models\SRI1;

class RIN1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'r_i_n1_s';

    public function ordr()
    {
        return $this->belongsTo(ORIN::class, 'DocEntry');
    }

    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function sri1()
    {
        return $this->belongsTo(SRI1::class, 'ItemCode', 'ItemCode');
    }
    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
}
