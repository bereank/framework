<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OITL extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
}
