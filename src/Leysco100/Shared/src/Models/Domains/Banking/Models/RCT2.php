<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Banking\Models;

use App\Domains\Banking\Models\ORCT;
use App\Domains\Marketing\Models\OINV;
use Illuminate\Database\Eloquent\Model;

class RCT2 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'r_c_t2_s';

    public function invoice()
    {
        return $this->belongsTo(OINV::class, 'DocEntry');
    }

    public function orct()
    {
        return $this->belongsTo(ORCT::class, 'DocNum');
    }
}
