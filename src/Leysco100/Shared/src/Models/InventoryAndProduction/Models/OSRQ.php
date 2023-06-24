<?php

namespace App\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OSRQ extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_s_r_q_s';
}
