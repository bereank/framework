<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\OUQR;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OALT extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_a_l_t';


    public function alt1()
    {
        return $this->hasMany(ALT1::class, 'DocEntry');
    }

    public function alt2()
    {
        return $this->hasMany(ALT2::class, 'DocEntry');
    }
    public function alt4()
    {
        return $this->hasMany(ALT4::class, 'DocEntry');
    }
    public function alt3()
    {
        return $this->hasMany(ALT3::class, 'DocEntry');
    }
    public function alt5()
    {
        return $this->hasOne(ALT5::class, 'DocEntry');
    }
    public function alt6()
    {
        return $this->hasMany(ALT6::class, 'AlertId');
    }
    public function saved_query()
    {
        return $this->belongsTo(OUQR::class, 'QueryId');
    }
}
