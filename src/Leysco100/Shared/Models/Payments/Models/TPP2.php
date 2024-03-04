<?php

namespace Leysco100\Shared\Models\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;



class TPP2 extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
}
