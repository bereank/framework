<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class THRDP extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 't_h_r_d_p_s';
}
