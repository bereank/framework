<?php

namespace Leysco\LS100SharedPackage\Models\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco\LS100SharedPackage\Models\Domains\Shared\Models\APDI;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\GPMGate;

class GMS1 extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'g_m_s1_s';

    protected $appends = array('state');

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }

    public function creator()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'UserSign');
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
