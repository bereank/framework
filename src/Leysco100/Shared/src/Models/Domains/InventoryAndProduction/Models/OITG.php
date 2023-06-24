<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use App\Domains\Administration\Models\ITG1;
use Illuminate\Database\Eloquent\Model;

class OITG extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_i_t_g_s';

    public function itg1()
    {
        return $this->hasMany(ITG1::class, 'ItmsTypCod');
    }
}
