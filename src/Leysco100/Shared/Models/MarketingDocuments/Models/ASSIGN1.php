<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ASSIGN1 extends Model
{
    use HasFactory, UsesTenantConnection;



    protected $guarded = ['id'];
    protected $table = 'a_s_s_i_g_n1_s';


    public function unitofmeasure()
    {
        return $this->belongsTo(OUOM::class, 'UomCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }


    public function ordr()
    {
        return $this->belongsTo(OASSIGN::class, 'DocEntry');
    }


    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }
}
