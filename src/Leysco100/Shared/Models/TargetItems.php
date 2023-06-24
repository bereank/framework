<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OITM;

class TargetItems extends Model
{
    use HasFactory;

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
