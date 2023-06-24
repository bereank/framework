<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use App\Domains\BusinessPartner\Models\OBPL;
use App\Domains\InventoryAndProduction\Models\WHS1;
use Illuminate\Database\Eloquent\Model;

class OWHS extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_w_h_s_s';
    public function whs1()
    {
        return $this->hasMany(WHS1::class, 'WhsCode');
    }

    public function location()
    {
        return $this->belongsTo(OBPL::class, 'BPLId', 'BPLId');
    }

    public function InventoryContents()
    {
        return $this->hasMany(WHS1::class, 'WhsCode');
    }
}
