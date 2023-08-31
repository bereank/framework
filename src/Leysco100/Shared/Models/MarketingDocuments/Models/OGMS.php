<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\MarketingDocuments\Models\GMS1;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OGMS extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_g_m_s';

    protected $appends = array('state', 'origin', 'doc_number');

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }

    public function scanlog(): BelongsTo
    {
        return $this->belongsTo(GMS1::class, 'ScanLogID');
    }
    public function scanlogs(): HasMany
    {

        return $this->hasMany(GMS1::class, 'id', 'ScanLogID');
    }

    public function getStateAttribute()
    {
        if ($this->Status == 0) {
            return "Open";
        }

        if ($this->Status == 1) {
            return "Scanned Not Released";
        }
        if ($this->Status == 2) {
            return "Scanned But flagged";
        }

        if ($this->Status == 3) {
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