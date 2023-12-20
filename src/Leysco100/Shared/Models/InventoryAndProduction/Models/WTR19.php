<?php


namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class WTR19 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
}
