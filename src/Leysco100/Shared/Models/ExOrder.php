<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ExOrder extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'ex_orders';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function items()
    {
        return $this->hasMany(ExOrderItems::class, 'DocEntry');
    }
}
