<?php

namespace Leysco100\Shared\Models\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class FTR100 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'f_t_r100_s';

    public function tabletabs()
    {
        return $this->belongsTo(FT100::class, 'TabID');
    }
}
