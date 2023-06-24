<?php

namespace App\Domains\HumanResourse\Models;

use Illuminate\Database\Eloquent\Model;

class OHEM extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_h_e_m_s';

    /*  public function department()
    {
    return $this->belongsTo(OUDP::class, 'Code', 'dept');
    } */

    protected $appends = array('full_name');
    public function getFullNameAttribute()
    {
        return "{$this->firstName} {$this->middleName}  {$this->lastName}";
    }
}
