<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\Administration\Models\ETST;
use Leysco100\Shared\Models\Administration\Models\User;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ETS1 extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function setup()
    {
        return $this->belongsTo(ETST::class, 'DocEntry');
    }
}
