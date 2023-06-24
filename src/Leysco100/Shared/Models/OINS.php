<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OINS extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_i_n_s';
}
