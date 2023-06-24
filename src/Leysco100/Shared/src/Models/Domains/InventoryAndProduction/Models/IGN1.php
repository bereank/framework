<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\InventoryAndProduction\Models\OITM;

class IGN1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'i_g_n1_s';

    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
}
