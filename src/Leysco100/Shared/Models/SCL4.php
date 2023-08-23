<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class SCL4 extends Model
{
    use UsesTenantConnection;

    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 's_c_l4_s';

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'Object', 'ObjectID');
    }
}
