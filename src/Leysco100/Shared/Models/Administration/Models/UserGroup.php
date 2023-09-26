<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class UserGroup extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'roles';
}
