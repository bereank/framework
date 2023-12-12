<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OCLG extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_c_l_g_s';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'id');
    }

    public function employees()
    {
        return $this->belongsTo(OSLP::class, 'SlpCode');
    }

    public function objectives()
    {
        return $this->hasMany(CallObjective::class, 'CallCode');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
}