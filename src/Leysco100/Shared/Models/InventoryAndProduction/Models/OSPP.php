<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OSPP extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_s_p_p_s';
    protected $appends = ['src_price'];
   


    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode', 'ItemCode');
    }

    public function getSrcPriceAttribute()
    {
        switch ($this->attributes['SrcPrice']) {
            case 0:
                return "Pri. Curr.";
            case 1:
                return "Add. Curr. 1";
            case 2:
                return "Add. Curr. 2";
            default:
                return "Unknown";
        }
    }
}
