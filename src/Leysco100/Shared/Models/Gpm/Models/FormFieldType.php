<?php

namespace Leysco100\Shared\Models\Gpm\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class FormFieldType extends Model
{
    use HasFactory,UsesTenantConnection;

    protected $guarded = [];
}
