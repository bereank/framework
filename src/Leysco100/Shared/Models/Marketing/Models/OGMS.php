<?php

namespace Leysco100\Shared\Models\Marketing\Models;

use App\Domains\Shared\Models\APDI;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OGMS extends Model
{
    use HasFactory,UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_g_m_s';

    protected $appends = array('state', 'origin', 'doc_number');

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }

    public function getStateAttribute()
    {
        if ($this->Status == 0) {
            return "Open";
        }

        if ($this->Status == 1) {
            return "Released";
        }
    }
    public function getOriginAttribute()
    {
        if ($this->DocOrigin == 0) {
            return "SAP";
        }

        if ($this->DocOrigin == 1) {
            return "LS100";
        }
    }

    public function getDocNumberAttribute()
    {
        if ($this->DocOrigin == 0) {
            return $this->ExtRefDocNum;
        }

        if ($this->DocOrigin == 1) {
            return $this->DocNum;
        }
    }
}
