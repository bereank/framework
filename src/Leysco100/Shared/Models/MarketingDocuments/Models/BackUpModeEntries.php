<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Leysco\LS100SharedPackage\Models\Domains\Shared\Models\APDI;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\GPMGate;

class BackUpModeEntries extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = [];
    protected $appends = array('status', 'release');


    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }
    public function ordr()
    {
        return $this->belongsTo(BackUpModeSetup::class, 'DocEntry', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'UserSign');
    }

    public function getReleaseAttribute()
    {
        if ($this->ReleaseStatus == 0) {
            return "Pending Release";
        }

        if ($this->ReleaseStatus == 1) {
            return "Released";
        }
        if ($this->ReleaseStatus == 2) {
            return "Document Flagged";
        }
    }
    public function getStatusAttribute()
    {
        if ($this->SyncStatus == 0) {
            return "Pending sync";
        }

        if ($this->SyncStatus == 1) {
            return "Synced Later";
        }
    }
    public function gates(): BelongsTo
    {
        return $this->belongsTo(GPMGate::class, 'GateID');
    }
}
