<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

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
