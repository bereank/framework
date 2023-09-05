<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class FormFieldsTemplate extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = [];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($model) {
    //         if ($model->DefaultTemplate !== null && static::where('DefaultTemplate', $model->DefaultTemplate)->exists()) {
    //             $model->DefaultTemplate = null;
    //         }
    //     });
    // }
}