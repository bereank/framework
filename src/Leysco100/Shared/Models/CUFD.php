<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class CUFD extends Model
{
    use HasFactory,UsesTenantConnection;
    protected $table= 'c_u_f_d';
    protected $guarded = [];
}