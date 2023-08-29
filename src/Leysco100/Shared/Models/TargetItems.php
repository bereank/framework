<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OITM;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class TargetItems extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'target_items';



    public function oitm()
    {

        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function setup()
    {

        return $this->belongsTo(TargetSetup::class, 'target_setup_id', 'id');
    }
}
