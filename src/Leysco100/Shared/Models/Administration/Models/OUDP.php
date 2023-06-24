<?php

namespace App\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OUDP extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_u_d_p_s';
}
