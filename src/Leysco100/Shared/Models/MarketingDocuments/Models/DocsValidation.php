<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class DocsValidation extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];

    protected $table = 'docs_validation';

    // protected $casts = [
    //     'StartsWith' => 'array',
    //     'EndsWith' => 'array'
    // ];

    protected function StartsWith(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    } 

    protected function EndsWith(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    } 
}
