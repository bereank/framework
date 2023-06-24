<?php

namespace App\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ITM12 extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'i_t_m12_s';
}
