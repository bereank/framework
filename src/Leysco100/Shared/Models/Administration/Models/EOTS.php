<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class EOTS extends Model
{
    use HasFactory,UsesTenantConnection;
    //numbering series
    protected $guarded = ['id'];
    protected $table = 'e_o_t_s';
}
