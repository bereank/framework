<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;


use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;

class DPI1 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'd_p_i1_s';
    public function odpi()
    {
        return $this->belongsTo('App\ODPI', 'DocEntry');
    }
    public function ItemDetails()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
}
