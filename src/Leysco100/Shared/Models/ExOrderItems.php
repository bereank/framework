<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExOrderItems extends Model
{
    protected $guarded = ['id'];
    protected $table = 'ex_order_items';

    public function ordr()
    {
        return $this->belongsTo('App\ExOrder', 'DocEntry');
    }

    public function ItemDetails()
    {
        return $this->belongsTo('App\OITM', 'ItemCode');
    }
}
