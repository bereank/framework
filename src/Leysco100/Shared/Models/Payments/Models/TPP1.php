<?php

namespace Leysco100\Shared\Models\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;



class TPP1 extends Model
{
    use HasFactory,UsesTenantConnection;
    protected $guarded = ['id'];

    public function tpp2()
    {
        return $this->hasMany(TPP2::class,  'DocEntry');
    }
}
