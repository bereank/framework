<?php


namespace Leysco100\Shared\Models\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\BusinessPartner\Models\OCQG;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class CQG1 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'c_q_g1_s';

    public function ocqg()
    {
        return $this->belongsTo(OCQG::class, 'GroupCode');
    }
}
