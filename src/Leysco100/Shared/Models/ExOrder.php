<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExOrder extends Model
{
    protected $guarded = ['id'];
    protected $table = 'ex_orders';

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
        return $this->hasMany('App\ExOrderItems', 'DocEntry');
    }
}
