<?php

namespace App\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseType extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $table = 'warehouse_types';
}
