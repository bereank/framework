<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OFSC extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = "o_f_s_c_s";

    public function fsc1()
    {
        return $this->hasMany(FSC1::class, 'DocEntry');
    }
    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }
}
