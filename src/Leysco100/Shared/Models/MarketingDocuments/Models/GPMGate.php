<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OLCT;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class GPMGate extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'gates';

    public function location(): BelongsTo
    {
        return $this->belongsTo(OLCT::class, 'location_id');
    }
}
