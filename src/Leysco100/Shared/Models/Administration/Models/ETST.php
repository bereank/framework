<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class ETST extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
}
