<?php

namespace App\Domains\Banking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OUBR extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_u_b_r_s';
}
