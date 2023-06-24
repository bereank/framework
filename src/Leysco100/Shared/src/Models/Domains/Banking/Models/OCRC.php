<?php

namespace Leysco\LS100SharedPackage\Models\Domains\Banking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OCRC extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'o_c_r_c_s';
}
