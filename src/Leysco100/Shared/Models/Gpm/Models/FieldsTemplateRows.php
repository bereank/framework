<?php

namespace Leysco100\Shared\Models\Gpm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class FieldsTemplateRows extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = [];
}
