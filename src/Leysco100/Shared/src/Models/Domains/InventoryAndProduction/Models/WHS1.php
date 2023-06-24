<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Finance\Models\ChartOfAccount;

class WHS1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'w_h_s1_s';

    public function glaccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'AcctCode');
    }
}
