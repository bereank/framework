<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GMS2 extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'g_m_s2_s';
}
