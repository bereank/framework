<?php

namespace App\Domains\BusinessPartner\Models;

use App\Models\OOAT;
use Illuminate\Database\Eloquent\Model;
use App\Domains\InventoryAndProduction\Models\OITM;

class OAT1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_a_t1_s';

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }

    public function ooat()
    {
        return $this->belongsTo(OOAT::class, ' AgrNo');
    }
}
