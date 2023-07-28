<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Manufacture extends Model
{
    use SoftDeletes, UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'manufactures';
}
