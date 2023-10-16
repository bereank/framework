<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\Administration\Models\User;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class EmailConfiguration extends Model
{
    use HasFactory, UsesTenantConnection;
    //numbering series
    protected $guarded = ['id'];
 
    public function creator()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
}
