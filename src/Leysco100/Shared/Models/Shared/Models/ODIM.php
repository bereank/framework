<?php

namespace Leysco100\Shared\Models\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\SalesOportunities\Models\OOCR;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ODIM extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_d_i_m_s';

    public function oocr()
    {
        return $this->hasMany(OOCR::class, 'DimCode');
    }
}
