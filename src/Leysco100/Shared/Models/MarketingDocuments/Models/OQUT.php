<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Leysco100\Shared\Models\Shared\Models\APDI;

class OQUT extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_q_u_t_s';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }




    public function rows()
    {
        return $this->hasMany(QUT1::class, 'DocEntry');
    }


    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }
}
