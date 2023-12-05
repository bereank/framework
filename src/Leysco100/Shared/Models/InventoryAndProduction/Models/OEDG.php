<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OEDG extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];

    public function edg1()
    {
        return $this->hasMany(EDG1::class, 'DocEntry');
    }
}
