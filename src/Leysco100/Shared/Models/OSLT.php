<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OSLT extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'o_s_l_t_s';

    public function creator()
    {
        return $this->belongsTo(User::class, 'Owner');
    }
}
