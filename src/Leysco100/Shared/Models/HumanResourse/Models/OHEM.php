<?php

namespace Leysco100\Shared\Models\HumanResourse\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\OUDP;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OHEM extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_h_e_m_s';

    public function department()
    {
    return $this->belongsTo(OUDP::class, 'Code', 'dept');
    } 

    protected $appends = array('full_name');
    public function getFullNameAttribute()
    {
        return "{$this->firstName} {$this->middleName}  {$this->lastName}";
    }
}
