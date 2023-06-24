<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OSCO extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_s_c_o_s';
}
