<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OASSIGN extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_a_s_s_i_g_n_s';

    public function rows()
    {
        return $this->hasMany(ASSIGN1::class, 'DocEntry');
    }

    public function ocrd()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
    }
}
