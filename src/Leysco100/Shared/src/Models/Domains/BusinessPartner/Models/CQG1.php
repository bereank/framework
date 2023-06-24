<?php
namespace Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\BusinessPartner\Models\OCQG;

class CQG1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'c_q_g1_s';

    public function ocqg()
    {
        return $this->belongsTo(OCQG::class, 'GroupCode');
    }
}
