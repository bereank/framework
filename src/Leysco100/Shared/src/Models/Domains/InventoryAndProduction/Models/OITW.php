<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;

class OITW extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_i_t_w_s';



    public function ITM1()
    {
        return $this->hasMany('App\ITM1');
    }
}
