<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;

class OWTR extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_w_t_q_s';

    public function outlet()
    {
        return $this->belongsTo('App\OCRD', 'CardCode');
    }

    public function CreatedBy()
    {
        return $this->belongsTo('App\User', 'UserSign');
    }

    public function items()
    {
        return $this->hasMany('App\RDR1', 'DocEntry');
    }

    public function wtr1()
    {
        return $this->hasMany('App\WTR1', 'DocEntry');
    }


    public function rows()
    {
        return $this->hasMany('App\WTR1', 'DocEntry');
    }
    public function objecttype()
    {
        return $this->belongsTo('App\APDI', 'ObjType', 'ObjectID');
    }
}
