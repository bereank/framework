<?php

namespace App\Models;

use App\Models\TargetItems;
use App\Models\TargetSetup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OITM;

class Targets extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'targets';


    public function setup()
    {

        return $this->belongsTo(TargetSetup::class, 'target_setup_id', 'id');
    }
    public function metrics()
    {

        return $this->belongsTo('App\Domains\InventoryAndProduction\Models\OUOM',  'UoM');
    }
}
