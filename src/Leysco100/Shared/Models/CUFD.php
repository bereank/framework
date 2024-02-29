<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\Shared\Models\UFD1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class CUFD extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $table = 'c_u_f_d';
    protected $guarded = [];


    public function items()
    {
        return $this->hasMany(UFD1::class, 'FieldID');
    }

    public function objtype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }
}
