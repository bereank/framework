<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;

class CallObjective extends Model
{
    protected $guarded = ['id'];
    protected $table = 'call_objectives';

    public function Calls()
    {
        return $this->belongsTo(OCLG::class, 'CallCode');
    }
}