<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OEDG extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];

}
