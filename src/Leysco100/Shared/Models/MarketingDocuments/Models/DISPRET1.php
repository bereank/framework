<?php

namespace App\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Marketing\Models\ODISPRET;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\TaxGroup;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OITM;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OUOM;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\SRI1;

class DISPRET1 extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'd_i_s_p_r_e_t1_s';



    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function sri1()
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
}
