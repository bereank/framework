<?php

namespace Leysco100\Shared\Models\Gpm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class GMS2 extends Model
{

    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'g_m_s2_s';
}
