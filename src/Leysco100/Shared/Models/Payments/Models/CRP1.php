<?php

namespace Leysco100\Shared\Models\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\MarketingDocuments\Models\INV1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class CRP1 extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'c_r_p1_s';

    public function ocrp()
    {
        return $this->belongsTo(OCRP::class);
    }

    public function invoice()
    {
        return $this->hasMany(INV1::class, 'DocEntry');
    }
}
