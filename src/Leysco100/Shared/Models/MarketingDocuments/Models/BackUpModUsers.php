<?php

namespace Leysco100\Shared\Models\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class BackUpModUsers extends Model
{
    use HasFactory,UsesTenantConnection;
    protected $guarded = [];
    //  protected $fillable = ['id'];

}
