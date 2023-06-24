<?php

namespace Leysco\LS100SharedPackage\Models\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\Shared\Models\APDI;

class OGMS extends Model
{
    use HasFactory;

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
