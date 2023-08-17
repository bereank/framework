<?php

namespace App\Domains\Marketing\Models;

use App\Domains\BusinessPartner\Models\OCRD;
use Illuminate\Database\Eloquent\Model;

class ORPC extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_r_p_c_s';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function inv1()
    {
        return $this->hasMany(INV1::class, 'DocEntry');
    }
    public function rows()
    {
        return $this->hasMany(INV1::class, 'DocEntry');
    }
    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }

    public function BusinessPartner()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
    }

    public function branch()
    {
        return $this->belongsTo(OBPL::class, 'BPLId');
    }
    public function oslp()
    {
        return $this->belongsTo(OSLP::class, 'SlpCode', 'SlpCode');
    }
}
