<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class CallObjective extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'call_objectives';

    public function Calls()
    {
        return $this->belongsTo(OCLG::class, 'CallCode');
    }
}