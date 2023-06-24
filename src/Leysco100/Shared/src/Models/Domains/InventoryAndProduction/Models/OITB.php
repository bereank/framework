<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use App\Models\ITB1;
use Illuminate\Database\Eloquent\Model;

class OITB extends Model
{
    //ITEM GROUPS
    protected $guarded = ['id'];
    protected $table = 'o_i_t_b_s';

    public function itb1()
    {
        return $this->hasMany(ITB1::class, 'ItmsGrpCod');
    }
}
