<?php

namespace App\Domains\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OINM extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_i_n_m_s';
}
