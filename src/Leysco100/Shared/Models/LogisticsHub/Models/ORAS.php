<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ORAS extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];

    public function route(): BelongsTo
    {
        return $this->belongsTo(ORPS::class, "RouteID", "id");
    }

    public function oslp(): BelongsTo
    {
        return $this->belongsTo(OSLP::class, "SlpCode", "SlpCode");
    }
}
