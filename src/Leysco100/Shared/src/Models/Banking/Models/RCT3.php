<?php

namespace App\Domains\Banking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RCT3 extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'r_c_t3_s';
}
