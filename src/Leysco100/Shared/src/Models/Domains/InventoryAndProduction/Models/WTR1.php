<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\InventoryAndProduction\Models\OITM;

class WTR1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'w_t_r1_s';
    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
}
