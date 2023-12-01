<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class EDG1 extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
}
