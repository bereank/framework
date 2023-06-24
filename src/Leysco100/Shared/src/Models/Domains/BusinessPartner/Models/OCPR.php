<?php
namespace Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models\OCRD;

class OCPR extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_c_p_r_s';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }
}
