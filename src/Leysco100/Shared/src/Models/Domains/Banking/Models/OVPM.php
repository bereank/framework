<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Banking\Models;

use App\Domains\Banking\Models\VPM1;
use App\Domains\Banking\Models\VPM4;
use App\Domains\Marketing\Models\OPRQ;
use Illuminate\Database\Eloquent\Model;
use App\Domains\BusinessPartner\Models\OBPL;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OVPM extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_v_p_m_s';

    public function vpm4()
    {
        return $this->hasMany(VPM4::class, 'DocNum');
    }

    public function vpm1()
    {
        return $this->hasMany(VPM1::class, 'DocNum');
    }

    public function branch()
    {
        return $this->belongsTo(OBPL::class, 'BPLId');
    }

    public function transaction()
    {
        return $this->belongsTo(OPRQ::class, 'BaseEntry');
    }
}
