<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use App\Domains\InventoryAndProduction\Models\OITM;
use Illuminate\Database\Eloquent\Model;

class OSRN extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_s_r_n_s';

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
}
