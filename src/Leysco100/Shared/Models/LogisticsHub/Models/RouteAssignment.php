<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class RouteAssignment extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];

    public function route():BelongsTo
    {
        return $this->belongsTo(RoutePlanning::class,"RouteID","id");
    }

    public function oslp():BelongsTo
    {
        return $this->belongsTo(OSLP::class,"SlpCode","SlpCode");
    }

}
