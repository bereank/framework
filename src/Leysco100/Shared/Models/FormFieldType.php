<?php

namespace Leysco100\Shared\Models;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco\GatePassManagementModule\Models\FormFieldValue;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class FormFieldType extends Model
{
    use HasFactory,UsesTenantConnection;

    protected $guarded = [];
}
