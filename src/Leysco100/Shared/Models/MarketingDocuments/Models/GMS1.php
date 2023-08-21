<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;


use Illuminate\Database\Eloquent\Model;

use Leysco100\Shared\Models\Shared\Models\APDI;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\Administration\Models\User;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Leysco100\Shared\Models\MarketingDocuments\Models\GPMGate;

class GMS1 extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'g_m_s1_s';

    protected $appends = array('state');

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function getStateAttribute()
    {
        if ($this->Status == 0) {
            return "Successfull";
        }

        if ($this->Status == 1) {
            return "Does Not Exist";
        }

        if ($this->Status == 2) {
            return "Duplicate";
        }
    }


    public function gates(): BelongsTo
    {
        return $this->belongsTo(GPMGate::class, 'GateID');
    }
}
