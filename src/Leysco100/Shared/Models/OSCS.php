<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OSCS extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_s_c_s';
}
