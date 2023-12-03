<?php

namespace Leysco100\Shared\Models\Gpm\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class AutoBCModeSettings extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = [];
    protected $table = 'bcp_auto_settings';
}
