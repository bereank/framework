<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;

class Targets extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'targets';


    public function setup()
    {

        return $this->belongsTo(TargetSetup::class, 'target_setup_id', 'id');
    }
    public function metrics()
    {

        return $this->belongsTo(OUOM::class,  'UoM');
    }
}
