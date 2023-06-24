<?php

namespace App\Domains\Banking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VPM1 extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'v_p_m1_s';
}
