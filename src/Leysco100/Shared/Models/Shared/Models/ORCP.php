<?php

namespace Leysco100\Shared\Models\Shared\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Shared\Models\ORCL;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\MarketingDocuments\Models\ODRF;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ORCP extends Model
{
    //o_r_c_p_s
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_r_c_p_s';

    public function odrf()
    {
        return $this->belongsTo(ODRF::class, 'DraftEntry');
    }
   // document_lines
    public function orcl()
    {
        return $this->hasOne(ORCL::class, 'RcpEntry');
    }

    
}
