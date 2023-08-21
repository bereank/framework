<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class UserDict extends Model
{
    use HasFactory,UsesTenantConnection;
    
    protected $guarded = ['id'];
    protected $table = 'user_dicts';
}
