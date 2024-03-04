<?php

namespace Leysco100\Shared\Models\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;



class OTPP extends Model
{
    use HasFactory,UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_t_p_p_s';

    public function tpp1()
    {
        return $this->hasMany(TPP1::class, 'DocEntry');
    }
}
