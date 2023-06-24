<?php
namespace Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models;

use App\Domains\Marketing\Models\OPLN;
use Illuminate\Database\Eloquent\Model;

class OCRG extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_c_r_g_s';

    public function opln()
    {
        return $this->belongsTo(OPLN::class, 'PriceList');
    }
}
