<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use App\Domains\Administration\Models\TaxGroup;
use Illuminate\Database\Eloquent\Model;

class WTQ1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'w_t_q1_s';
    public function oitm()
    {
        return $this->belongsTo(OITM::class, 'ItemCode');
    }
    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
}
